<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * عرض قائمة الاشتراكات
     */
    public function index(Request $request): JsonResponse
    {
        $query = Subscription::with(['user', 'package']);

        // فلترة حسب الحالة
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // فلترة حسب الباقة
        if ($request->has('package_id') && $request->package_id !== 'all') {
            $query->where('package_id', $request->package_id);
        }

        // فلترة حسب تاريخ البداية
        if ($request->has('start_date')) {
            $query->whereDate('start_date', $request->start_date);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $subscriptions
        ]);
    }

    /**
     * عرض اشتراك محدد
     */
    public function show(Subscription $subscription): JsonResponse
    {
        $subscription->load(['user', 'package']);
        
        return response()->json([
            'success' => true,
            'data' => $subscription
        ]);
    }

    /**
     * إنشاء اشتراك جديد
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'package_id' => 'required|exists:packages,id',
            'start_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,pending,failed',
            'auto_renew' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        // التحقق من عدم وجود اشتراك نشط للمستخدم
        $activeSubscription = Subscription::where('user_id', $request->user_id)
            ->where('status', 'active')
            ->first();

        if ($activeSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم لديه اشتراك نشط بالفعل'
            ], 400);
        }

        $package = Package::findOrFail($request->package_id);
        $endDate = Carbon::parse($request->start_date)->addDays($package->duration_days);

        $subscription = Subscription::create([
            'user_id' => $request->user_id,
            'package_id' => $request->package_id,
            'start_date' => $request->start_date,
            'end_date' => $endDate,
            'amount_paid' => $request->amount_paid,
            'payment_status' => $request->payment_status,
            'auto_renew' => $request->auto_renew ?? false,
            'status' => $request->payment_status === 'paid' ? 'active' : 'pending',
            'notes' => $request->notes
        ]);

        $subscription->load(['user', 'package']);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الاشتراك بنجاح',
            'data' => $subscription
        ], 201);
    }

    /**
     * تحديث اشتراك
     */
    public function update(Request $request, Subscription $subscription): JsonResponse
    {
        $request->validate([
            'start_date' => 'sometimes|date',
            'amount_paid' => 'sometimes|numeric|min:0',
            'payment_status' => 'sometimes|in:paid,pending,failed',
            'auto_renew' => 'boolean',
            'status' => 'sometimes|in:active,expired,cancelled',
            'notes' => 'nullable|string'
        ]);

        $data = $request->only(['amount_paid', 'payment_status', 'auto_renew', 'status', 'notes']);

        // إذا تم تغيير تاريخ البداية، إعادة حساب تاريخ الانتهاء
        if ($request->has('start_date')) {
            $package = $subscription->package;
            $endDate = Carbon::parse($request->start_date)->addDays($package->duration_days);
            $data['start_date'] = $request->start_date;
            $data['end_date'] = $endDate;
        }

        $subscription->update($data);

        $subscription->load(['user', 'package']);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الاشتراك بنجاح',
            'data' => $subscription
        ]);
    }

    /**
     * حذف اشتراك
     */
    public function destroy(Subscription $subscription): JsonResponse
    {
        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الاشتراك بنجاح'
        ]);
    }

    /**
     * تجديد اشتراك
     */
    public function renew(Subscription $subscription): JsonResponse
    {
        if ($subscription->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تجديد اشتراك غير نشط'
            ], 400);
        }

        $package = $subscription->package;
        $newEndDate = Carbon::parse($subscription->end_date)->addDays($package->duration_days);

        $subscription->update([
            'end_date' => $newEndDate,
            'renewed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تجديد الاشتراك بنجاح',
            'data' => $subscription
        ]);
    }

    /**
     * إلغاء اشتراك
     */
    public function cancel(Subscription $subscription): JsonResponse
    {
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الاشتراك بنجاح',
            'data' => $subscription
        ]);
    }

    /**
     * إحصائيات الاشتراكات
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'expired_subscriptions' => Subscription::where('status', 'expired')->count(),
            'cancelled_subscriptions' => Subscription::where('status', 'cancelled')->count(),
            'total_revenue' => Subscription::sum('amount_paid'),
            'monthly_revenue' => Subscription::whereMonth('created_at', now()->month)->sum('amount_paid'),
            'auto_renew_count' => Subscription::where('auto_renew', true)->count(),
            'expiring_soon' => Subscription::where('status', 'active')
                ->where('end_date', '<=', Carbon::now()->addDays(7))
                ->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * الحصول على قائمة المستخدمين للفلتر
     */
    public function getUsers(): JsonResponse
    {
        $users = User::select('id', 'name', 'email')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * الحصول على قائمة الباقات للفلتر
     */
    public function getPackages(): JsonResponse
    {
        $packages = Package::select('id', 'name')->get();

        return response()->json([
            'success' => true,
            'data' => $packages
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\User;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class MembershipController extends Controller
{
    /**
     * عرض قائمة العضويات
     */
    public function index(Request $request): JsonResponse
    {
        $query = Membership::with(['user', 'features']);

        // فلترة حسب الحالة
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // فلترة حسب المستخدم
        if ($request->has('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        // فلترة حسب نوع العضوية
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $memberships = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $memberships
        ]);
    }

    /**
     * عرض عضوية محددة
     */
    public function show(Membership $membership): JsonResponse
    {
        $membership->load(['user', 'features']);
        
        return response()->json([
            'success' => true,
            'data' => $membership
        ]);
    }

    /**
     * إنشاء عضوية جديدة
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:basic,premium,enterprise',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,expired',
            'features' => 'array',
            'features.*' => 'exists:features,id',
            'notes' => 'nullable|string'
        ]);

        // التحقق من عدم وجود عضوية نشطة للمستخدم
        $activeMembership = Membership::where('user_id', $request->user_id)
            ->where('status', 'active')
            ->first();

        if ($activeMembership) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم لديه عضوية نشطة بالفعل'
            ], 400);
        }

        $membership = Membership::create($request->except('features'));
        
        if ($request->has('features')) {
            $membership->features()->attach($request->features);
        }

        $membership->load(['user', 'features']);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء العضوية بنجاح',
            'data' => $membership
        ], 201);
    }

    /**
     * تحديث عضوية
     */
    public function update(Request $request, Membership $membership): JsonResponse
    {
        $request->validate([
            'type' => 'sometimes|in:basic,premium,enterprise',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'price' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:active,inactive,expired',
            'features' => 'array',
            'features.*' => 'exists:features,id',
            'notes' => 'nullable|string'
        ]);

        $membership->update($request->except('features'));
        
        if ($request->has('features')) {
            $membership->features()->sync($request->features);
        }

        $membership->load(['user', 'features']);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث العضوية بنجاح',
            'data' => $membership
        ]);
    }

    /**
     * حذف عضوية
     */
    public function destroy(Membership $membership): JsonResponse
    {
        $membership->features()->detach();
        $membership->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف العضوية بنجاح'
        ]);
    }

    /**
     * تجديد عضوية
     */
    public function renew(Membership $membership): JsonResponse
    {
        if ($membership->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تجديد عضوية غير نشطة'
            ], 400);
        }

        $newEndDate = Carbon::parse($membership->end_date)->addYear();

        $membership->update([
            'end_date' => $newEndDate,
            'renewed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تجديد العضوية بنجاح',
            'data' => $membership
        ]);
    }

    /**
     * إلغاء عضوية
     */
    public function cancel(Membership $membership): JsonResponse
    {
        $membership->update([
            'status' => 'inactive',
            'cancelled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء العضوية بنجاح',
            'data' => $membership
        ]);
    }

    /**
     * إحصائيات العضويات
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_memberships' => Membership::count(),
            'active_memberships' => Membership::where('status', 'active')->count(),
            'inactive_memberships' => Membership::where('status', 'inactive')->count(),
            'expired_memberships' => Membership::where('status', 'expired')->count(),
            'basic_memberships' => Membership::where('type', 'basic')->count(),
            'premium_memberships' => Membership::where('type', 'premium')->count(),
            'enterprise_memberships' => Membership::where('type', 'enterprise')->count(),
            'total_revenue' => Membership::sum('price'),
            'monthly_revenue' => Membership::whereMonth('created_at', now()->month)->sum('price'),
            'expiring_soon' => Membership::where('status', 'active')
                ->where('end_date', '<=', Carbon::now()->addDays(30))
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
     * الحصول على قائمة الميزات
     */
    public function getFeatures(): JsonResponse
    {
        $features = Feature::select('id', 'name', 'description')->get();

        return response()->json([
            'success' => true,
            'data' => $features
        ]);
    }

    /**
     * تصدير العضويات
     */
    public function export(Request $request): JsonResponse
    {
        $query = Membership::with(['user', 'features']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $memberships = $query->get();

        $exportData = $memberships->map(function ($membership) {
            return [
                'id' => $membership->id,
                'user_name' => $membership->user->name,
                'user_email' => $membership->user->email,
                'type' => $membership->type,
                'start_date' => $membership->start_date,
                'end_date' => $membership->end_date,
                'price' => $membership->price,
                'status' => $membership->status,
                'features' => $membership->features->pluck('name')->implode(', '),
                'created_at' => $membership->created_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $exportData
        ]);
    }
}

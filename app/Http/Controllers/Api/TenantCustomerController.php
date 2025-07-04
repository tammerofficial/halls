<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TenantCustomerController extends Controller
{
    /**
     * عرض قائمة العملاء
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DB::table('customers')->where('is_active', true);

            // البحث
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            // الترتيب
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // الصفحات
            $perPage = $request->get('per_page', 15);
            $customers = $query->paginate($perPage);

            // إحصائيات
            $stats = [
                'total' => DB::table('customers')->where('is_active', true)->count(),
                'active' => DB::table('customers')->where('is_active', true)->count(),
                'inactive' => DB::table('customers')->where('is_active', false)->count(),
                'this_month' => DB::table('customers')
                    ->where('is_active', true)
                    ->whereMonth('created_at', now()->month)
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'customers' => $customers->items(),
                    'pagination' => [
                        'current_page' => $customers->currentPage(),
                        'last_page' => $customers->lastPage(),
                        'per_page' => $customers->perPage(),
                        'total' => $customers->total()
                    ],
                    'stats' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب العملاء: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض عميل واحد
     */
    public function show(int $id): JsonResponse
    {
        try {
            $customer = DB::table('customers')->find($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'العميل غير موجود'
                ], 404);
            }

            // جلب الحجوزات المرتبطة
            $bookings = DB::table('bookings')
                ->where('customer_id', $id)
                ->join('halls', 'bookings.hall_id', '=', 'halls.id')
                ->select('bookings.*', 'halls.name as hall_name')
                ->orderBy('bookings.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'customer' => $customer,
                    'bookings' => $bookings,
                    'total_bookings' => $bookings->count(),
                    'total_spent' => $bookings->sum('total_amount')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات العميل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إنشاء عميل جديد
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ], [
            'name.required' => 'اسم العميل مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customerId = DB::table('customers')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'notes' => $request->notes,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $customer = DB::table('customers')->find($customerId);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء العميل بنجاح',
                'data' => $customer
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء العميل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث بيانات العميل
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = DB::table('customers')->find($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'العميل غير موجود'
                ], 404);
            }

            DB::table('customers')->where('id', $id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'notes' => $request->notes,
                'is_active' => $request->get('is_active', $customer->is_active),
                'updated_at' => now()
            ]);

            $updatedCustomer = DB::table('customers')->find($id);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث بيانات العميل بنجاح',
                'data' => $updatedCustomer
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث العميل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف العميل
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $customer = DB::table('customers')->find($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'العميل غير موجود'
                ], 404);
            }

            // التحقق من وجود حجوزات مرتبطة
            $hasBookings = DB::table('bookings')->where('customer_id', $id)->exists();

            if ($hasBookings) {
                // إذا كان لديه حجوزات، نجعله غير نشط بدلاً من الحذف
                DB::table('customers')->where('id', $id)->update([
                    'is_active' => false,
                    'updated_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'تم إلغاء تفعيل العميل (لديه حجوزات مرتبطة)'
                ]);
            }

            // إذا لم يكن لديه حجوزات، احذفه نهائياً
            DB::table('customers')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف العميل بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف العميل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إحصائيات العملاء
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_customers' => DB::table('customers')->where('is_active', true)->count(),
                'new_this_month' => DB::table('customers')
                    ->where('is_active', true)
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                'new_this_week' => DB::table('customers')
                    ->where('is_active', true)
                    ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count(),
                'top_customers' => DB::table('customers')
                    ->join('bookings', 'customers.id', '=', 'bookings.customer_id')
                    ->select('customers.id', 'customers.name', 'customers.phone', 
                             DB::raw('COUNT(bookings.id) as bookings_count'),
                             DB::raw('SUM(bookings.total_amount) as total_spent'))
                    ->where('customers.is_active', true)
                    ->groupBy('customers.id', 'customers.name', 'customers.phone')
                    ->orderBy('total_spent', 'desc')
                    ->limit(5)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإحصائيات: ' . $e->getMessage()
            ], 500);
        }
    }
}

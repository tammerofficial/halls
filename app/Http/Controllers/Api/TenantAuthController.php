<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TenantAuthController extends Controller
{
    /**
     * تسجيل دخول المستأجر
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // الحصول على subdomain
        $subdomain = $this->getSubdomain($request);
        
        if (!$subdomain) {
            return response()->json([
                'success' => false,
                'message' => 'يجب الوصول عبر subdomain صحيح'
            ], 400);
        }

        // البحث عن المستأجر
        $tenant = Tenant::where('domain', $subdomain)->first();
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'المستأجر غير موجود'
            ], 404);
        }

        // التحقق من حالة المستأجر
        if ($tenant->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'حساب المستأجر غير نشط'
            ], 403);
        }

        // تبديل قاعدة البيانات
        $this->switchToTenantDatabase($tenant->db_name);

        // محاولة تسجيل الدخول
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // إنشاء token باستخدام Sanctum
            $token = $user->createToken('tenant-token', ['*'])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ],
                    'tenant' => [
                        'id' => $tenant->id,
                        'name' => $tenant->name,
                        'domain' => $tenant->domain,
                        'package' => $tenant->package->name
                    ],
                    'token' => $token
                ]
            ]);
        }

        // العودة لقاعدة البيانات الرئيسية
        $this->switchToMainDatabase();

        return response()->json([
            'success' => false,
            'message' => 'بيانات الدخول غير صحيحة'
        ], 401);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    /**
     * الحصول على بيانات المستخدم الحالي
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = session('tenant');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'tenant' => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'domain' => $tenant->domain,
                    'package' => $tenant->package->name,
                    'features' => $this->getTenantFeatures($tenant)
                ]
            ]
        ]);
    }

    /**
     * الحصول على subdomain من الطلب
     */
    private function getSubdomain(Request $request): ?string
    {
        $host = $request->getHost();
        
        // تجاهل localhost و IP addresses
        if (in_array($host, ['localhost', '127.0.0.1', '::1']) || filter_var($host, FILTER_VALIDATE_IP)) {
            return null;
        }

        // استخراج subdomain
        $parts = explode('.', $host);
        
        if (count($parts) >= 3) {
            return $parts[0];
        }

        return null;
    }

    /**
     * تبديل قاعدة البيانات للمستأجر
     */
    private function switchToTenantDatabase(string $databaseName): void
    {
        // إغلاق الاتصال الحالي
        DB::purge('mysql');
        
        // تحديث إعدادات قاعدة البيانات
        config([
            'database.connections.mysql.database' => $databaseName,
        ]);
        
        // إعادة الاتصال بقاعدة البيانات الجديدة
        DB::reconnect('mysql');
    }

    /**
     * العودة لقاعدة البيانات الرئيسية
     */
    private function switchToMainDatabase(): void
    {
        // إغلاق الاتصال الحالي
        DB::purge('mysql');
        
        // العودة لقاعدة البيانات الرئيسية
        config([
            'database.connections.mysql.database' => config('database.connections.mysql.database'),
        ]);
        
        // إعادة الاتصال
        DB::reconnect('mysql');
    }

    /**
     * الحصول على ميزات المستأجر
     */
    private function getTenantFeatures(Tenant $tenant): array
    {
        $features = json_decode($tenant->package->features, true);
        $limits = json_decode($tenant->package->limits, true);

        return [
            'features' => $features,
            'limits' => $limits,
            'can_create_halls' => $tenant->canCreateHalls(),
            'can_create_bookings' => $tenant->canCreateBookings(),
            'has_invoices' => $tenant->hasFeature('invoices'),
            'has_reports' => $tenant->hasFeature('reports'),
            'has_custom_branding' => $tenant->hasFeature('custom_branding'),
            'has_api_access' => $tenant->hasFeature('api_access'),
            'has_multiple_halls' => $tenant->hasFeature('multiple_halls'),
        ];
    }
}

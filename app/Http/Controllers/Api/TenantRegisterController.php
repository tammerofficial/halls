<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TenantRegisterController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * تسجيل مستأجر جديد
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'project' => 'required|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
            'package_id' => 'required|exists:packages,id',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'project.regex' => 'اسم المشروع يجب أن يحتوي على أحرف إنجليزية وأرقام وشرطة سفلية فقط',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'package_id.exists' => 'الباقة المختارة غير موجودة'
        ]);

        try {
            $tenant = $this->tenantService->createTenant($request->all());

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل المستأجر بنجاح',
                'data' => [
                    'tenant_id' => $tenant->id,
                    'domain' => $tenant->domain,
                    'database_name' => $tenant->db_name,
                    'package_name' => $tenant->package->name,
                    'trial_ends_at' => $tenant->trial_ends_at,
                    'login_url' => 'http://' . $tenant->domain . '/auth/login'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل المستأجر: ' . $e->getMessage()
            ], 500);
        }
    }
} 
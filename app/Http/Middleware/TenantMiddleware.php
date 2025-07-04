<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // تجاهل الطلبات للصفحة الرئيسية
        if ($request->is('/') || $request->is('login') || $request->is('register*') || $request->is('auth/*')) {
            return $next($request);
        }

        // الحصول على subdomain
        $subdomain = $this->getSubdomain($request);
        
        if (!$subdomain) {
            return $next($request);
        }

        // البحث عن المستأجر
        $tenant = Tenant::where('domain', $subdomain)->first();
        
        if (!$tenant) {
            abort(404, 'المستأجر غير موجود');
        }

        // التحقق من حالة المستأجر
        if ($tenant->status !== 'active') {
            abort(403, 'حساب المستأجر غير نشط');
        }

        // حفظ بيانات المستأجر في الجلسة
        session(['tenant' => $tenant]);
        
        // تبديل قاعدة البيانات
        $this->switchToTenantDatabase($tenant->db_name);

        return $next($request);
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
}

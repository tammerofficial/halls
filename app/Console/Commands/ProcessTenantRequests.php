<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessTenantRequests extends Command
{
    protected $signature = 'tenants:process-requests';
    protected $description = 'Process pending tenant database creation requests';

    public function handle()
    {
        $requests = DB::table('tenant_requests')->where('status', 'pending')->get();
        
        if ($requests->isEmpty()) {
            $this->info('لا توجد طلبات معلقة لإنشاء قواعد البيانات');
            return;
        }
        
        foreach ($requests as $req) {
            try {
                // إنشاء قاعدة البيانات إذا لم تكن موجودة
                DB::statement("CREATE DATABASE IF NOT EXISTS `{$req->db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                // تحديث حالة الطلب إلى مكتمل
                DB::table('tenant_requests')->where('id', $req->id)->update([
                    'status' => 'completed', 
                    'updated_at' => now()
                ]);
                
                $this->info("✅ تم إنشاء قاعدة البيانات: {$req->db_name}");
                
            } catch (\Exception $e) {
                // تحديث حالة الطلب إلى فشل
                DB::table('tenant_requests')->where('id', $req->id)->update([
                    'status' => 'failed', 
                    'updated_at' => now()
                ]);
                
                $this->error("❌ فشل في إنشاء قاعدة البيانات: {$req->db_name} - " . $e->getMessage());
            }
        }
        
        $this->info('تم معالجة جميع الطلبات المعلقة');
    }
} 
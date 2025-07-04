<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * إنشاء مستأجر جديد
     */
    public function createTenant(array $data): Tenant
    {
        $databaseName = 'hall_' . $data['project'];
        
        // التحقق من عدم وجود قاعدة بيانات بنفس الاسم
        if (Schema::hasTable($databaseName)) {
            throw new \Exception('اسم المشروع مستخدم بالفعل');
        }

        // إنشاء قاعدة البيانات الجديدة
        $this->createTenantDatabase($databaseName);

        // إنشاء المستخدم الرئيسي للمستأجر
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
            'is_active' => true
        ]);

        // الحصول على الباقة المختارة
        $package = Package::findOrFail($data['package_id']);

        // إنشاء المستأجر
        $tenant = Tenant::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'domain' => $data['project'] . '.halls.com',
            'db_name' => $databaseName,
            'package_id' => $package->id,
            'status' => 'active',
            'trial_ends_at' => now()->addDays(30) // تجربة مجانية لمدة 30 يوم
        ]);

        // إنشاء اشتراك
        DB::table('subscriptions')->insert([
            'tenant_id' => $tenant->id,
            'package_id' => $package->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'status' => 'active',
            'amount_paid' => $package->price_monthly,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // إنشاء جداول المستأجر
        $this->createTenantTables($databaseName);

        // إنشاء مستخدم افتراضي في قاعدة بيانات المستأجر
        $this->createTenantAdmin($databaseName, $data);

        return $tenant;
    }

    /**
     * إنشاء قاعدة بيانات المستأجر
     */
    private function createTenantDatabase(string $databaseName): void
    {
        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\Exception $e) {
            throw new \Exception("فشل في إنشاء قاعدة البيانات: " . $e->getMessage());
        }
    }

    /**
     * إنشاء جداول المستأجر
     */
    private function createTenantTables(string $databaseName): void
    {
        try {
            // استخدام قاعدة البيانات الجديدة
            DB::statement("USE `{$databaseName}`");

            // إنشاء جدول المستخدمين
            Schema::create('users', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('password');
                $table->enum('role', ['admin', 'staff', 'user'])->default('user');
                $table->boolean('is_active')->default(true);
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });

            // إنشاء جدول القاعات
            Schema::create('halls', function ($table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('capacity');
                $table->decimal('price_per_hour', 8, 2);
                $table->string('location')->nullable();
                $table->json('amenities')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // إنشاء جدول الخدمات
            Schema::create('services', function ($table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 8, 2);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // إنشاء جدول العملاء
            Schema::create('customers', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone');
                $table->text('address')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // إنشاء جدول الحجوزات
            Schema::create('bookings', function ($table) {
                $table->id();
                $table->foreignId('hall_id')->constrained()->onDelete('cascade');
                $table->foreignId('customer_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->dateTime('start_time');
                $table->dateTime('end_time');
                $table->decimal('total_amount', 10, 2);
                $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();
            });

            // إنشاء جدول الفواتير
            Schema::create('invoices', function ($table) {
                $table->id();
                $table->foreignId('booking_id')->constrained()->onDelete('cascade');
                $table->string('invoice_number')->unique();
                $table->decimal('amount', 10, 2);
                $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
                $table->date('due_date');
                $table->date('paid_date')->nullable();
                $table->timestamps();
            });

            // العودة لقاعدة البيانات الرئيسية
            DB::statement("USE " . config('database.connections.mysql.database'));

        } catch (\Exception $e) {
            // العودة لقاعدة البيانات الرئيسية في حالة الخطأ
            try {
                DB::statement("USE " . config('database.connections.mysql.database'));
            } catch (\Exception $e2) {
                // تجاهل خطأ العودة
            }
            throw new \Exception("فشل في إنشاء جداول قاعدة البيانات: " . $e->getMessage());
        }
    }

    /**
     * إنشاء مدير افتراضي في قاعدة بيانات المستأجر
     */
    private function createTenantAdmin(string $databaseName, array $data): void
    {
        try {
            DB::statement("USE `{$databaseName}`");
            
            // إضافة المستخدم إلى جدول users
            DB::table('users')->insert([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // إضافة المستخدم إلى جدول العملاء إذا لم يكن موجوداً
            if (!DB::table('customers')->where('email', $data['email'])->exists()) {
                DB::table('customers')->insert([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'address' => $data['address'] ?? '',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // العودة لقاعدة البيانات الرئيسية
            DB::statement("USE " . config('database.connections.mysql.database'));
        } catch (\Exception $e) {
            // العودة لقاعدة البيانات الرئيسية في حالة الخطأ
            try {
                DB::statement("USE " . config('database.connections.mysql.database'));
            } catch (\Exception $e2) {
                // تجاهل خطأ العودة
            }
        }
    }

    /**
     * حذف مستأجر
     */
    public function deleteTenant(Tenant $tenant): bool
    {
        try {
            // حذف قاعدة البيانات
            DB::statement("DROP DATABASE IF EXISTS `{$tenant->db_name}`");
            
            // حذف المستأجر من قاعدة البيانات الرئيسية
            $tenant->delete();
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * تجديد اشتراك المستأجر
     */
    public function renewSubscription(Tenant $tenant, int $months = 1): bool
    {
        try {
            $subscription = $tenant->activeSubscription;
            
            if (!$subscription) {
                return false;
            }

            $subscription->update([
                'end_date' => $subscription->end_date->addMonths($months),
                'status' => 'active'
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

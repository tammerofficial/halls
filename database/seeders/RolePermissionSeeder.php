<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الصلاحيات الأساسية
        $permissions = [
            // إدارة القاعات
            ['name' => 'halls.view', 'display_name' => 'عرض القاعات', 'module' => 'halls', 'action' => 'view'],
            ['name' => 'halls.create', 'display_name' => 'إنشاء قاعة', 'module' => 'halls', 'action' => 'create'],
            ['name' => 'halls.edit', 'display_name' => 'تعديل القاعات', 'module' => 'halls', 'action' => 'edit'],
            ['name' => 'halls.delete', 'display_name' => 'حذف القاعات', 'module' => 'halls', 'action' => 'delete'],
            
            // إدارة الحجوزات
            ['name' => 'bookings.view', 'display_name' => 'عرض الحجوزات', 'module' => 'bookings', 'action' => 'view'],
            ['name' => 'bookings.create', 'display_name' => 'إنشاء حجز', 'module' => 'bookings', 'action' => 'create'],
            ['name' => 'bookings.edit', 'display_name' => 'تعديل الحجوزات', 'module' => 'bookings', 'action' => 'edit'],
            ['name' => 'bookings.delete', 'display_name' => 'حذف الحجوزات', 'module' => 'bookings', 'action' => 'delete'],
            
            // إدارة العملاء
            ['name' => 'customers.view', 'display_name' => 'عرض العملاء', 'module' => 'customers', 'action' => 'view'],
            ['name' => 'customers.create', 'display_name' => 'إنشاء عميل', 'module' => 'customers', 'action' => 'create'],
            ['name' => 'customers.edit', 'display_name' => 'تعديل العملاء', 'module' => 'customers', 'action' => 'edit'],
            ['name' => 'customers.delete', 'display_name' => 'حذف العملاء', 'module' => 'customers', 'action' => 'delete'],
            
            // إدارة الخدمات
            ['name' => 'services.view', 'display_name' => 'عرض الخدمات', 'module' => 'services', 'action' => 'view'],
            ['name' => 'services.create', 'display_name' => 'إنشاء خدمة', 'module' => 'services', 'action' => 'create'],
            ['name' => 'services.edit', 'display_name' => 'تعديل الخدمات', 'module' => 'services', 'action' => 'edit'],
            ['name' => 'services.delete', 'display_name' => 'حذف الخدمات', 'module' => 'services', 'action' => 'delete'],
            
            // إدارة الفواتير
            ['name' => 'invoices.view', 'display_name' => 'عرض الفواتير', 'module' => 'invoices', 'action' => 'view'],
            ['name' => 'invoices.create', 'display_name' => 'إنشاء فاتورة', 'module' => 'invoices', 'action' => 'create'],
            ['name' => 'invoices.edit', 'display_name' => 'تعديل الفواتير', 'module' => 'invoices', 'action' => 'edit'],
            ['name' => 'invoices.delete', 'display_name' => 'حذف الفواتير', 'module' => 'invoices', 'action' => 'delete'],
            
            // إدارة التقارير
            ['name' => 'reports.view', 'display_name' => 'عرض التقارير', 'module' => 'reports', 'action' => 'view'],
            ['name' => 'reports.export', 'display_name' => 'تصدير التقارير', 'module' => 'reports', 'action' => 'export'],
            
            // إدارة النظام
            ['name' => 'system.settings', 'display_name' => 'إعدادات النظام', 'module' => 'system', 'action' => 'settings'],
            ['name' => 'system.users', 'display_name' => 'إدارة المستخدمين', 'module' => 'system', 'action' => 'users'],
            ['name' => 'system.roles', 'display_name' => 'إدارة الأدوار', 'module' => 'system', 'action' => 'roles'],
            ['name' => 'system.packages', 'display_name' => 'إدارة الباقات', 'module' => 'system', 'action' => 'packages'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // إنشاء الأدوار الأساسية
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'مدير النظام',
                'description' => 'مدير النظام مع جميع الصلاحيات',
                'is_system' => true,
                'permissions' => Permission::all()->pluck('name')->toArray()
            ],
            [
                'name' => 'admin',
                'display_name' => 'مدير',
                'description' => 'مدير مع صلاحيات محدودة',
                'is_system' => true,
                'permissions' => [
                    'halls.view', 'halls.create', 'halls.edit',
                    'bookings.view', 'bookings.create', 'bookings.edit',
                    'customers.view', 'customers.create', 'customers.edit',
                    'services.view', 'services.create', 'services.edit',
                    'invoices.view', 'invoices.create', 'invoices.edit',
                    'reports.view'
                ]
            ],
            [
                'name' => 'manager',
                'display_name' => 'مدير قاعات',
                'description' => 'مدير القاعات والحجوزات',
                'is_system' => true,
                'permissions' => [
                    'halls.view', 'halls.create', 'halls.edit',
                    'bookings.view', 'bookings.create', 'bookings.edit',
                    'customers.view', 'customers.create', 'customers.edit',
                    'reports.view'
                ]
            ],
            [
                'name' => 'staff',
                'display_name' => 'موظف',
                'description' => 'موظف عادي',
                'is_system' => true,
                'permissions' => [
                    'halls.view',
                    'bookings.view', 'bookings.create',
                    'customers.view', 'customers.create'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);
            
            $role = Role::create($roleData);
            
            foreach ($permissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission) {
                    $role->permissions()->attach($permission->id);
                }
            }
        }
    }
}

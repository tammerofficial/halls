<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            // الميزات الأساسية
            [
                'name' => 'basic_halls',
                'display_name' => 'إدارة القاعات الأساسية',
                'description' => 'إمكانية إدارة قاعات أساسية',
                'category' => 'basic',
                'default_settings' => ['max_halls' => 3]
            ],
            [
                'name' => 'basic_bookings',
                'display_name' => 'إدارة الحجوزات الأساسية',
                'description' => 'إمكانية إدارة الحجوزات الأساسية',
                'category' => 'basic',
                'default_settings' => ['max_bookings_per_month' => 50]
            ],
            [
                'name' => 'customer_management',
                'display_name' => 'إدارة العملاء',
                'description' => 'إمكانية إدارة قاعدة بيانات العملاء',
                'category' => 'basic',
                'default_settings' => ['max_customers' => 100]
            ],
            
            // الميزات المتقدمة
            [
                'name' => 'advanced_analytics',
                'display_name' => 'التحليلات المتقدمة',
                'description' => 'تقارير وتحليلات متقدمة',
                'category' => 'advanced',
                'default_settings' => ['reports_limit' => 10]
            ],
            [
                'name' => 'email_notifications',
                'display_name' => 'إشعارات البريد الإلكتروني',
                'description' => 'إرسال إشعارات تلقائية عبر البريد الإلكتروني',
                'category' => 'advanced',
                'default_settings' => ['daily_limit' => 100]
            ],
            [
                'name' => 'sms_notifications',
                'display_name' => 'إشعارات الرسائل النصية',
                'description' => 'إرسال إشعارات عبر الرسائل النصية',
                'category' => 'advanced',
                'default_settings' => ['daily_limit' => 50]
            ],
            [
                'name' => 'calendar_integration',
                'display_name' => 'تكامل التقويم',
                'description' => 'تكامل مع تقويم Google و Outlook',
                'category' => 'advanced',
                'default_settings' => ['sync_interval' => '15min']
            ],
            
            // الميزات المميزة
            [
                'name' => 'custom_branding',
                'display_name' => 'التخصيص التجاري',
                'description' => 'إمكانية تخصيص الشعار والألوان',
                'category' => 'premium',
                'default_settings' => ['logo_upload' => true, 'color_scheme' => true]
            ],
            [
                'name' => 'priority_support',
                'display_name' => 'الدعم المميز',
                'description' => 'دعم فني مميز مع أولوية الاستجابة',
                'category' => 'premium',
                'default_settings' => ['response_time' => '2hours']
            ],
            [
                'name' => 'api_access',
                'display_name' => 'واجهة برمجة التطبيقات',
                'description' => 'إمكانية الوصول إلى API للتكامل مع أنظمة أخرى',
                'category' => 'premium',
                'default_settings' => ['rate_limit' => 1000]
            ],
            [
                'name' => 'white_label',
                'display_name' => 'العلامة البيضاء',
                'description' => 'إزالة جميع علامات النظام التجارية',
                'category' => 'premium',
                'default_settings' => ['remove_branding' => true]
            ],
            [
                'name' => 'multi_location',
                'display_name' => 'إدارة المواقع المتعددة',
                'description' => 'إدارة قاعات في مواقع متعددة',
                'category' => 'premium',
                'default_settings' => ['max_locations' => 5]
            ],
            [
                'name' => 'advanced_reporting',
                'display_name' => 'التقارير المتقدمة',
                'description' => 'تقارير مخصصة ومتقدمة',
                'category' => 'premium',
                'default_settings' => ['custom_reports' => true, 'export_formats' => ['pdf', 'excel', 'csv']]
            ]
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }
    }
}

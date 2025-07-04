<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'خدمة التصوير الاحترافي',
                'description' => 'تصوير احترافي للمناسبات بأحدث المعدات',
                'price' => 1500,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة الضيافة الفاخرة',
                'description' => 'بوفيه مفتوح مع خدمة النادل',
                'price' => 150,
                'is_active' => 1,
            ],
            [
                'name' => 'تزيين القاعة',
                'description' => 'تزيين احترافي حسب المناسبة',
                'price' => 2000,
                'is_active' => 1,
            ],
            [
                'name' => 'DJ وموسيقى',
                'description' => 'خدمة DJ محترف مع معدات صوتية',
                'price' => 500,
                'is_active' => 1,
            ],
            [
                'name' => 'شاشات عرض',
                'description' => 'شاشات LED كبيرة للعروض',
                'price' => 800,
                'is_active' => 0,
            ],
            [
                'name' => 'خدمة الترجمة الفورية',
                'description' => 'ترجمة فورية لجميع اللغات أثناء الفعاليات',
                'price' => 1200,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة الأمن والحراسة',
                'description' => 'توفير حراس أمن محترفين للفعاليات',
                'price' => 900,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة الإنترنت السريع',
                'description' => 'توفير اتصال إنترنت عالي السرعة للضيوف',
                'price' => 300,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة الإضاءة الذكية',
                'description' => 'إضاءة ذكية قابلة للتخصيص حسب الحدث',
                'price' => 700,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة الضيافة العربية',
                'description' => 'تقديم القهوة العربية والتمور للضيوف',
                'price' => 250,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة البوفيه المفتوح',
                'description' => 'بوفيه عالمي متنوع لجميع الأذواق',
                'price' => 350,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة تزيين الطاولات',
                'description' => 'تزيين الطاولات بزهور طبيعية وديكورات عصرية',
                'price' => 600,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة التصوير بالفيديو',
                'description' => 'تصوير فيديو احترافي وتحرير المقاطع',
                'price' => 1800,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة البث المباشر',
                'description' => 'بث مباشر للفعاليات عبر الإنترنت',
                'price' => 2200,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة التكييف المتنقل',
                'description' => 'توفير أجهزة تكييف متنقلة للفعاليات الخارجية',
                'price' => 400,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة الاستقبال والترحيب',
                'description' => 'فريق استقبال محترف للضيوف',
                'price' => 350,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة تنظيم المواقف',
                'description' => 'تنظيم مواقف السيارات للضيوف',
                'price' => 200,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة تنظيف القاعة',
                'description' => 'تنظيف القاعة قبل وبعد الفعالية',
                'price' => 350,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة تجهيز الهدايا',
                'description' => 'تجهيز وتغليف هدايا الضيوف',
                'price' => 500,
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة الطباعة الفورية',
                'description' => 'طباعة صور الضيوف فورًا خلال الفعالية',
                'price' => 600,
                'is_active' => 1,
            ],
        ];

        foreach ($services as $service) {
            DB::table('services')->insert(array_merge($service, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

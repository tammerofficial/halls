<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'أحمد العلي',
                'email' => 'ahmed.ali@email.com',
                'phone' => '0501234567',
                'address' => 'الرياض - شارع الملك فهد',
                'national_id' => '1234567890',
            ],
            [
                'name' => 'سارة محمد',
                'email' => 'sara.mohamed@email.com',
                'phone' => '0502345678',
                'address' => 'جدة - شارع التحلية',
                'national_id' => '1234567891',
            ],
            [
                'name' => 'خالد الدوسري',
                'email' => 'khaled.dosari@email.com',
                'phone' => '0503456789',
                'address' => 'الدمام - شارع الملك خالد',
                'national_id' => '1234567892',
            ],
            [
                'name' => 'مؤسسة النجاح',
                'email' => 'info@success.com',
                'phone' => '0551234567',
                'address' => 'مكة - شارع العزيزية',
                'national_id' => '1234567893',
            ],
            [
                'name' => 'ليلى الحربي',
                'email' => 'layla.harbi@email.com',
                'phone' => '0552345678',
                'address' => 'المدينة - شارع الملك عبدالله',
                'national_id' => '1234567894',
            ],
            [
                'name' => 'شركة الرؤية',
                'email' => 'contact@vision.com',
                'phone' => '0553456789',
                'address' => 'الرياض - شارع العليا',
                'national_id' => '1234567895',
            ],
            [
                'name' => 'عبدالله الغامدي',
                'email' => 'abdullah.ghamdi@email.com',
                'phone' => '0561234567',
                'address' => 'جدة - شارع فلسطين',
                'national_id' => '1234567896',
            ],
            [
                'name' => 'مها السبيعي',
                'email' => 'maha.subaie@email.com',
                'phone' => '0562345678',
                'address' => 'الدمام - شارع الملك عبدالعزيز',
                'national_id' => '1234567897',
            ],
            [
                'name' => 'شركة الإبداع',
                'email' => 'info@creativity.com',
                'phone' => '0563456789',
                'address' => 'الرياض - شارع الملك عبدالله',
                'national_id' => '1234567898',
            ],
            [
                'name' => 'سلمان العتيبي',
                'email' => 'salman.otaibi@email.com',
                'phone' => '0571234567',
                'address' => 'مكة - شارع إبراهيم الخليل',
                'national_id' => '1234567899',
            ],
            [
                'name' => 'شركة التميز',
                'email' => 'info@tamayoz.com',
                'phone' => '0572345678',
                'address' => 'جدة - شارع التحلية',
                'national_id' => '1234567900',
            ],
            [
                'name' => 'ريم الزهراني',
                'email' => 'reem.zahrani@email.com',
                'phone' => '0573456789',
                'address' => 'الدمام - شارع الملك خالد',
                'national_id' => '1234567901',
            ],
            [
                'name' => 'شركة المستقبل',
                'email' => 'info@future.com',
                'phone' => '0581234567',
                'address' => 'الرياض - شارع الملك فهد',
                'national_id' => '1234567902',
            ],
            [
                'name' => 'ناصر الشهري',
                'email' => 'nasser.shahri@email.com',
                'phone' => '0582345678',
                'address' => 'مكة - شارع العزيزية',
                'national_id' => '1234567903',
            ],
            [
                'name' => 'شركة الريادة',
                'email' => 'info@leadership.com',
                'phone' => '0583456789',
                'address' => 'جدة - شارع فلسطين',
                'national_id' => '1234567904',
            ],
            [
                'name' => 'هدى القحطاني',
                'email' => 'huda.qahtani@email.com',
                'phone' => '0591234567',
                'address' => 'الرياض - شارع العليا',
                'national_id' => '1234567905',
            ],
            [
                'name' => 'شركة النجاح',
                'email' => 'info@success2.com',
                'phone' => '0592345678',
                'address' => 'الدمام - شارع الملك عبدالعزيز',
                'national_id' => '1234567906',
            ],
            [
                'name' => 'محمد السالم',
                'email' => 'mohammed.salem@email.com',
                'phone' => '0593456789',
                'address' => 'مكة - شارع إبراهيم الخليل',
                'national_id' => '1234567907',
            ],
            [
                'name' => 'شركة التقدم',
                'email' => 'info@progress.com',
                'phone' => '0509876543',
                'address' => 'الرياض - شارع الملك عبدالله',
                'national_id' => '1234567908',
            ],
            [
                'name' => 'سعد المطيري',
                'email' => 'saad.mutairi@email.com',
                'phone' => '0512345678',
                'address' => 'جدة - شارع التحلية',
                'national_id' => '1234567909',
            ],
        ];

        foreach ($customers as $customer) {
            DB::table('customers')->insert(array_merge($customer, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

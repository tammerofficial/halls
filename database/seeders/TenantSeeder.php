<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'قاعات الرياض الفاخرة',
                'email' => 'info@riyadh-halls.com',
                'domain' => 'riyadh-halls',
                'db_name' => 'riyadh_halls_db',
                'package_id' => 1,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات جدة المتميزة',
                'email' => 'contact@jeddah-halls.com',
                'domain' => 'jeddah-halls',
                'db_name' => 'jeddah_halls_db',
                'package_id' => 2,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات الدمام الحديثة',
                'email' => 'info@dammam-halls.com',
                'domain' => 'dammam-halls',
                'db_name' => 'dammam_halls_db',
                'package_id' => 3,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات مكة المكرمة',
                'email' => 'contact@makkah-halls.com',
                'domain' => 'makkah-halls',
                'db_name' => 'makkah_halls_db',
                'package_id' => 4,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات المدينة المنورة',
                'email' => 'info@madinah-halls.com',
                'domain' => 'madinah-halls',
                'db_name' => 'madinah_halls_db',
                'package_id' => 5,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات الخبر الفاخرة',
                'email' => 'contact@khobar-halls.com',
                'domain' => 'khobar-halls',
                'db_name' => 'khobar_halls_db',
                'package_id' => 6,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات تبوك المتميزة',
                'email' => 'info@tabuk-halls.com',
                'domain' => 'tabuk-halls',
                'db_name' => 'tabuk_halls_db',
                'package_id' => 7,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات أبها الحديثة',
                'email' => 'contact@abha-halls.com',
                'domain' => 'abha-halls',
                'db_name' => 'abha_halls_db',
                'package_id' => 8,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات الطائف الفاخرة',
                'email' => 'info@taif-halls.com',
                'domain' => 'taif-halls',
                'db_name' => 'taif_halls_db',
                'package_id' => 9,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات القصيم المتميزة',
                'email' => 'contact@qassim-halls.com',
                'domain' => 'qassim-halls',
                'db_name' => 'qassim_halls_db',
                'package_id' => 10,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات حائل الحديثة',
                'email' => 'info@hail-halls.com',
                'domain' => 'hail-halls',
                'db_name' => 'hail_halls_db',
                'package_id' => 11,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات الجوف الفاخرة',
                'email' => 'contact@jouf-halls.com',
                'domain' => 'jouf-halls',
                'db_name' => 'jouf_halls_db',
                'package_id' => 12,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات نجران المتميزة',
                'email' => 'info@najran-halls.com',
                'domain' => 'najran-halls',
                'db_name' => 'najran_halls_db',
                'package_id' => 13,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات الباحة الحديثة',
                'email' => 'contact@baha-halls.com',
                'domain' => 'baha-halls',
                'db_name' => 'baha_halls_db',
                'package_id' => 14,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات جازان الفاخرة',
                'email' => 'info@jazan-halls.com',
                'domain' => 'jazan-halls',
                'db_name' => 'jazan_halls_db',
                'package_id' => 15,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات عسير المتميزة',
                'email' => 'contact@asir-halls.com',
                'domain' => 'asir-halls',
                'db_name' => 'asir_halls_db',
                'package_id' => 16,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات الحدود الشمالية الحديثة',
                'email' => 'info@northern-halls.com',
                'domain' => 'northern-halls',
                'db_name' => 'northern_halls_db',
                'package_id' => 17,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات حفر الباطن الفاخرة',
                'email' => 'contact@hafr-halls.com',
                'domain' => 'hafr-halls',
                'db_name' => 'hafr_halls_db',
                'package_id' => 18,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات القريات المتميزة',
                'email' => 'info@qurayyat-halls.com',
                'domain' => 'qurayyat-halls',
                'db_name' => 'qurayyat_halls_db',
                'package_id' => 19,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
            [
                'name' => 'قاعات رنية الحديثة',
                'email' => 'contact@ranyah-halls.com',
                'domain' => 'ranyah-halls',
                'db_name' => 'ranyah_halls_db',
                'package_id' => 20,
                'status' => 'active',
                'trial_ends_at' => null,
            ],
        ];

        foreach ($tenants as $tenant) {
            DB::table('tenants')->insert(array_merge($tenant, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
} 
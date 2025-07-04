<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptions = [
            [
                'tenant_id' => 1,
                'package_id' => 1,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'credit_card',
                'amount_paid' => 500,
            ],
            [
                'tenant_id' => 2,
                'package_id' => 2,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'bank_transfer',
                'amount_paid' => 1000,
            ],
            [
                'tenant_id' => 3,
                'package_id' => 3,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'credit_card',
                'amount_paid' => 2000,
            ],
            [
                'tenant_id' => 4,
                'package_id' => 4,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'paypal',
                'amount_paid' => 5000,
            ],
            [
                'tenant_id' => 5,
                'package_id' => 5,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'credit_card',
                'amount_paid' => 5000,
            ],
            [
                'tenant_id' => 6,
                'package_id' => 6,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'bank_transfer',
                'amount_paid' => 10000,
            ],
            [
                'tenant_id' => 7,
                'package_id' => 7,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'credit_card',
                'amount_paid' => 20000,
            ],
            [
                'tenant_id' => 8,
                'package_id' => 8,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'paypal',
                'amount_paid' => 50000,
            ],
            [
                'tenant_id' => 9,
                'package_id' => 9,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'credit_card',
                'amount_paid' => 200,
            ],
            [
                'tenant_id' => 10,
                'package_id' => 10,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'bank_transfer',
                'amount_paid' => 400,
            ],
            [
                'tenant_id' => 11,
                'package_id' => 11,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'credit_card',
                'amount_paid' => 800,
            ],
            [
                'tenant_id' => 12,
                'package_id' => 12,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'status' => 'active',
                'payment_method' => 'paypal',
                'amount_paid' => 2000,
            ],
            [
                'tenant_id' => 13,
                'package_id' => 13,
                'start_date' => '2024-01-01',
                'end_date' => '2024-03-31',
                'status' => 'active',
                'payment_method' => 'credit_card',
                'amount_paid' => 1200,
            ],
            [
                'tenant_id' => 14,
                'package_id' => 14,
                'start_date' => '2024-01-01',
                'end_date' => '2024-03-31',
                'status' => 'active',
                'payment_method' => 'bank_transfer',
                'amount_paid' => 2400,
            ],
            [
                'tenant_id' => 15,
                'package_id' => 15,
                'start_date' => '2024-01-01',
                'end_date' => '2024-03-31',
                'status' => 'active',
                'payment_method' => 'credit_card',
                'amount_paid' => 4800,
            ],
            [
                'tenant_id' => 16,
                'package_id' => 16,
                'start_date' => '2024-01-01',
                'end_date' => '2024-03-31',
                'status' => 'active',
                'payment_method' => 'paypal',
                'amount_paid' => 12000,
            ],
            [
                'tenant_id' => 17,
                'package_id' => 17,
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-30',
                'status' => 'active',
                'payment_method' => 'credit_card',
                'amount_paid' => 2200,
            ],
            [
                'tenant_id' => 18,
                'package_id' => 18,
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-30',
                'status' => 'active',
                'payment_method' => 'bank_transfer',
                'amount_paid' => 4400,
            ],
            [
                'tenant_id' => 19,
                'package_id' => 19,
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-30',
                'status' => 'active',
                'payment_method' => 'credit_card',
                'amount_paid' => 8800,
            ],
            [
                'tenant_id' => 20,
                'package_id' => 20,
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-30',
                'status' => 'active',
                'payment_method' => 'paypal',
                'amount_paid' => 22000,
            ],
        ];

        foreach ($subscriptions as $subscription) {
            DB::table('subscriptions')->insert(array_merge($subscription, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

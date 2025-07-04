<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic',
                'description' => 'Perfect for small halls with basic features',
                'price_monthly' => 15.00,
                'price_yearly' => 150.00,
                'features' => json_encode([
                    'invoices' => false,
                    'reports' => false,
                    'custom_branding' => false,
                    'api_access' => false,
                    'multiple_halls' => false,
                ]),
                'limits' => json_encode([
                    'halls' => 1,
                    'staff' => 1,
                    'bookings' => 50,
                    'storage' => 100, // MB
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'description' => 'Ideal for growing businesses with advanced features',
                'price_monthly' => 30.00,
                'price_yearly' => 300.00,
                'features' => json_encode([
                    'invoices' => true,
                    'reports' => true,
                    'custom_branding' => false,
                    'api_access' => false,
                    'multiple_halls' => true,
                ]),
                'limits' => json_encode([
                    'halls' => 3,
                    'staff' => 5,
                    'bookings' => 200,
                    'storage' => 500, // MB
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Complete solution for large operations',
                'price_monthly' => 50.00,
                'price_yearly' => 500.00,
                'features' => json_encode([
                    'invoices' => true,
                    'reports' => true,
                    'custom_branding' => true,
                    'api_access' => true,
                    'multiple_halls' => true,
                ]),
                'limits' => json_encode([
                    'halls' => -1, // Unlimited
                    'staff' => -1, // Unlimited
                    'bookings' => -1, // Unlimited
                    'storage' => 2000, // MB
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}

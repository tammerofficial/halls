<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            FeatureSeeder::class,
            PackageSeeder::class,
            RolePermissionSeeder::class,
            TenantSeeder::class,
            // Seeders الجديدة
            ServiceSeeder::class,
            CustomerSeeder::class,
            HallSeeder::class,
            BookingSeeder::class,
            MembershipSeeder::class,
            SubscriptionSeeder::class,
            InvoiceSeeder::class,
            MembershipFeatureSeeder::class,
            UserRoleSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@halls.om')->exists()) {
            User::create([
                'name' => 'مدير النظام',
            'email' => 'admin@halls.om',
                'password' => Hash::make('password123'),
                'phone' => '966500000000',
                'is_active' => 1,
        ]);
        }
    }
}

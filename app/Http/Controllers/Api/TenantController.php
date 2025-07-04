<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tenant = Tenant::with(['subscription.package', 'users'])->findOrFail($id);
        
        return response()->json([
            'tenant' => $tenant
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tenant = Tenant::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:active,inactive,suspended',
            'settings' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $tenant->update($request->only(['name', 'status', 'settings']));
        
        return response()->json([
            'message' => 'Tenant updated successfully',
            'tenant' => $tenant
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        
        try {
            // Drop tenant database
            DB::statement("DROP DATABASE IF EXISTS `{$tenant->database}`");
            
            // Delete tenant and related data
            $tenant->delete();
            
            return response()->json([
                'message' => 'Tenant deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_type' => 'required|string|in:wedding_hall,event_center,conference_hall,restaurant,other',
            'manager_name' => 'required|string|max:255',
            'manager_phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
            'database_name' => 'required|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
            'package_id' => 'required|exists:packages,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create tenant
            $tenant = Tenant::create([
                'name' => $request->company_name,
                'email' => $request->email,
                'domain' => $request->database_name,
                'db_name' => $request->database_name,
                'package_id' => $request->package_id,
                'status' => 'active',
            ]);

            // Get package
            $package = Package::find($request->package_id);
            if (!$package) {
                throw new \Exception('Package not found');
            }

            // Create subscription
            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'package_id' => $package->id,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'status' => 'active',
            ]);

            // Create user
            $user = User::create([
                'name' => $request->manager_name,
                'email' => $request->email,
                'username' => $request->email,
                'password' => Hash::make($request->password),
                'tenant_id' => $tenant->id,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            // Create tenant database
            $this->createTenantDatabase($request->database_name);

            // Create tenant tables
            $this->createTenantTables($request->database_name);

            DB::commit();

            return response()->json([
                'message' => 'تم تسجيل المستأجر بنجاح',
                'tenant' => $tenant,
                'user' => $user,
                'subscription' => $subscription
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'فشل في إنشاء المستأجر',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function createTenantDatabase($databaseName)
    {
        try {
            // Create database
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");
            
            // Set database connection
            config(['database.connections.tenant.database' => $databaseName]);
            
        } catch (\Exception $e) {
            throw new \Exception("Failed to create database: " . $e->getMessage());
        }
    }

    private function createTenantTables($databaseName)
    {
        try {
            // Set database connection
            config(['database.connections.tenant.database' => $databaseName]);
            
            // Create tables
            $migrations = [
                'create_halls_table',
                'create_services_table',
                'create_customers_table',
                'create_bookings_table',
                'create_booking_services_table',
                'create_invoices_table',
                'create_staff_users_table',
            ];

            foreach ($migrations as $migration) {
                $this->runTenantMigration($migration, $databaseName);
            }

        } catch (\Exception $e) {
            throw new \Exception("Failed to create tenant tables: " . $e->getMessage());
        }
    }

    private function runTenantMigration($migrationName, $databaseName)
    {
        // This is a simplified version - in production you'd want to use proper migration files
        $sql = $this->getMigrationSQL($migrationName);
        
        if ($sql) {
            DB::connection('tenant')->statement($sql);
        }
    }

    private function getMigrationSQL($migrationName)
    {
        $migrations = [
            'create_halls_table' => "
                CREATE TABLE IF NOT EXISTS halls (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    description TEXT,
                    capacity INT,
                    price_per_hour DECIMAL(10,2),
                    price_per_day DECIMAL(10,2),
                    address TEXT,
                    amenities JSON,
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ",
            'create_services_table' => "
                CREATE TABLE IF NOT EXISTS services (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    description TEXT,
                    price DECIMAL(10,2),
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ",
            'create_customers_table' => "
                CREATE TABLE IF NOT EXISTS customers (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255),
                    phone VARCHAR(20),
                    national_id VARCHAR(20),
                    address TEXT,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ",
            'create_bookings_table' => "
                CREATE TABLE IF NOT EXISTS bookings (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    customer_id BIGINT UNSIGNED,
                    hall_id BIGINT UNSIGNED,
                    event_date DATE,
                    start_time TIME,
                    end_time TIME,
                    total_amount DECIMAL(10,2),
                    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
                    notes TEXT,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ",
            'create_booking_services_table' => "
                CREATE TABLE IF NOT EXISTS booking_services (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    booking_id BIGINT UNSIGNED,
                    service_id BIGINT UNSIGNED,
                    price DECIMAL(10,2),
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ",
            'create_invoices_table' => "
                CREATE TABLE IF NOT EXISTS invoices (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    booking_id BIGINT UNSIGNED,
                    invoice_number VARCHAR(50),
                    amount DECIMAL(10,2),
                    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
                    due_date DATE,
                    paid_at TIMESTAMP NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ",
            'create_staff_users_table' => "
                CREATE TABLE IF NOT EXISTS staff_users (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) UNIQUE,
                    username VARCHAR(50) UNIQUE,
                    password VARCHAR(255),
                    role ENUM('admin', 'staff', 'manager') DEFAULT 'staff',
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            "
        ];

        return $migrations[$migrationName] ?? null;
    }

    public function list()
    {
        $tenants = Tenant::with(['subscription.package'])->paginate(10);
        
        return response()->json([
            'tenants' => $tenants
        ]);
    }
}

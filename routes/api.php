<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\HallController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TenantRegisterController;
use App\Http\Controllers\Api\TenantAuthController;
use App\Http\Controllers\Api\TenantCustomerController;

/*
|--------------------------------------------------------------------------
| API Routes v2.0 - Clean & Tenant-Aware
|--------------------------------------------------------------------------
*/

// =========================================================================
// == Public Routes (No Authentication Needed)
// =========================================================================
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']); // For central admins/users if any
Route::post('/tenants/register', [TenantRegisterController::class, 'register']);

// Tenant Auth Routes
Route::post('/tenant/auth/login', [TenantAuthController::class, 'login']);

// Public packages for tenant registration
Route::get('/packages', [PackageController::class, 'index']);

// =========================================================================
// == Central Authenticated Routes (Not Tenant-Specific)
// =========================================================================
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Packages - Additional Routes (for authenticated users)
    Route::post('/packages', [PackageController::class, 'store']);
    Route::put('/packages/{package}', [PackageController::class, 'update']);
    Route::delete('/packages/{package}', [PackageController::class, 'destroy']);
});

// =========================================================================
// == Tenant-Specific Routes (Authentication Required)
// =========================================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Tenant Auth
    Route::post('/tenant/auth/logout', [TenantAuthController::class, 'logout']);
    Route::get('/tenant/auth/user', [TenantAuthController::class, 'user']);
    
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    
    // Resources
    Route::apiResource('halls', HallController::class);
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('subscriptions', SubscriptionController::class);

    // Customer specific routes
    Route::get('/customers/stats', [TenantCustomerController::class, 'stats']);

    // Subscriptions - Additional Routes
    Route::post('/subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew']);
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
    Route::get('/subscriptions/stats/overview', [SubscriptionController::class, 'stats']);
    
    // Bookings - Additional Routes
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm']);
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

    // Invoices - Additional Routes
    Route::post('/invoices/{invoice}/pay', [InvoiceController::class, 'pay']);
    
}); 
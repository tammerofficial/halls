<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PackageController extends Controller
{
    /**
     * عرض قائمة الباقات العامة (بدون مصادقة)
     */
    public function publicList(): JsonResponse
    {
        $packages = Package::where('is_active', true)
            ->orderBy('price_monthly', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $packages
        ]);
    }

    /**
     * عرض قائمة الباقات
     */
    public function index(): JsonResponse
    {
        $packages = Package::with(['features'])
            ->orderBy('price', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $packages
        ]);
    }

    /**
     * عرض باقة محددة
     */
    public function show(Package $package): JsonResponse
    {
        $package->load(['features', 'subscriptions']);
        
        return response()->json([
            'success' => true,
            'data' => $package
        ]);
    }

    /**
     * إنشاء باقة جديدة
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_halls' => 'required|integer|min:1',
            'max_bookings_per_month' => 'required|integer|min:1',
            'priority_support' => 'boolean',
            'custom_branding' => 'boolean',
            'advanced_analytics' => 'boolean',
            'features' => 'array',
            'features.*' => 'exists:features,id'
        ]);

        $package = Package::create($request->except('features'));
        
        if ($request->has('features')) {
            $package->features()->attach($request->features);
        }

        $package->load('features');

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الباقة بنجاح',
            'data' => $package
        ], 201);
    }

    /**
     * تحديث باقة
     */
    public function update(Request $request, Package $package): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_halls' => 'required|integer|min:1',
            'max_bookings_per_month' => 'required|integer|min:1',
            'priority_support' => 'boolean',
            'custom_branding' => 'boolean',
            'advanced_analytics' => 'boolean',
            'is_active' => 'boolean',
            'features' => 'array',
            'features.*' => 'exists:features,id'
        ]);

        $package->update($request->except('features'));
        
        if ($request->has('features')) {
            $package->features()->sync($request->features);
        }

        $package->load('features');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الباقة بنجاح',
            'data' => $package
        ]);
    }

    /**
     * حذف باقة
     */
    public function destroy(Package $package): JsonResponse
    {
        // التحقق من عدم وجود اشتراكات نشطة
        if ($package->subscriptions()->where('status', 'active')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الباقة لوجود اشتراكات نشطة عليها'
            ], 400);
        }

        $package->features()->detach();
        $package->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الباقة بنجاح'
        ]);
    }

    /**
     * تغيير حالة الباقة
     */
    public function toggleStatus(Package $package): JsonResponse
    {
        $package->update(['is_active' => !$package->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير حالة الباقة بنجاح',
            'data' => $package
        ]);
    }

    /**
     * إحصائيات الباقات
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_packages' => Package::count(),
            'active_packages' => Package::where('is_active', true)->count(),
            'total_subscriptions' => \App\Models\Subscription::count(),
            'active_subscriptions' => \App\Models\Subscription::where('status', 'active')->count(),
            'total_revenue' => \App\Models\Subscription::sum('amount_paid'),
            'monthly_revenue' => \App\Models\Subscription::whereMonth('created_at', now()->month)->sum('amount_paid')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

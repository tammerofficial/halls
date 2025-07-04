<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PackageController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $packages = Package::orderBy('price_monthly')->get();
            
            return response()->json([
                'success' => true,
                'data' => $packages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الباقات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price_monthly' => 'required|numeric|min:0',
                'price_yearly' => 'required|numeric|min:0',
                'features' => 'nullable|array'
            ]);

            $package = Package::create($validated);

            return response()->json([
                'success' => true,
                'data' => $package,
                'message' => 'تم إنشاء الباقة بنجاح'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الباقة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Package $package): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $package
        ]);
    }

    public function update(Request $request, Package $package): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price_monthly' => 'sometimes|required|numeric|min:0',
                'price_yearly' => 'sometimes|required|numeric|min:0',
                'features' => 'nullable|array'
            ]);

            $package->update($validated);

            return response()->json([
                'success' => true,
                'data' => $package,
                'message' => 'تم تحديث الباقة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الباقة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Package $package): JsonResponse
    {
        try {
            $package->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الباقة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الباقة',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
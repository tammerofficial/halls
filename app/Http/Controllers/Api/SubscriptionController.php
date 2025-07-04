<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $subscriptions = Subscription::with(['tenant', 'package'])->get();
            
            return response()->json([
                'success' => true,
                'data' => $subscriptions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الاشتراكات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
                'package_id' => 'required|exists:packages,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => 'sometimes|in:active,inactive,expired'
            ]);

            $subscription = Subscription::create($validated);

            return response()->json([
                'success' => true,
                'data' => $subscription,
                'message' => 'تم إنشاء الاشتراك بنجاح'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الاشتراك',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Subscription $subscription): JsonResponse
    {
        $subscription->load(['tenant', 'package']);
        
        return response()->json([
            'success' => true,
            'data' => $subscription
        ]);
    }

    public function update(Request $request, Subscription $subscription): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tenant_id' => 'sometimes|required|exists:tenants,id',
                'package_id' => 'sometimes|required|exists:packages,id',
                'start_date' => 'sometimes|required|date',
                'end_date' => 'sometimes|required|date|after:start_date',
                'status' => 'sometimes|in:active,inactive,expired'
            ]);

            $subscription->update($validated);

            return response()->json([
                'success' => true,
                'data' => $subscription,
                'message' => 'تم تحديث الاشتراك بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الاشتراك',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Subscription $subscription): JsonResponse
    {
        try {
            $subscription->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الاشتراك بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الاشتراك',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function renew(Subscription $subscription): JsonResponse
    {
        try {
            $subscription->update([
                'end_date' => now()->addYear(),
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تجديد الاشتراك بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تجديد الاشتراك',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Subscription $subscription): JsonResponse
    {
        try {
            $subscription->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الاشتراك بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إلغاء الاشتراك',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total' => Subscription::count(),
                'active' => Subscription::where('status', 'active')->count(),
                'inactive' => Subscription::where('status', 'inactive')->count(),
                'expired' => Subscription::where('status', 'expired')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب إحصائيات الاشتراكات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CentralService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CentralController extends Controller
{
    protected $centralService;

    public function __construct(CentralService $centralService)
    {
        $this->centralService = $centralService;
    }

    /**
     * Get dashboard statistics
     */
    public function dashboardStats(): JsonResponse
    {
        try {
            $stats = $this->centralService->getDashboardStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new booking
     */
    public function createBooking(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'hall_id' => 'required|exists:halls,id',
                'customer_id' => 'required|exists:customers,id',
                'event_type' => 'required|string',
                'event_date' => 'required|date',
                'start_time' => 'required|string',
                'end_time' => 'required|string',
                'guests_count' => 'required|integer|min:1',
                'total_amount' => 'required|numeric|min:0',
                'deposit_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'services' => 'nullable|array',
                'services.*.service_id' => 'required|exists:services,id',
                'services.*.price' => 'required|numeric|min:0',
                'services.*.quantity' => 'nullable|integer|min:1'
            ]);

            $booking = $this->centralService->createBooking($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحجز بنجاح',
                'data' => $booking
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إنشاء الحجز',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Create new customer
     */
    public function createCustomer(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string',
                'national_id' => 'nullable|string|max:20'
            ]);

            $customer = $this->centralService->createCustomer($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء العميل بنجاح',
                'data' => $customer
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إنشاء العميل',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Create new hall
     */
    public function createHall(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'capacity' => 'required|integer|min:1',
                'price_per_hour' => 'required|numeric|min:0',
                'price_per_day' => 'required|numeric|min:0',
                'address' => 'required|string',
                'photos' => 'nullable|array',
                'amenities' => 'nullable|array',
                'services' => 'nullable|array',
                'services.*.service_id' => 'required|exists:services,id',
                'services.*.price' => 'required|numeric|min:0'
            ]);

            $hall = $this->centralService->createHall($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء القاعة بنجاح',
                'data' => $hall
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إنشاء القاعة',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Create new service
     */
    public function createService(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0'
            ]);

            $service = $this->centralService->createService($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الخدمة بنجاح',
                'data' => $service
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إنشاء الخدمة',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get hall availability
     */
    public function hallAvailability(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'hall_id' => 'required|exists:halls,id',
                'date' => 'required|date'
            ]);

            $availability = $this->centralService->getHallAvailability(
                $validated['hall_id'],
                $validated['date']
            );
            
            return response()->json([
                'success' => true,
                'data' => $availability
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل توفر القاعة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search bookings
     */
    public function searchBookings(Request $request): JsonResponse
    {
        try {
            $query = $request->get('query', '');
            $filters = $request->only(['status', 'date_from', 'date_to']);

            $bookings = $this->centralService->searchBookings($query, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في البحث',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search customers
     */
    public function searchCustomers(Request $request): JsonResponse
    {
        try {
            $query = $request->get('query', '');

            $customers = $this->centralService->searchCustomers($query);
            
            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في البحث',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pay invoice
     */
    public function payInvoice(Request $request, $invoiceId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01'
            ]);

            $invoice = $this->centralService->payInvoice($invoiceId, $validated['amount']);
            
            return response()->json([
                'success' => true,
                'message' => 'تم دفع الفاتورة بنجاح',
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في دفع الفاتورة',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Export bookings
     */
    public function exportBookings(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to']);
            $bookings = $this->centralService->exportBookings($filters);
            
            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تصدير البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export invoices
     */
    public function exportInvoices(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status']);
            $invoices = $this->centralService->exportInvoices($filters);
            
            return response()->json([
                'success' => true,
                'data' => $invoices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تصدير البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all stats
     */
    public function getAllStats(): JsonResponse
    {
        try {
            $stats = [
                'bookings' => $this->centralService->getBookingStats(),
                'customers' => $this->centralService->getCustomerStats(),
                'services' => $this->centralService->getServiceStats(),
                'invoices' => $this->centralService->getInvoiceStats(),
                'tenants' => $this->centralService->getTenantStats(),
                'staff' => $this->centralService->getStaffStats(),
                'packages' => $this->centralService->getPackageStats(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل الإحصائيات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
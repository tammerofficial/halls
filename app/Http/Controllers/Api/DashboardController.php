<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hall;
use App\Models\Booking;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $stats = [
            'totalHalls' => Hall::count(),
            'activeBookings' => Booking::where('status', 'confirmed')->where('event_date', '>=', now())->count(),
            'totalCustomers' => Customer::count(),
            'monthlyRevenue' => number_format(Booking::where('status', 'confirmed')->whereMonth('event_date', now()->month)->sum('total_amount'), 2)
        ];

        $recentBookings = Booking::with(['customer', 'hall'])
            ->latest()
            ->take(5)
            ->get();

        $availableHalls = Hall::whereDoesntHave('bookings', function($query) {
                $query->whereDate('event_date', Carbon::today());
            })
            ->take(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'availableHalls' => $availableHalls,
        ]);
    }
}

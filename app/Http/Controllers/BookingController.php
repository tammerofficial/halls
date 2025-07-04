<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Hall;
use App\Models\Service;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['customer', 'hall'])->get();
        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $halls = Hall::where('is_active', true)->get();
        $customers = Customer::all();
        $services = Service::all();
        
        return view('bookings.create', compact('halls', 'customers', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'hall_id' => 'required|exists:halls,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'guests_count' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $booking = Booking::create($validated);
        
        if ($request->has('services')) {
            $booking->services()->attach($request->services);
        }

        return redirect()->route('web.bookings.index')->with('success', 'تم إنشاء الحجز بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $booking->load(['customer', 'hall', 'services']);
        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $halls = Hall::where('is_active', true)->get();
        $customers = Customer::all();
        $services = Service::all();
        $booking->load('services');
        
        return view('bookings.edit', compact('booking', 'halls', 'customers', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'hall_id' => 'required|exists:halls,id',
            'booking_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'guests_count' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $booking->update($validated);
        
        if ($request->has('services')) {
            $booking->services()->sync($request->services);
        } else {
            $booking->services()->detach();
        }

        return redirect()->route('web.bookings.index')->with('success', 'تم تحديث الحجز بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->services()->detach();
        $booking->delete();

        return redirect()->route('web.bookings.index')->with('success', 'تم حذف الحجز بنجاح');
    }
}

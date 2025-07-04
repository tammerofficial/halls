<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['hall', 'customer', 'services.service'])->get();
        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hall_id' => 'required|exists:halls,id',
            'customer_id' => 'required|exists:customers,id',
            'event_type' => 'required|string|max:255',
            'event_date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'guests_count' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $booking = Booking::create($request->all());

        return response()->json($booking, 201);
    }

    public function show(string $id)
    {
        $booking = Booking::with(['hall', 'customer', 'services.service', 'invoice'])->findOrFail($id);
        return response()->json($booking);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'hall_id' => 'sometimes|required|exists:halls,id',
            'customer_id' => 'sometimes|required|exists:customers,id',
            'event_type' => 'sometimes|required|string|max:255',
            'event_date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i',
            'guests_count' => 'sometimes|required|integer|min:1',
            'total_amount' => 'sometimes|required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update($request->all());

        return response()->json($booking);
    }

    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }

    public function confirm(Booking $booking)
    {
        $booking->update(['status' => 'confirmed']);
        return response()->json(['message' => 'Booking confirmed successfully']);
    }

    public function cancel(Booking $booking)
    {
        $booking->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Booking cancelled successfully']);
    }
}

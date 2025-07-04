<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Booking;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with(['booking', 'booking.customer'])->get();
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bookings = Booking::with('customer')
            ->whereDoesntHave('invoice')
            ->where('status', 'completed')
            ->get();
        
        return view('invoices.create', compact('bookings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id|unique:invoices,booking_id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,cancelled',
            'payment_method' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        Invoice::create($validated);

        return redirect()->route('web.invoices.index')->with('success', 'تم إنشاء الفاتورة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['booking', 'booking.customer', 'booking.services']);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $bookings = Booking::with('customer')
            ->where('status', 'completed')
            ->get();
            
        return view('invoices.edit', compact('invoice', 'bookings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id|unique:invoices,booking_id,' . $invoice->id,
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,cancelled',
            'payment_method' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $invoice->update($validated);

        return redirect()->route('web.invoices.index')->with('success', 'تم تحديث الفاتورة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('web.invoices.index')->with('success', 'تم حذف الفاتورة بنجاح');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('booking.customer')->get();
        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
        ]);

        $invoice = Invoice::create([
            'booking_id' => $request->booking_id,
            'invoice_number' => 'INV-' . time(),
            'amount' => $request->amount,
            'due_date' => $request->due_date,
        ]);

        return response()->json($invoice, 201);
    }

    public function show(string $id)
    {
        $invoice = Invoice::with('booking.customer')->findOrFail($id);
        return response()->json($invoice);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'amount' => 'sometimes|required|numeric|min:0',
            'due_date' => 'sometimes|required|date',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->all());

        return response()->json($invoice);
    }

    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    public function pay(Invoice $invoice)
    {
        $invoice->update([
            'paid_amount' => $invoice->amount,
            'status' => 'paid',
        ]);

        return response()->json(['message' => 'Invoice paid successfully']);
    }
}

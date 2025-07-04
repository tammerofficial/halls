<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('bookings')->get();
        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'national_id' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create($request->all());

        return response()->json($customer, 201);
    }

    public function show(string $id)
    {
        $customer = Customer::with('bookings.hall')->findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customers,email,' . $id,
            'phone' => 'sometimes|required|string|max:20',
            'address' => 'nullable|string',
            'national_id' => 'nullable|string|max:20',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return response()->json($customer);
    }

    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}

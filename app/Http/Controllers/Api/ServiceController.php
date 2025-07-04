<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->get();
        return response()->json($services);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $service = Service::create($request->all());

        return response()->json($service, 201);
    }

    public function show(string $id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $service = Service::findOrFail($id);
        $service->update($request->all());

        return response()->json($service);
    }

    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => false]);

        return response()->json(['message' => 'Service deactivated successfully']);
    }
}

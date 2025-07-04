<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use Illuminate\Http\Request;
use App\Http\Resources\HallResource;

class HallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Hall::query();

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Capacity filter
        if ($request->has('capacity') && $request->capacity) {
            switch($request->capacity) {
                case 'small':
                    $query->where('capacity', '<', 100);
                    break;
                case 'medium':
                    $query->whereBetween('capacity', [100, 300]);
                    break;
                case 'large':
                    $query->where('capacity', '>', 300);
                    break;
            }
        }

        $halls = $query->latest()->paginate(12); // Paginate results

        return HallResource::collection($halls);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'price_per_hour' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'amenities' => 'nullable|array',
            'is_available' => 'required|boolean',
        ]);

        $hall = Hall::create($validated);

        return new HallResource($hall);
    }

    /**
     * Display the specified resource.
     */
    public function show(Hall $hall)
    {
        return new HallResource($hall);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hall $hall)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'price_per_hour' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'amenities' => 'nullable|array',
            'is_available' => 'required|boolean',
        ]);
        
        $hall->update($validated);

        return new HallResource($hall);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hall $hall)
    {
        $hall->delete();
        return response()->noContent();
    }
}

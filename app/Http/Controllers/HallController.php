<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use Illuminate\Http\Request;

class HallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $halls = Hall::all();
        return view('halls.index', compact('halls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('halls.create');
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
            'is_active' => 'boolean',
        ]);

        Hall::create($validated);

        return redirect()->route('web.halls.index')->with('success', 'تمت إضافة القاعة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hall $hall)
    {
        return view('halls.show', compact('hall'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hall $hall)
    {
        return view('halls.edit', compact('hall'));
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
            'is_active' => 'boolean',
        ]);

        $hall->update($validated);

        return redirect()->route('web.halls.index')->with('success', 'تم تحديث القاعة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hall $hall)
    {
        $hall->delete();

        return redirect()->route('web.halls.index')->with('success', 'تم حذف القاعة بنجاح');
    }

    /**
     * Search halls by criteria
     */
    public function search(Request $request)
    {
        $query = Hall::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('capacity_min')) {
            $query->where('capacity', '>=', $request->capacity_min);
        }

        if ($request->has('price_max')) {
            $query->where('price_per_hour', '<=', $request->price_max);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $halls = $query->get();

        return view('halls.index', compact('halls'));
    }
}

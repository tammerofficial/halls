<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::paginate(10);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'required|array',
            'limits' => 'required|array',
        ]);

        $package = Package::create($request->all());

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created successfully');
    }

    public function show(Package $package)
    {
        $package->load('tenants');
        return view('admin.packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'required|array',
            'limits' => 'required|array',
        ]);

        $package->update($request->all());

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated successfully');
    }

    public function destroy(Package $package)
    {
        if ($package->tenants()->count() > 0) {
            return redirect()->route('admin.packages.index')
                ->with('error', 'Cannot delete package with active tenants');
        }

        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted successfully');
    }
}

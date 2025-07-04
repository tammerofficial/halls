<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Package;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('package')->paginate(10);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $packages = Package::where('is_active', true)->get();
        return view('admin.tenants.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants',
            'domain' => 'required|string|unique:tenants',
            'package_id' => 'required|exists:packages,id',
        ]);

        $tenant = Tenant::create([
            'name' => $request->name,
            'email' => $request->email,
            'domain' => $request->domain,
            'db_name' => 'tenant_' . strtolower(str_replace([' ', '-'], '_', $request->domain)),
            'package_id' => $request->package_id,
            'status' => 'active',
        ]);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant created successfully');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['package', 'subscriptions', 'settings']);
        return view('admin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $packages = Package::where('is_active', true)->get();
        return view('admin.tenants.edit', compact('tenant', 'packages'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'domain' => 'required|string|unique:tenants,domain,' . $tenant->id,
            'package_id' => 'required|exists:packages,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $tenant->update($request->all());

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant updated successfully');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully');
    }
}

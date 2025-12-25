<?php

namespace App\Http\Controllers\Farmers;

use App\Http\Controllers\Controller;
use App\Models\Farmers\FarmerGroup;
use App\Models\Location\Region;
use App\Models\Location\District;
use App\Models\Location\Village;
use Illuminate\Http\Request;

class FarmerGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FarmerGroup::with(['region', 'district', 'village'])
            ->withCount('farmers');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by region
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        $groups = $query->latest()->paginate(12);

        // Stats
        $stats = [
            'total' => FarmerGroup::count(),
            'total_members' => \App\Models\Farmers\Farmer::count(),
            'active' => FarmerGroup::active()->count(),
        ];

        $regions = Region::orderBy('name')->get();

        return view('farmer-groups.index', compact('groups', 'stats', 'regions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regions = Region::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $villages = Village::orderBy('name')->get();

        return view('farmer-groups.create', compact('regions', 'districts', 'villages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'leader_name' => 'nullable|string|max:255',
            'leader_phone' => 'nullable|string|max:20',
            'established_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = FarmerGroup::generateCode();
        $validated['is_active'] = $request->boolean('is_active', true);

        $group = FarmerGroup::create($validated);

        return redirect()
            ->route('farmer-groups.show', $group)
            ->with('success', 'Farmer group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FarmerGroup $farmerGroup)
    {
        $farmerGroup->load(['region', 'district', 'village', 'farmers' => function ($query) {
            $query->with(['village', 'farms'])->latest()->take(10);
        }]);

        $stats = [
            'total_members' => $farmerGroup->farmers()->count(),
            'active_members' => $farmerGroup->farmers()->active()->count(),
            'total_farms' => $farmerGroup->farmers()->withCount('farms')->get()->sum('farms_count'),
            'organic_farmers' => $farmerGroup->farmers()->organic()->count(),
        ];

        return view('farmer-groups.show', compact('farmerGroup', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FarmerGroup $farmerGroup)
    {
        $regions = Region::orderBy('name')->get();
        $districts = District::where('region_id', $farmerGroup->region_id)->orderBy('name')->get();
        $villages = Village::where('district_id', $farmerGroup->district_id)->orderBy('name')->get();

        return view('farmer-groups.edit', compact('farmerGroup', 'regions', 'districts', 'villages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FarmerGroup $farmerGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'leader_name' => 'nullable|string|max:255',
            'leader_phone' => 'nullable|string|max:20',
            'established_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $farmerGroup->update($validated);

        return redirect()
            ->route('farmer-groups.show', $farmerGroup)
            ->with('success', 'Farmer group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FarmerGroup $farmerGroup)
    {
        // Check if group has farmers
        if ($farmerGroup->farmers()->count() > 0) {
            return back()->with('error', 'Cannot delete group with assigned farmers. Please reassign farmers first.');
        }

        $farmerGroup->delete();

        return redirect()
            ->route('farmer-groups.index')
            ->with('success', 'Farmer group deleted successfully.');
    }

    /**
     * Show members of a farmer group.
     */
    public function members(FarmerGroup $farmerGroup)
    {
        $farmerGroup->load(['region', 'district', 'village']);

        $farmers = $farmerGroup->farmers()
            ->with(['village', 'district', 'farms'])
            ->withCount('farms')
            ->paginate(15);

        return view('farmer-groups.members', compact('farmerGroup', 'farmers'));
    }
}

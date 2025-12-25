<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\Location\Region;
use App\Models\Location\District;
use App\Models\Location\Ward;
use App\Models\Location\Village;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    public function index(Request $request)
    {
        $query = Village::with(['region', 'district', 'ward'])
            ->withCount(['farmers', 'farms', 'subvillages']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $villages = $query->orderBy('name')->paginate(15);
        $regions = Region::orderBy('name')->get();
        $districts = District::orderBy('name')->get();

        return view('catchment-areas.villages.index', compact('villages', 'regions', 'districts'));
    }

    public function create()
    {
        $regions = Region::orderBy('name')->get();
        return view('catchment-areas.villages.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'ward_id' => 'required|exists:wards,id',
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Village::create($validated);

        return redirect()
            ->route('villages.index')
            ->with('success', 'Village created successfully.');
    }

    public function show(Village $village)
    {
        $village->load(['region', 'district', 'ward']);
        $village->loadCount(['farmers', 'farms', 'subvillages']);
        $subvillages = $village->subvillages()->orderBy('name')->get();

        return view('catchment-areas.villages.show', compact('village', 'subvillages'));
    }

    public function edit(Village $village)
    {
        $regions = Region::orderBy('name')->get();
        $districts = District::where('region_id', $village->region_id)->orderBy('name')->get();
        $wards = Ward::where('district_id', $village->district_id)->orderBy('name')->get();

        return view('catchment-areas.villages.edit', compact('village', 'regions', 'districts', 'wards'));
    }

    public function update(Request $request, Village $village)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'ward_id' => 'required|exists:wards,id',
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $village->update($validated);

        return redirect()
            ->route('villages.show', $village)
            ->with('success', 'Village updated successfully.');
    }

    public function destroy(Village $village)
    {
        if ($village->subvillages()->count() > 0) {
            return back()->with('error', 'Cannot delete village with subvillages. Please delete subvillages first.');
        }

        if ($village->farmers()->count() > 0) {
            return back()->with('error', 'Cannot delete village with assigned farmers.');
        }

        $village->delete();

        return redirect()
            ->route('villages.index')
            ->with('success', 'Village deleted successfully.');
    }
}

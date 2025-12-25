<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\Location\Region;
use App\Models\Location\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function index(Request $request)
    {
        $query = District::with('region')
            ->withCount(['villages', 'farmers', 'farms']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $districts = $query->orderBy('name')->paginate(15);
        $regions = Region::orderBy('name')->get();

        return view('catchment-areas.districts.index', compact('districts', 'regions'));
    }

    public function create()
    {
        $regions = Region::orderBy('name')->get();
        return view('catchment-areas.districts.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'required|string|max:10|unique:districts,code',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        District::create($validated);

        return redirect()
            ->route('districts.index')
            ->with('success', 'District created successfully.');
    }

    public function show(District $district)
    {
        $district->load('region');
        $district->loadCount(['villages', 'farmers', 'farms']);
        $villages = $district->villages()->withCount('farmers')->orderBy('name')->get();

        return view('catchment-areas.districts.show', compact('district', 'villages'));
    }

    public function edit(District $district)
    {
        $regions = Region::orderBy('name')->get();
        return view('catchment-areas.districts.edit', compact('district', 'regions'));
    }

    public function update(Request $request, District $district)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'required|string|max:10|unique:districts,code,' . $district->id,
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $district->update($validated);

        return redirect()
            ->route('districts.show', $district)
            ->with('success', 'District updated successfully.');
    }

    public function destroy(District $district)
    {
        if ($district->villages()->count() > 0) {
            return back()->with('error', 'Cannot delete district with villages. Please delete villages first.');
        }

        $district->delete();

        return redirect()
            ->route('districts.index')
            ->with('success', 'District deleted successfully.');
    }
}

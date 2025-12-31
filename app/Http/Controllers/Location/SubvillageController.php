<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\Location\Region;
use App\Models\Location\District;
use App\Models\Location\Ward;
use App\Models\Location\Village;
use App\Models\Location\Subvillage;
use Illuminate\Http\Request;

class SubvillageController extends Controller
{
    public function __construct()
    {
        // Restrict CRUD actions for ICS inspectors (view-only access)
        $this->middleware(function ($request, $next) {
            if (auth()->user()->isIcsInspector()) {
                abort(403, 'ICS inspectors have view-only access to catchment areas.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = Subvillage::with(['region', 'district', 'village'])
            ->withCount(['farmers', 'farms']);

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

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $subvillages = $query->orderBy('name')->paginate(15);
        $regions = Region::orderBy('name')->get();

        return view('catchment-areas.subvillages.index', compact('subvillages', 'regions'));
    }

    public function create()
    {
        $regions = Region::orderBy('name')->get();
        return view('catchment-areas.subvillages.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'ward_id' => 'required|exists:wards,id',
            'village_id' => 'required|exists:villages,id',
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Subvillage::create($validated);

        return redirect()
            ->route('subvillages.index')
            ->with('success', 'Subvillage created successfully.');
    }

    public function show(Subvillage $subvillage)
    {
        $subvillage->load(['region', 'district', 'ward', 'village']);
        $subvillage->loadCount(['farmers', 'farms']);

        return view('catchment-areas.subvillages.show', compact('subvillage'));
    }

    public function edit(Subvillage $subvillage)
    {
        $regions = Region::orderBy('name')->get();
        $districts = District::where('region_id', $subvillage->region_id)->orderBy('name')->get();
        $wards = Ward::where('district_id', $subvillage->district_id)->orderBy('name')->get();
        $villages = Village::where('ward_id', $subvillage->ward_id)->orderBy('name')->get();

        return view('catchment-areas.subvillages.edit', compact('subvillage', 'regions', 'districts', 'wards', 'villages'));
    }

    public function update(Request $request, Subvillage $subvillage)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'ward_id' => 'required|exists:wards,id',
            'village_id' => 'required|exists:villages,id',
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $subvillage->update($validated);

        return redirect()
            ->route('subvillages.show', $subvillage)
            ->with('success', 'Subvillage updated successfully.');
    }

    public function destroy(Subvillage $subvillage)
    {
        if ($subvillage->farmers()->count() > 0) {
            return back()->with('error', 'Cannot delete subvillage with assigned farmers.');
        }

        $subvillage->delete();

        return redirect()
            ->route('subvillages.index')
            ->with('success', 'Subvillage deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\Location\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
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
        $query = Region::withCount(['districts', 'villages', 'farmers', 'farms']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $regions = $query->orderBy('name')->paginate(15);

        return view('catchment-areas.regions.index', compact('regions'));
    }

    public function create()
    {
        return view('catchment-areas.regions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'required|string|max:10|unique:regions,code',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Region::create($validated);

        return redirect()
            ->route('regions.index')
            ->with('success', 'Region created successfully.');
    }

    public function show(Region $region)
    {
        $region->loadCount(['districts', 'villages', 'farmers', 'farms']);
        $districts = $region->districts()->withCount(['villages', 'farmers'])->orderBy('name')->get();

        return view('catchment-areas.regions.show', compact('region', 'districts'));
    }

    public function edit(Region $region)
    {
        return view('catchment-areas.regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'required|string|max:10|unique:regions,code,' . $region->id,
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $region->update($validated);

        return redirect()
            ->route('regions.show', $region)
            ->with('success', 'Region updated successfully.');
    }

    public function destroy(Region $region)
    {
        if ($region->districts()->count() > 0) {
            return back()->with('error', 'Cannot delete region with districts. Please delete districts first.');
        }

        $region->delete();

        return redirect()
            ->route('regions.index')
            ->with('success', 'Region deleted successfully.');
    }
}

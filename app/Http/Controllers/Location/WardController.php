<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\Location\District;
use App\Models\Location\Ward;
use Illuminate\Http\Request;

class WardController extends Controller
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
        $query = Ward::with('district.region')
            ->withCount('villages');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $wards = $query->orderBy('name')->paginate(15);
        $districts = District::with('region')->orderBy('name')->get();

        return view('catchment-areas.wards.index', compact('wards', 'districts'));
    }

    public function create()
    {
        $districts = District::with('region')->orderBy('name')->get();
        return view('catchment-areas.wards.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:10|unique:wards,code',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Ward::create($validated);

        return redirect()
            ->route('wards.index')
            ->with('success', 'Ward created successfully.');
    }

    public function show(Ward $ward)
    {
        $ward->load('district.region');
        $ward->loadCount('villages');
        $villages = $ward->villages()->withCount('subvillages')->orderBy('name')->get();

        return view('catchment-areas.wards.show', compact('ward', 'villages'));
    }

    public function edit(Ward $ward)
    {
        $districts = District::with('region')->orderBy('name')->get();
        return view('catchment-areas.wards.edit', compact('ward', 'districts'));
    }

    public function update(Request $request, Ward $ward)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:10|unique:wards,code,' . $ward->id,
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $ward->update($validated);

        return redirect()
            ->route('wards.show', $ward)
            ->with('success', 'Ward updated successfully.');
    }

    public function destroy(Ward $ward)
    {
        if ($ward->villages()->count() > 0) {
            return back()->with('error', 'Cannot delete ward with villages. Please delete villages first.');
        }

        $ward->delete();

        return redirect()
            ->route('wards.index')
            ->with('success', 'Ward deleted successfully.');
    }
}

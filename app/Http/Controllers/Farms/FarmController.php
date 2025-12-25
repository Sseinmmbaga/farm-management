<?php

namespace App\Http\Controllers\Farms;

use App\Http\Controllers\Controller;
use App\Enums\UserRole;
use App\Models\Farms\Farm;
use App\Models\Farmers\Farmer;
use App\Models\Location\Region;
use App\Models\Location\District;
use App\Models\Location\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FarmController extends Controller
{
    /**
     * Display a listing of farms.
     */
    public function index(Request $request)
    {
        $query = Farm::with(['farmer', 'region', 'district', 'village']);
        
        // Filter by farmer if requested
        if ($request->has('farmer_id')) {
            $query->where('farmer_id', $request->farmer_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by certification status
        if ($request->filled('certification_status')) {
            $query->where('certification_status', $request->certification_status);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // For farmers, only show their own farms
        if (Auth::user()->hasRole(UserRole::FARMER)) {
            $query->where('farmer_id', Auth::user()->farmer->id);
        }

        // For extension officers, show farms of their assigned farmers
        if (Auth::user()->hasRole(UserRole::EXTENSION_OFFICER)) {
            $farmerIds = Farmer::where('extension_officer_id', Auth::id())->pluck('id');
            $query->whereIn('farmer_id', $farmerIds);
        }
        
        $farms = $query->latest()->paginate(20);
        
        return view('farms.index', compact('farms'));
    }

    /**
     * Show the form for creating a new farm.
     */
    public function create(Request $request)
    {
        $farmers = [];
        $selectedFarmer = null;
        
        // If farmer_id is provided, pre-select that farmer
        if ($request->has('farmer_id')) {
            $selectedFarmer = Farmer::find($request->farmer_id);
        }
        
        // Get farmers based on user role
        if (Auth::user()->hasRole(UserRole::ADMIN) || Auth::user()->hasRole(UserRole::SUPERVISOR)) {
            $farmers = Farmer::active()->orderBy('first_name')->get();
        } elseif (Auth::user()->hasRole(UserRole::EXTENSION_OFFICER)) {
            $farmers = Farmer::where('extension_officer_id', Auth::id())->active()->orderBy('first_name')->get();
        } elseif (Auth::user()->hasRole(UserRole::FARMER)) {
            // Farmers can only create farms for themselves
            $selectedFarmer = Auth::user()->farmer;
        }
        
        $regions = Region::orderBy('name')->get();
        $districts = $selectedFarmer ? District::where('region_id', $selectedFarmer->region_id)->orderBy('name')->get() : collect();
        $villages = $selectedFarmer ? Village::where('district_id', $selectedFarmer->district_id)->orderBy('name')->get() : collect();
        
        return view('farms.create', compact('farmers', 'selectedFarmer', 'regions', 'districts', 'villages'));
    }

    /**
     * Store a newly created farm in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'total_area' => 'required|numeric|min:0.01',
            'cultivated_area' => 'nullable|numeric|min:0',
            'soil_type' => 'nullable|string|max:100',
            'water_source' => 'nullable|string|max:100',
            'terrain' => 'nullable|string|max:100',
            'certification_status' => 'nullable|in:organic,in-conversion,conventional',
            'organic_since' => 'nullable|date',
            'conversion_year' => 'nullable|integer|min:2000|max:' . date('Y'),
            'status' => 'required|in:active,inactive,abandoned',
            'registration_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        // Check permissions
        $this->authorizeFarmCreation($validated['farmer_id']);
        
        // Generate farm code
        $validated['code'] = Farm::generateCode($validated['farmer_id']);
        
        // Set cultivated area to total area if not provided
        if (empty($validated['cultivated_area'])) {
            $validated['cultivated_area'] = $validated['total_area'];
        }
        
        try {
            DB::beginTransaction();
            
            $farm = Farm::create($validated);
            
            // Create initial farm history entry
            $farm->histories()->create([
                'action' => 'created',
                'description' => 'Farm registered in the system',
                'user_id' => Auth::id(),
                'changes' => $validated,
            ]);
            
            DB::commit();
            
            return redirect()->route('farms.show', $farm)
                ->with('success', 'Farm created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create farm: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified farm.
     */
    public function show(Farm $farm)
    {
        $this->authorize('view', $farm);
        
        $farm->load([
            'farmer', 
            'region', 
            'district', 
            'village',
            'histories' => function ($query) {
                $query->latest()->limit(10);
            },
            'seasons' => function ($query) {
                $query->latest()->limit(5);
            },
            'boundaries',
            'assets' => function ($query) {
                $query->latest()->limit(5);
            }
        ]);
        
        return view('farms.show', compact('farm'));
    }

    /**
     * Show the form for editing the specified farm.
     */
    public function edit(Farm $farm)
    {
        $this->authorize('update', $farm);
        
        $farmers = [];
        if (Auth::user()->hasRole(UserRole::ADMIN) || Auth::user()->hasRole(UserRole::SUPERVISOR)) {
            $farmers = Farmer::active()->orderBy('first_name')->get();
        } elseif (Auth::user()->hasRole(UserRole::EXTENSION_OFFICER)) {
            $farmers = Farmer::where('extension_officer_id', Auth::id())->active()->orderBy('first_name')->get();
        }
        
        $regions = Region::orderBy('name')->get();
        $districts = District::where('region_id', $farm->region_id)->orderBy('name')->get();
        $villages = Village::where('district_id', $farm->district_id)->orderBy('name')->get();
        
        return view('farms.edit', compact('farm', 'farmers', 'regions', 'districts', 'villages'));
    }

    /**
     * Update the specified farm in storage.
     */
    public function update(Request $request, Farm $farm)
    {
        $this->authorize('update', $farm);
        
        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'total_area' => 'required|numeric|min:0.01',
            'cultivated_area' => 'nullable|numeric|min:0',
            'soil_type' => 'nullable|string|max:100',
            'water_source' => 'nullable|string|max:100',
            'terrain' => 'nullable|string|max:100',
            'certification_status' => 'nullable|in:organic,in-conversion,conventional',
            'organic_since' => 'nullable|date',
            'conversion_year' => 'nullable|integer|min:2000|max:' . date('Y'),
            'status' => 'required|in:active,inactive,abandoned',
            'notes' => 'nullable|string',
        ]);
        
        // Check if farmer is being changed
        if ($farm->farmer_id != $validated['farmer_id']) {
            $this->authorizeFarmCreation($validated['farmer_id']);
        }
        
        // Set cultivated area to total area if not provided
        if (empty($validated['cultivated_area'])) {
            $validated['cultivated_area'] = $validated['total_area'];
        }
        
        try {
            DB::beginTransaction();
            
            // Track changes for history
            $changes = [];
            foreach ($validated as $key => $value) {
                if ($farm->$key != $value) {
                    $changes[$key] = [
                        'old' => $farm->$key,
                        'new' => $value,
                    ];
                }
            }
            
            $farm->update($validated);
            
            // Create history entry if there were changes
            if (!empty($changes)) {
                $farm->histories()->create([
                    'action' => 'updated',
                    'description' => 'Farm details updated',
                    'user_id' => Auth::id(),
                    'changes' => $changes,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('farms.show', $farm)
                ->with('success', 'Farm updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update farm: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified farm from storage.
     */
    public function destroy(Farm $farm)
    {
        $this->authorize('delete', $farm);
        
        try {
            DB::beginTransaction();
            
            // Create history entry before deletion
            $farm->histories()->create([
                'action' => 'deleted',
                'description' => 'Farm marked as deleted',
                'user_id' => Auth::id(),
                'changes' => $farm->toArray(),
            ]);
            
            $farm->delete();
            
            DB::commit();
            
            return redirect()->route('farms.index')
                ->with('success', 'Farm deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete farm: ' . $e->getMessage());
        }
    }

    /**
     * Show farm history.
     */
    public function history(Farm $farm)
    {
        $this->authorize('view', $farm);
        
        $histories = $farm->histories()->with('user')->latest()->paginate(20);
        
        return view('farms.history', compact('farm', 'histories'));
    }

    /**
     * Show farm seasons.
     */
    public function seasons(Farm $farm)
    {
        $this->authorize('view', $farm);
        
        $seasons = $farm->seasons()->with('season')->latest()->paginate(20);
        
        return view('farms.seasons', compact('farm', 'seasons'));
    }

    /**
     * Show farm boundaries.
     */
    public function boundaries(Farm $farm)
    {
        $this->authorize('view', $farm);

        // Refresh farm to get latest boundary_coordinates and load boundaries relationship
        $farm->refresh();
        $farm->load('boundaries');

        return response()
            ->view('farms.boundaries', compact('farm'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Store farm boundaries.
     */
    public function storeBoundaries(Request $request, Farm $farm)
    {
        $this->authorize('update', $farm);
        
        $validated = $request->validate([
            'boundaries' => 'required|array|min:3',
            'boundaries.*.latitude' => 'required|numeric|between:-90,90',
            'boundaries.*.longitude' => 'required|numeric|between:-180,180',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Clear existing boundaries
            $farm->boundaries()->delete();
            
            // Store new boundaries
            foreach ($validated['boundaries'] as $index => $point) {
                $farm->boundaries()->create([
                    'latitude' => $point['latitude'],
                    'longitude' => $point['longitude'],
                    'point_order' => $index + 1,
                ]);
            }
            
            // Update boundary coordinates array
            $farm->update([
                'boundary_coordinates' => array_map(function ($point) {
                    return [$point['longitude'], $point['latitude']];
                }, $validated['boundaries'])
            ]);
            
            // Create activity log entry
            $farm->activityLogs()->create([
                'type' => \App\Enums\LogType::ACTIVITY,
                'name' => 'Boundary Updated',
                'description' => 'Farm boundaries updated with ' . count($validated['boundaries']) . ' points',
                'log_date' => now(),
                'status' => 'done',
                'farmer_id' => $farm->farmer_id,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Farm boundaries saved successfully!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save boundaries: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show farm activity logs.
     */
    public function logs(Farm $farm)
    {
        $this->authorize('view', $farm);
        
        $logs = $farm->activityLogs()->with('user')->latest()->paginate(20);
        
        return view('farms.logs', compact('farm', 'logs'));
    }

    /**
     * Show farm on map.
     */
    public function map(Farm $farm)
    {
        $this->authorize('view', $farm);

        // Refresh farm to get latest data and load boundaries relationship
        $farm->refresh();
        $farm->load('boundaries');

        return response()
            ->view('farms.map', compact('farm'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Show all farms on map.
     */
    public function allFarmsMap(Request $request)
    {
        $this->authorize('viewAny', Farm::class);
        
        $farms = Farm::with('farmer')->active()->get();
        
        $geoJson = [
            'type' => 'FeatureCollection',
            'features' => $farms->map(function ($farm) {
                return $farm->toGeoJson();
            })->toArray(),
        ];
        
        return view('farms.all-map', compact('geoJson'));
    }

    /**
     * Authorize farm creation for the given farmer.
     */
    private function authorizeFarmCreation($farmerId)
    {
        $user = Auth::user();
        
        // Admins and supervisors can create farms for any farmer
        if ($user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::SUPERVISOR)) {
            return true;
        }

        // Extension officers can only create farms for their assigned farmers
        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id')->toArray();
            if (!in_array($farmerId, $assignedFarmerIds)) {
                abort(403, 'You can only create farms for farmers assigned to you.');
            }
            return true;
        }

        // Farmers can only create farms for themselves
        if ($user->hasRole(UserRole::FARMER)) {
            if ($user->farmer->id != $farmerId) {
                abort(403, 'You can only create farms for yourself.');
            }
            return true;
        }
        
        abort(403, 'You are not authorized to create farms.');
    }
}
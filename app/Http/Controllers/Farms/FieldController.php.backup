<?php

namespace App\Http\Controllers\Farms;

use App\Http\Controllers\Controller;
use App\Models\Farms\Farm;
use App\Models\Farms\Field;
use App\Models\Farms\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FieldController extends Controller
{
    /**
     * Display a listing of fields for a farm.
     */
    public function index(Request $request, Farm $farm)
    {
        $this->authorize('viewAny', [Field::class, $farm]);
        
        $query = Field::where('farm_id', $farm->id)->with(['farm']);
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by crop type
        if ($request->has('crop_type')) {
            $query->where('current_crop_type', $request->crop_type);
        }
        
        // Search
        if ($request->has('search')) {
            $query->search($request->search);
        }
        
        $fields = $query->latest()->paginate(20);
        
        return view('fields.index', compact('farm', 'fields'));
    }

    /**
     * Show the form for creating a new field.
     */
    public function create(Farm $farm)
    {
        $this->authorize('create', [Field::class, $farm]);
        
        $seasons = Season::active()->orderBy('name')->get();
        
        return view('fields.create', compact('farm', 'seasons'));
    }

    /**
     * Store a newly created field in storage.
     */
    public function store(Request $request, Farm $farm)
    {
        $this->authorize('create', [Field::class, $farm]);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location_description' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'total_area' => 'required|numeric|min:0.01',
            'measurement_unit' => 'required|in:acres,hectares',
            'soil_type' => 'nullable|string|max:100',
            'soil_ph' => 'nullable|numeric|between:0,14',
            'soil_texture' => 'nullable|string|max:50',
            'slope_percentage' => 'nullable|numeric|min:0|max:100',
            'drainage' => 'nullable|string|max:50',
            'current_crop_type' => 'nullable|string|max:100',
            'crop_variety' => 'nullable|string|max:100',
            'planting_date' => 'nullable|date',
            'expected_harvest_date' => 'nullable|date|after_or_equal:planting_date',
            'expected_yield' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|in:kg,tons',
            'status' => 'required|in:active,fallow,prepared,planted,growing,harvested,abandoned,converted',
            'irrigation_type' => 'nullable|string|max:100',
            'irrigation_source' => 'nullable|string|max:100',
            'irrigation_frequency_days' => 'nullable|integer|min:1',
            'is_organic' => 'boolean',
            'organic_certified_since' => 'nullable|date',
            'certification_body' => 'nullable|string|max:200',
            'certification_number' => 'nullable|string|max:100',
            'previous_crop' => 'nullable|string|max:100',
            'next_planned_crop' => 'nullable|string|max:100',
            'rotation_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'establishment_date' => 'nullable|date',
        ]);
        
        // Add farm_id to validated data
        $validated['farm_id'] = $farm->id;
        
        // Generate field code
        $validated['code'] = Field::generateCode($farm->id);
        
        // Set default values
        $validated['is_organic'] = $validated['is_organic'] ?? false;
        
        try {
            DB::beginTransaction();
            
            $field = Field::create($validated);
            
            // Create initial field history entry if crop type is provided
            if (!empty($validated['current_crop_type'])) {
                $field->histories()->create([
                    'crop_type' => $validated['current_crop_type'],
                    'crop_variety' => $validated['crop_variety'] ?? null,
                    'area_planted' => $validated['total_area'],
                    'planting_date' => $validated['planting_date'] ?? null,
                    'year' => $validated['planting_date'] ? date('Y', strtotime($validated['planting_date'])) : date('Y'),
                    'recorded_by' => Auth::id(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('fields.show', [$farm, $field])
                ->with('success', 'Field created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create field: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified field.
     */
    public function show(Farm $farm, Field $field)
    {
        $this->authorize('view', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        $field->load([
            'farm',
            'histories' => function ($query) {
                $query->latest()->limit(10);
            },
            'boundaries',
        ]);
        
        return view('fields.show', compact('farm', 'field'));
    }

    /**
     * Show the form for editing the specified field.
     */
    public function edit(Farm $farm, Field $field)
    {
        $this->authorize('update', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        $seasons = Season::active()->orderBy('name')->get();
        
        return view('fields.edit', compact('farm', 'field', 'seasons'));
    }

    /**
     * Update the specified field in storage.
     */
    public function update(Request $request, Farm $farm, Field $field)
    {
        $this->authorize('update', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location_description' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'total_area' => 'required|numeric|min:0.01',
            'measurement_unit' => 'required|in:acres,hectares',
            'soil_type' => 'nullable|string|max:100',
            'soil_ph' => 'nullable|numeric|between:0,14',
            'soil_texture' => 'nullable|string|max:50',
            'slope_percentage' => 'nullable|numeric|min:0|max:100',
            'drainage' => 'nullable|string|max:50',
            'current_crop_type' => 'nullable|string|max:100',
            'crop_variety' => 'nullable|string|max:100',
            'planting_date' => 'nullable|date',
            'expected_harvest_date' => 'nullable|date|after_or_equal:planting_date',
            'actual_harvest_date' => 'nullable|date|after_or_equal:planting_date',
            'expected_yield' => 'nullable|numeric|min:0',
            'actual_yield' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|in:kg,tons',
            'status' => 'required|in:active,fallow,prepared,planted,growing,harvested,abandoned,converted',
            'irrigation_type' => 'nullable|string|max:100',
            'irrigation_source' => 'nullable|string|max:100',
            'irrigation_frequency_days' => 'nullable|integer|min:1',
            'is_organic' => 'boolean',
            'organic_certified_since' => 'nullable|date',
            'certification_body' => 'nullable|string|max:200',
            'certification_number' => 'nullable|string|max:100',
            'previous_crop' => 'nullable|string|max:100',
            'next_planned_crop' => 'nullable|string|max:100',
            'rotation_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        
        // Set default values
        $validated['is_organic'] = $validated['is_organic'] ?? false;
        
        try {
            DB::beginTransaction();
            
            // Track changes for history
            $changes = [];
            $cropChanged = false;
            
            foreach ($validated as $key => $value) {
                if ($field->$key != $value) {
                    $changes[$key] = [
                        'old' => $field->$key,
                        'new' => $value,
                    ];
                    
                    // Check if crop type changed
                    if ($key === 'current_crop_type' && $value !== $field->current_crop_type) {
                        $cropChanged = true;
                    }
                }
            }
            
            $field->update($validated);
            
            // Create history entry if crop changed
            if ($cropChanged && !empty($validated['current_crop_type'])) {
                $field->histories()->create([
                    'crop_type' => $validated['current_crop_type'],
                    'crop_variety' => $validated['crop_variety'] ?? null,
                    'area_planted' => $validated['total_area'],
                    'planting_date' => $validated['planting_date'] ?? null,
                    'year' => $validated['planting_date'] ? date('Y', strtotime($validated['planting_date'])) : date('Y'),
                    'recorded_by' => Auth::id(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('fields.show', [$farm, $field])
                ->with('success', 'Field updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update field: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified field from storage.
     */
    public function destroy(Farm $farm, Field $field)
    {
        $this->authorize('delete', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        try {
            DB::beginTransaction();
            
            $field->delete();
            
            DB::commit();
            
            return redirect()->route('fields.index', $farm)
                ->with('success', 'Field deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete field: ' . $e->getMessage());
        }
    }

    /**
     * Show field boundaries.
     */
    public function boundaries(Farm $farm, Field $field)
    {
        $this->authorize('manageBoundaries', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        return view('fields.boundaries', compact('farm', 'field'));
    }

    /**
     * Store field boundaries.
     */
    public function storeBoundaries(Request $request, Farm $farm, Field $field)
    {
        $this->authorize('manageBoundaries', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        $validated = $request->validate([
            'boundaries' => 'required|array|min:3',
            'boundaries.*.latitude' => 'required|numeric|between:-90,90',
            'boundaries.*.longitude' => 'required|numeric|between:-180,180',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Clear existing boundaries
            $field->boundaries()->delete();
            
            // Store new boundaries
            foreach ($validated['boundaries'] as $index => $point) {
                $field->boundaries()->create([
                    'latitude' => $point['latitude'],
                    'longitude' => $point['longitude'],
                    'point_order' => $index + 1,
                    'captured_by' => Auth::id(),
                    'captured_at' => now(),
                ]);
            }
            
            // Update boundary coordinates array
            $field->update([
                'boundary_coordinates' => array_map(function ($point) {
                    return [$point['longitude'], $point['latitude']];
                }, $validated['boundaries'])
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Field boundaries saved successfully!'
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
     * Show field history.
     */
    public function history(Farm $farm, Field $field)
    {
        $this->authorize('viewHistory', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        $histories = $field->histories()->with(['season', 'recordedBy'])->latest()->paginate(20);
        
        return view('fields.history', compact('farm', 'field', 'histories'));
    }

    /**
     * Show field on map.
     */
    public function map(Farm $farm, Field $field)
    {
        $this->authorize('viewMap', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        return view('fields.map', compact('farm', 'field'));
    }

    /**
     * Update field status (quick action).
     */
    public function updateStatus(Request $request, Farm $farm, Field $field)
    {
        $this->authorize('updateStatus', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:active,fallow,prepared,planted,growing,harvested,abandoned,converted',
        ]);
        
        $field->update($validated);
        
        return back()->with('success', 'Field status updated successfully!');
    }

    /**
     * Record harvest for a field.
     */
    public function recordHarvest(Request $request, Farm $farm, Field $field)
    {
        $this->authorize('recordHarvest', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        $validated = $request->validate([
            'actual_yield' => 'required|numeric|min:0',
            'actual_harvest_date' => 'required|date',
            'quality_grade' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update field
            $field->update([
                'actual_yield' => $validated['actual_yield'],
                'actual_harvest_date' => $validated['actual_harvest_date'],
                'status' => 'harvested',
            ]);
            
            // Update latest history entry
            $latestHistory = $field->histories()->latest()->first();
            if ($latestHistory) {
                $latestHistory->update([
                    'harvest_date' => $validated['actual_harvest_date'],
                    'yield_amount' => $validated['actual_yield'],
                    'quality_grade' => $validated['quality_grade'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }
            
            DB::commit();
            
            return back()->with('success', 'Harvest recorded successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to record harvest: ' . $e->getMessage());
        }
    }
}

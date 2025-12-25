<?php

namespace App\Http\Controllers;

use App\Services\MapService;
use App\Models\Farms\Farm;
use App\Models\Farms\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MapController extends Controller
{
    use AuthorizesRequests;
    
    protected $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    /**
     * Display the main map with all farms
     */
    public function index()
    {
        $this->authorize('viewAny', Farm::class);
        
        $config = $this->mapService->getMapConfig();
        
        return view('map.index', [
            'config' => $config,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Display farm map
     */
    public function farmMap(Farm $farm)
    {
        $this->authorize('view', $farm);
        
        $config = $this->mapService->getMapConfig();
        $geoJson = $this->mapService->getFarmGeoJson($farm);
        
        return view('map.farm', [
            'farm' => $farm,
            'config' => $config,
            'geoJson' => $geoJson,
        ]);
    }

    /**
     * Display field map
     */
    public function fieldMap(Farm $farm, Field $field)
    {
        $this->authorize('view', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        $config = $this->mapService->getMapConfig();
        $geoJson = $this->mapService->getFieldGeoJson($field);
        
        return view('map.field', [
            'farm' => $farm,
            'field' => $field,
            'config' => $config,
            'geoJson' => $geoJson,
        ]);
    }

    /**
     * Get GeoJSON for all farms (API endpoint)
     */
    public function getAllFarmsGeoJson()
    {
        $this->authorize('viewAny', Farm::class);
        
        $geoJson = $this->mapService->getAllFarmsGeoJson();
        
        return response()->json($geoJson);
    }

    /**
     * Get GeoJSON for a specific farm (API endpoint)
     */
    public function getFarmGeoJson(Farm $farm)
    {
        $this->authorize('view', $farm);
        
        $geoJson = $this->mapService->getFarmGeoJson($farm);
        
        return response()->json($geoJson);
    }

    /**
     * Get GeoJSON for all fields of a farm (API endpoint)
     */
    public function getFarmFieldsGeoJson(Farm $farm)
    {
        $this->authorize('view', $farm);
        
        $geoJson = $this->mapService->getFarmFieldsGeoJson($farm);
        
        return response()->json($geoJson);
    }

    /**
     * Get GeoJSON for a specific field (API endpoint)
     */
    public function getFieldGeoJson(Farm $farm, Field $field)
    {
        $this->authorize('view', $field);
        
        // Ensure field belongs to farm
        if ($field->farm_id !== $farm->id) {
            abort(404);
        }
        
        $geoJson = $this->mapService->getFieldGeoJson($field);
        
        return response()->json($geoJson);
    }

    /**
     * Calculate area from coordinates (API endpoint)
     */
    public function calculateArea(Request $request)
    {
        $this->authorize('create', Farm::class);
        
        $request->validate([
            'coordinates' => 'required|array|min:3',
            'coordinates.*' => 'required|array|size:2',
            'coordinates.*.0' => 'required|numeric|between:-180,180', // longitude
            'coordinates.*.1' => 'required|numeric|between:-90,90',   // latitude
        ]);
        
        $area = $this->mapService->calculateAreaFromCoordinates($request->coordinates);
        
        return response()->json([
            'area' => $area,
            'unit' => 'hectares',
        ]);
    }

    /**
     * Get map configuration (API endpoint)
     */
    public function getConfig()
    {
        $config = $this->mapService->getMapConfig();
        
        return response()->json($config);
    }
}
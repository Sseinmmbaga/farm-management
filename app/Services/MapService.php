<?php

namespace App\Services;

use App\Models\Farms\Farm;
use App\Models\Farms\Field;
use Illuminate\Support\Facades\Cache;

class MapService
{
    /**
     * Get GeoJSON for all farms
     */
    public function getAllFarmsGeoJson(): array
    {
        return Cache::remember('geojson.all_farms', 3600, function () {
            $farms = Farm::with(['farmer', 'fields'])
                ->whereHas('boundaries')
                ->orWhereNotNull('boundary_coordinates')
                ->get();
            
            return $this->createGeoJsonCollection($farms->map(function ($farm) {
                return $farm->toGeoJson();
            })->toArray());
        });
    }

    /**
     * Get GeoJSON for a specific farm
     */
    public function getFarmGeoJson(Farm $farm): array
    {
        return Cache::remember("geojson.farm.{$farm->id}", 3600, function () use ($farm) {
            $farm->load(['farmer', 'fields']);
            return $farm->toGeoJson();
        });
    }

    /**
     * Get GeoJSON for all fields of a farm
     */
    public function getFarmFieldsGeoJson(Farm $farm): array
    {
        return Cache::remember("geojson.farm_fields.{$farm->id}", 3600, function () use ($farm) {
            $fields = $farm->fields()
                ->whereHas('boundaries')
                ->orWhereNotNull('boundary_coordinates')
                ->get();
            
            return $this->createGeoJsonCollection($fields->map(function ($field) {
                return $field->toGeoJson();
            })->toArray());
        });
    }

    /**
     * Get GeoJSON for a specific field
     */
    public function getFieldGeoJson(Field $field): array
    {
        return Cache::remember("geojson.field.{$field->id}", 3600, function () use ($field) {
            $field->load(['farm', 'farm.farmer']);
            return $field->toGeoJson();
        });
    }

    /**
     * Get map configuration
     */
    public function getMapConfig(): array
    {
        return [
            'center' => config('remei.map.default_center'),
            'zoom' => config('remei.map.default_zoom'),
            'tile_provider' => config('remei.map.tile_provider'),
            'mapbox_token' => config('remei.map.mapbox_token'),
            'mapbox_style' => 'mapbox://styles/mapbox/satellite-streets-v12',
            'openstreetmap_url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'attribution' => '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        ];
    }

    /**
     * Calculate area from GeoJSON coordinates
     */
    public function calculateAreaFromCoordinates(array $coordinates): float
    {
        if (count($coordinates) < 3) {
            return 0.0;
        }

        $area = 0.0;
        $n = count($coordinates);
        
        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $area += $coordinates[$i][0] * $coordinates[$j][1];
            $area -= $coordinates[$j][0] * $coordinates[$i][1];
        }
        
        $area = abs($area) / 2.0;
        
        // Convert from square degrees to hectares (approximate)
        // This is a simplified conversion - for production use a proper geodesic area calculation
        $areaInHectares = $area * 10000;
        
        return round($areaInHectares, 2);
    }

    /**
     * Create a GeoJSON FeatureCollection
     */
    private function createGeoJsonCollection(array $features): array
    {
        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    /**
     * Get bounds from coordinates
     */
    public function getBoundsFromCoordinates(array $coordinates): array
    {
        if (empty($coordinates)) {
            return [
                'southwest' => [config('remei.map.default_center.lat'), config('remei.map.default_center.lng')],
                'northeast' => [config('remei.map.default_center.lat'), config('remei.map.default_center.lng')],
            ];
        }

        $lats = array_column($coordinates, 1); // latitude is second in [lng, lat]
        $lngs = array_column($coordinates, 0); // longitude is first in [lng, lat]

        return [
            'southwest' => [min($lats), min($lngs)],
            'northeast' => [max($lats), max($lngs)],
        ];
    }

    /**
     * Validate coordinates
     */
    public function validateCoordinates(float $latitude, float $longitude): bool
    {
        return $latitude >= -90 && $latitude <= 90 && 
               $longitude >= -180 && $longitude <= 180;
    }

    /**
     * Convert coordinates to WKT (Well-Known Text)
     */
    public function coordinatesToWkt(array $coordinates): string
    {
        if (empty($coordinates)) {
            return '';
        }

        // Close the polygon if not already closed
        if ($coordinates[0] !== end($coordinates)) {
            $coordinates[] = $coordinates[0];
        }

        $points = array_map(function ($coord) {
            return "{$coord[0]} {$coord[1]}";
        }, $coordinates);

        return 'POLYGON((' . implode(', ', $points) . '))';
    }

    /**
     * Parse WKT to coordinates
     */
    public function wktToCoordinates(string $wkt): array
    {
        if (empty($wkt) || !str_starts_with($wkt, 'POLYGON')) {
            return [];
        }

        // Extract coordinates from WKT
        preg_match('/POLYGON\(\((.*?)\)\)/', $wkt, $matches);
        if (!isset($matches[1])) {
            return [];
        }

        $points = explode(',', $matches[1]);
        $coordinates = [];

        foreach ($points as $point) {
            $coords = explode(' ', trim($point));
            if (count($coords) >= 2) {
                $coordinates[] = [(float)$coords[0], (float)$coords[1]];
            }
        }

        return $coordinates;
    }
}
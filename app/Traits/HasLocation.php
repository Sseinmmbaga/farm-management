<?php

namespace App\Traits;

use App\Models\Location\Village;
use App\Models\Location\District;
use App\Models\Location\Region;

trait HasLocation
{
    /**
     * Get the village relationship
     */
    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Get the district relationship
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the region relationship
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get formatted GPS coordinates
     */
    public function getCoordinatesAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "{$this->latitude}, {$this->longitude}";
        }
        return null;
    }

    /**
     * Get Google Maps URL
     */
    public function getMapUrlAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->village?->name,
            $this->district?->name,
            $this->region?->name,
        ]);
        return implode(', ', $parts);
    }

    /**
     * Scope to filter by region
     */
    public function scopeInRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    /**
     * Scope to filter by district
     */
    public function scopeInDistrict($query, $districtId)
    {
        return $query->where('district_id', $districtId);
    }

    /**
     * Scope to filter by village
     */
    public function scopeInVillage($query, $villageId)
    {
        return $query->where('village_id', $villageId);
    }

    /**
     * Scope to find nearby records within distance (km)
     */
    public function scopeNearby($query, float $lat, float $lng, float $distanceKm = 10)
    {
        $haversine = "(6371 * acos(cos(radians(?))
                     * cos(radians(latitude))
                     * cos(radians(longitude) - radians(?))
                     + sin(radians(?))
                     * sin(radians(latitude))))";

        return $query
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("*, {$haversine} AS distance", [$lat, $lng, $lat])
            ->havingRaw("distance < ?", [$distanceKm])
            ->orderBy('distance');
    }
}

<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandAsset extends Model
{
    protected $fillable = [
        'asset_id',
        'land_type',
        'total_area',
        'area_unit',
        'soil_type',
        'irrigation_type',
        'land_use',
        'is_organic',
        'organic_since',
        'boundary_points',
    ];

    protected $casts = [
        'total_area' => 'decimal:2',
        'is_organic' => 'boolean',
        'organic_since' => 'integer',
        'boundary_points' => 'array',
    ];

    // ==================== RELATIONSHIPS ====================

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // ==================== ACCESSORS ====================

    public function getLandTypeLabelAttribute(): string
    {
        return match($this->land_type) {
            'field' => 'Field (Shamba)',
            'paddock' => 'Paddock',
            'property' => 'Property',
            'greenhouse' => 'Greenhouse',
            default => $this->land_type,
        };
    }

    public function getSoilTypeLabelAttribute(): string
    {
        return match($this->soil_type) {
            'clay' => 'Clay (Udongo wa Mfinyanzi)',
            'sandy' => 'Sandy (Mchanga)',
            'loam' => 'Loam',
            'silt' => 'Silt',
            'black_cotton' => 'Black Cotton Soil',
            default => $this->soil_type ?? 'Not specified',
        };
    }

    public function getIrrigationTypeLabelAttribute(): string
    {
        return match($this->irrigation_type) {
            'rainfed' => 'Rain-fed',
            'drip' => 'Drip Irrigation',
            'sprinkler' => 'Sprinkler',
            'flood' => 'Flood Irrigation',
            'none' => 'None',
            default => $this->irrigation_type ?? 'Not specified',
        };
    }

    public function getAreaDisplayAttribute(): string
    {
        return $this->total_area . ' ' . $this->area_unit;
    }

    public function getYearsOrganicAttribute(): ?int
    {
        return $this->organic_since ? (date('Y') - $this->organic_since) : null;
    }
}

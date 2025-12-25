<?php

namespace App\Models\Assets;

use App\Enums\CropType;
use App\Models\Farms\Season;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropAsset extends Model
{
    protected $fillable = [
        'asset_id',
        'season_id',
        'crop_type',
        'variety',
        'planting_date',
        'expected_harvest_date',
        'actual_harvest_date',
        'planted_area',
        'growth_stage',
        'expected_yield',
        'actual_yield',
        'yield_unit',
    ];

    protected $casts = [
        'planting_date' => 'date',
        'expected_harvest_date' => 'date',
        'actual_harvest_date' => 'date',
        'planted_area' => 'decimal:2',
        'expected_yield' => 'decimal:2',
        'actual_yield' => 'decimal:2',
    ];

    // ==================== RELATIONSHIPS ====================

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    // ==================== ACCESSORS ====================

    public function getCropTypeLabelAttribute(): string
    {
        return match($this->crop_type) {
            'cotton' => 'Cotton (Pamba)',
            'sesame' => 'Sesame (Ufuta)',
            'sunflower' => 'Sunflower (Alizeti)',
            'maize' => 'Maize (Mahindi)',
            default => $this->crop_type,
        };
    }

    public function getGrowthStageLabelAttribute(): string
    {
        return match($this->growth_stage) {
            'seedling' => 'Seedling',
            'vegetative' => 'Vegetative',
            'flowering' => 'Flowering',
            'fruiting' => 'Fruiting',
            'mature' => 'Mature',
            'harvested' => 'Harvested',
            default => $this->growth_stage ?? 'Not set',
        };
    }

    public function getGrowthStageColorAttribute(): string
    {
        return match($this->growth_stage) {
            'seedling' => 'info',
            'vegetative' => 'primary',
            'flowering' => 'warning',
            'fruiting' => 'success',
            'mature' => 'dark',
            'harvested' => 'secondary',
            default => 'light',
        };
    }

    public function getDaysToHarvestAttribute(): ?int
    {
        if (!$this->expected_harvest_date || $this->actual_harvest_date) {
            return null;
        }
        return now()->diffInDays($this->expected_harvest_date, false);
    }

    public function getYieldPerHectareAttribute(): ?float
    {
        if (!$this->planted_area || !$this->actual_yield) {
            return null;
        }
        return round($this->actual_yield / $this->planted_area, 2);
    }

    public function getYieldVariancePercentAttribute(): ?float
    {
        if (!$this->expected_yield || !$this->actual_yield) {
            return null;
        }
        return round((($this->actual_yield - $this->expected_yield) / $this->expected_yield) * 100, 1);
    }

    public function getIsHarvestedAttribute(): bool
    {
        return $this->actual_harvest_date !== null;
    }

    // ==================== SCOPES ====================

    public function scopeForCropType($query, $type)
    {
        return $query->where('crop_type', $type);
    }

    public function scopeForSeason($query, $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    public function scopeHarvested($query)
    {
        return $query->whereNotNull('actual_harvest_date');
    }

    public function scopeGrowing($query)
    {
        return $query->whereNull('actual_harvest_date')
                     ->whereNotNull('planting_date');
    }

    public function scopeAtStage($query, $stage)
    {
        return $query->where('growth_stage', $stage);
    }
}

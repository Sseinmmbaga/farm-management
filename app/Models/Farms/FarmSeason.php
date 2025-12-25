<?php

namespace App\Models\Farms;

use App\Models\User;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmSeason extends Model
{
    use HasAuditTrail;

    protected $fillable = [
        'farm_id',
        'season_id',
        'primary_crop',
        'secondary_crop',
        'planned_area',
        'actual_area',
        'planting_date',
        'expected_harvest_date',
        'actual_harvest_date',
        'expected_yield',
        'actual_yield',
        'status',
        'notes',
    ];

    protected $casts = [
        'planned_area' => 'decimal:2',
        'actual_area' => 'decimal:2',
        'expected_yield' => 'decimal:2',
        'actual_yield' => 'decimal:2',
        'planting_date' => 'date',
        'expected_harvest_date' => 'date',
        'actual_harvest_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    // ==================== ACCESSORS ====================

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'planned' => 'Planned',
            'planted' => 'Planted',
            'growing' => 'Growing',
            'harvested' => 'Harvested',
            'failed' => 'Failed',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planned' => 'secondary',
            'planted' => 'info',
            'growing' => 'primary',
            'harvested' => 'success',
            'failed' => 'danger',
            default => 'secondary',
        };
    }

    public function getYieldVarianceAttribute(): ?float
    {
        if (!$this->expected_yield || !$this->actual_yield) {
            return null;
        }
        return (($this->actual_yield - $this->expected_yield) / $this->expected_yield) * 100;
    }

    public function getYieldPerHectareAttribute(): ?float
    {
        $area = $this->actual_area ?? $this->planned_area;
        if (!$area || !$this->actual_yield) {
            return null;
        }
        return $this->actual_yield / $area;
    }

    public function getDaysToHarvestAttribute(): ?int
    {
        if (!$this->planting_date || !$this->expected_harvest_date) {
            return null;
        }
        return $this->planting_date->diffInDays($this->expected_harvest_date);
    }

    // ==================== SCOPES ====================

    public function scopeForStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForCrop($query, $crop)
    {
        return $query->where('primary_crop', $crop);
    }

    public function scopeHarvested($query)
    {
        return $query->where('status', 'harvested');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planted', 'growing']);
    }
}

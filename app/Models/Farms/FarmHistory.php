<?php

namespace App\Models\Farms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmHistory extends Model
{
    protected $fillable = [
        'farm_id',
        'season_id',
        'year',
        'crop_type',
        'area_planted',
        'yield_amount',
        'yield_per_hectare',
        'quality_grade',
        'farming_practices',
        'inputs_used',
        'challenges',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'area_planted' => 'decimal:2',
        'yield_amount' => 'decimal:2',
        'yield_per_hectare' => 'decimal:2',
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

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
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

    public function getYieldPerformanceAttribute(): string
    {
        if (!$this->yield_per_hectare) {
            return 'Not recorded';
        }

        // Average yield benchmarks (kg/ha)
        $benchmarks = [
            'cotton' => 800,
            'sesame' => 500,
            'sunflower' => 1200,
            'maize' => 2000,
        ];

        $benchmark = $benchmarks[$this->crop_type] ?? 1000;
        $percentage = ($this->yield_per_hectare / $benchmark) * 100;

        if ($percentage >= 120) return 'Excellent';
        if ($percentage >= 100) return 'Good';
        if ($percentage >= 80) return 'Average';
        if ($percentage >= 60) return 'Below Average';
        return 'Poor';
    }

    // ==================== SCOPES ====================

    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeForCrop($query, $cropType)
    {
        return $query->where('crop_type', $cropType);
    }

    public function scopeForSeason($query, $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }
}

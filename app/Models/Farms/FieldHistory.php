<?php

namespace App\Models\Farms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FieldHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'season_id',
        'year',
        'crop_type',
        'crop_variety',
        'area_planted',
        'planting_date',
        'harvest_date',
        'yield_amount',
        'yield_per_unit_area',
        'fertilizers_used',
        'pesticides_used',
        'irrigation_details',
        'farming_practices',
        'quality_grade',
        'production_cost',
        'revenue',
        'profit',
        'challenges',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'area_planted' => 'decimal:2',
        'yield_amount' => 'decimal:2',
        'yield_per_unit_area' => 'decimal:2',
        'production_cost' => 'decimal:2',
        'revenue' => 'decimal:2',
        'profit' => 'decimal:2',
        'planting_date' => 'datetime',
        'harvest_date' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }

    // ==================== ACCESSORS ====================

    public function getYieldPerHectareAttribute(): ?float
    {
        if (!$this->yield_amount || !$this->area_planted) {
            return null;
        }
        return $this->yield_amount / $this->area_planted;
    }

    public function getProfitMarginAttribute(): ?float
    {
        if (!$this->revenue || !$this->production_cost) {
            return null;
        }
        return (($this->revenue - $this->production_cost) / $this->revenue) * 100;
    }

    public function getGrowingDaysAttribute(): ?int
    {
        if (!$this->planting_date || !$this->harvest_date) {
            return null;
        }
        return $this->planting_date->diffInDays($this->harvest_date);
    }
}
<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HarvestLog extends Model
{
    protected $fillable = [
        'activity_log_id',
        'crop_type',
        'variety',
        'area_harvested',
        'area_unit',
        'quantity_harvested',
        'quantity_unit',
        'yield_per_hectare',
        'quality_grade',
        'moisture_content',
        'harvest_method',
        'labor_hours',
        'workers_count',
        'storage_location',
        'buyer',
        'sale_price',
        'price_unit',
    ];

    protected $casts = [
        'area_harvested' => 'decimal:2',
        'quantity_harvested' => 'decimal:2',
        'yield_per_hectare' => 'decimal:2',
        'moisture_content' => 'decimal:2',
        'labor_hours' => 'integer',
        'workers_count' => 'integer',
        'sale_price' => 'decimal:2',
    ];

    // ==================== RELATIONSHIPS ====================

    public function activityLog(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class);
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

    public function getQualityGradeLabelAttribute(): string
    {
        return match($this->quality_grade) {
            'A' => 'Grade A (Premium)',
            'B' => 'Grade B (Standard)',
            'C' => 'Grade C (Below Standard)',
            'reject' => 'Rejected',
            default => $this->quality_grade ?? 'Not graded',
        };
    }

    public function getQualityGradeColorAttribute(): string
    {
        return match($this->quality_grade) {
            'A' => 'success',
            'B' => 'primary',
            'C' => 'warning',
            'reject' => 'danger',
            default => 'secondary',
        };
    }

    public function getHarvestMethodLabelAttribute(): string
    {
        return match($this->harvest_method) {
            'manual' => 'Manual Harvesting',
            'mechanical' => 'Mechanical Harvesting',
            'mixed' => 'Mixed (Manual & Mechanical)',
            default => $this->harvest_method ?? 'Not specified',
        };
    }

    public function getQuantityDisplayAttribute(): string
    {
        return number_format($this->quantity_harvested, 2) . ' ' . $this->quantity_unit;
    }

    public function getAreaDisplayAttribute(): string
    {
        return $this->area_harvested . ' ' . $this->area_unit;
    }

    public function getYieldDisplayAttribute(): string
    {
        if (!$this->yield_per_hectare) {
            return 'Not calculated';
        }
        return number_format($this->yield_per_hectare, 2) . ' kg/ha';
    }

    public function getTotalValueAttribute(): ?float
    {
        if (!$this->sale_price || !$this->quantity_harvested) {
            return null;
        }
        return $this->sale_price * $this->quantity_harvested;
    }

    public function getLaborEfficiencyAttribute(): ?float
    {
        if (!$this->labor_hours || !$this->quantity_harvested) {
            return null;
        }
        return round($this->quantity_harvested / $this->labor_hours, 2);
    }

    public function getYieldPerformanceAttribute(): string
    {
        if (!$this->yield_per_hectare) {
            return 'Unknown';
        }

        // Benchmark yields (kg/ha)
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

    public function scopeForCrop($query, $cropType)
    {
        return $query->where('crop_type', $cropType);
    }

    public function scopeByGrade($query, $grade)
    {
        return $query->where('quality_grade', $grade);
    }

    public function scopePremiumGrade($query)
    {
        return $query->where('quality_grade', 'A');
    }

    public function scopeWithSales($query)
    {
        return $query->whereNotNull('sale_price');
    }
}

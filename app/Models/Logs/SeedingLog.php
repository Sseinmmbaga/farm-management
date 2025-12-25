<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeedingLog extends Model
{
    protected $fillable = [
        'activity_log_id',
        'crop_type',
        'variety',
        'seed_source',
        'seed_lot_number',
        'seed_quantity',
        'seed_unit',
        'area_seeded',
        'area_unit',
        'seeding_method',
        'row_spacing',
        'plant_spacing',
        'seeding_depth',
        'soil_preparation',
    ];

    protected $casts = [
        'seed_quantity' => 'decimal:2',
        'area_seeded' => 'decimal:2',
        'row_spacing' => 'decimal:2',
        'plant_spacing' => 'decimal:2',
        'seeding_depth' => 'decimal:2',
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

    public function getSeedingMethodLabelAttribute(): string
    {
        return match($this->seeding_method) {
            'direct' => 'Direct Seeding',
            'transplant' => 'Transplanting',
            'broadcast' => 'Broadcasting',
            'drilling' => 'Drilling',
            default => $this->seeding_method ?? 'Not specified',
        };
    }

    public function getSeedRateAttribute(): ?float
    {
        if (!$this->seed_quantity || !$this->area_seeded) {
            return null;
        }
        return round($this->seed_quantity / $this->area_seeded, 2);
    }

    public function getSpacingDisplayAttribute(): string
    {
        $parts = [];
        if ($this->row_spacing) {
            $parts[] = "Row: {$this->row_spacing}cm";
        }
        if ($this->plant_spacing) {
            $parts[] = "Plant: {$this->plant_spacing}cm";
        }
        return implode(', ', $parts) ?: 'Not specified';
    }

    // ==================== SCOPES ====================

    public function scopeForCrop($query, $cropType)
    {
        return $query->where('crop_type', $cropType);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('seeding_method', $method);
    }
}

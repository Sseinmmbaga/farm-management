<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentAsset extends Model
{
    protected $fillable = [
        'asset_id',
        'equipment_type',
        'manufacturer',
        'model',
        'serial_number',
        'year_manufactured',
        'purchase_date',
        'purchase_price',
        'condition',
        'last_maintenance_date',
        'next_maintenance_date',
    ];

    protected $casts = [
        'year_manufactured' => 'integer',
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // ==================== ACCESSORS ====================

    public function getEquipmentTypeLabelAttribute(): string
    {
        return match($this->equipment_type) {
            'tractor' => 'Tractor',
            'sprayer' => 'Sprayer',
            'plough' => 'Plough',
            'harvester' => 'Harvester',
            'seeder' => 'Seeder',
            'tools' => 'Hand Tools',
            'pump' => 'Water Pump',
            'storage' => 'Storage Equipment',
            default => $this->equipment_type,
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return match($this->condition) {
            'excellent' => 'Excellent',
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor',
            'broken' => 'Broken/Non-functional',
            default => $this->condition,
        };
    }

    public function getConditionColorAttribute(): string
    {
        return match($this->condition) {
            'excellent' => 'success',
            'good' => 'primary',
            'fair' => 'warning',
            'poor' => 'danger',
            'broken' => 'dark',
            default => 'secondary',
        };
    }

    public function getAgeYearsAttribute(): ?int
    {
        return $this->year_manufactured ? (date('Y') - $this->year_manufactured) : null;
    }

    public function getIsMaintenanceDueAttribute(): bool
    {
        return $this->next_maintenance_date && $this->next_maintenance_date->isPast();
    }

    public function getIsMaintenanceSoonAttribute(): bool
    {
        return $this->next_maintenance_date &&
               $this->next_maintenance_date->isBetween(now(), now()->addDays(30));
    }

    public function getDaysSinceMaintenanceAttribute(): ?int
    {
        return $this->last_maintenance_date?->diffInDays(now());
    }

    // ==================== SCOPES ====================

    public function scopeOfType($query, $type)
    {
        return $query->where('equipment_type', $type);
    }

    public function scopeInCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    public function scopeNeedsMaintenance($query)
    {
        return $query->where('next_maintenance_date', '<=', now());
    }

    public function scopeMaintenanceDueSoon($query, $days = 30)
    {
        return $query->whereBetween('next_maintenance_date', [now(), now()->addDays($days)]);
    }
}

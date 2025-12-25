<?php

namespace App\Models\Quantities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Quantity extends Model
{
    protected $fillable = [
        'quantifiable_type',
        'quantifiable_id',
        'value',
        'unit_id',
        'label',
        'measure_type',
        'notes',
        'recorded_at',
        'recorded_by',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'recorded_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function quantifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ==================== ACCESSORS ====================

    public function getDisplayValueAttribute(): string
    {
        return number_format($this->value, 2) . ' ' . $this->unit?->symbol;
    }

    public function getFullDisplayAttribute(): string
    {
        $parts = [];
        if ($this->label) {
            $parts[] = $this->label . ':';
        }
        $parts[] = $this->display_value;
        return implode(' ', $parts);
    }

    public function getMeasureTypeLabelAttribute(): string
    {
        return match($this->measure_type) {
            'estimated' => 'Estimated',
            'measured' => 'Measured',
            'calculated' => 'Calculated',
            default => $this->measure_type ?? 'Unknown',
        };
    }

    // ==================== SCOPES ====================

    public function scopeForLabel($query, $label)
    {
        return $query->where('label', $label);
    }

    public function scopeForUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeArea($query)
    {
        return $query->where('label', 'area');
    }

    public function scopeYield($query)
    {
        return $query->where('label', 'yield');
    }

    public function scopeWeight($query)
    {
        return $query->where('label', 'weight');
    }

    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('recorded_at', [$start, $end]);
    }

    // ==================== HELPERS ====================

    /**
     * Convert this quantity to a different unit
     */
    public function convertTo(Unit $targetUnit): ?float
    {
        return $this->unit->convertTo($this->value, $targetUnit);
    }
}

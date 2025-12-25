<?php

namespace App\Models\Quantities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'name_sw',
        'symbol',
        'category',
        'conversion_factor',
        'base_unit',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:8',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function quantities(): HasMany
    {
        return $this->hasMany(Quantity::class);
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return $this->name_sw ?? $this->name;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->symbol})";
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'weight' => 'Weight/Mass',
            'area' => 'Area',
            'volume' => 'Volume',
            'count' => 'Count/Quantity',
            'length' => 'Length/Distance',
            default => $this->category,
        };
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeWeight($query)
    {
        return $query->where('category', 'weight');
    }

    public function scopeArea($query)
    {
        return $query->where('category', 'area');
    }

    public function scopeVolume($query)
    {
        return $query->where('category', 'volume');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ==================== HELPERS ====================

    /**
     * Convert a value from this unit to another unit
     */
    public function convertTo(float $value, Unit $targetUnit): ?float
    {
        if ($this->category !== $targetUnit->category) {
            return null; // Can't convert between different categories
        }

        // Convert to base unit, then to target
        $baseValue = $value * $this->conversion_factor;
        return $baseValue / $targetUnit->conversion_factor;
    }

    /**
     * Get common units for selection
     */
    public static function getCommonUnits(): array
    {
        return [
            'weight' => self::weight()->active()->ordered()->get(),
            'area' => self::area()->active()->ordered()->get(),
            'volume' => self::volume()->active()->ordered()->get(),
        ];
    }
}

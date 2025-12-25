<?php

namespace App\Traits;

use App\Models\Quantities\Quantity;
use App\Models\Quantities\Unit;

trait HasQuantities
{
    /**
     * Get all quantities for this model
     */
    public function quantities()
    {
        return $this->morphMany(Quantity::class, 'quantifiable');
    }

    /**
     * Add a quantity measurement
     */
    public function addQuantity(float $value, int $unitId, ?string $label = null, ?string $notes = null): Quantity
    {
        return $this->quantities()->create([
            'value' => $value,
            'unit_id' => $unitId,
            'label' => $label,
            'notes' => $notes,
            'recorded_at' => now(),
        ]);
    }

    /**
     * Get total quantity for a specific unit
     */
    public function getTotalQuantity(int $unitId): float
    {
        return $this->quantities()
            ->where('unit_id', $unitId)
            ->sum('value');
    }

    /**
     * Get the latest quantity
     */
    public function getLatestQuantity(): ?Quantity
    {
        return $this->quantities()
            ->latest('recorded_at')
            ->first();
    }

    /**
     * Get quantities by label
     */
    public function getQuantitiesByLabel(string $label)
    {
        return $this->quantities()
            ->where('label', $label)
            ->get();
    }

    /**
     * Get area in hectares (if applicable)
     */
    public function getAreaHectaresAttribute(): ?float
    {
        $hectareUnit = Unit::where('symbol', 'ha')->first();
        if (!$hectareUnit) {
            return null;
        }

        return $this->quantities()
            ->where('unit_id', $hectareUnit->id)
            ->where('label', 'area')
            ->sum('value');
    }

    /**
     * Get yield in kg (if applicable)
     */
    public function getYieldKgAttribute(): ?float
    {
        $kgUnit = Unit::where('symbol', 'kg')->first();
        if (!$kgUnit) {
            return null;
        }

        return $this->quantities()
            ->where('unit_id', $kgUnit->id)
            ->where('label', 'yield')
            ->sum('value');
    }
}

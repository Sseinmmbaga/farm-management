<?php

namespace App\Traits;

use App\Models\Farmers\Farmer;

trait BelongsToFarmer
{
    /**
     * Get the farmer relationship
     */
    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    /**
     * Scope to filter by farmer
     */
    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    /**
     * Scope to filter by multiple farmers
     */
    public function scopeForFarmers($query, array $farmerIds)
    {
        return $query->whereIn('farmer_id', $farmerIds);
    }

    /**
     * Get farmer name
     */
    public function getFarmerNameAttribute(): ?string
    {
        return $this->farmer?->full_name;
    }

    /**
     * Get farmer registration number
     */
    public function getFarmerRegNumberAttribute(): ?string
    {
        return $this->farmer?->registration_number;
    }
}

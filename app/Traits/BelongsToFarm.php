<?php

namespace App\Traits;

use App\Models\Farms\Farm;

trait BelongsToFarm
{
    /**
     * Get the farm relationship
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Scope to filter by farm
     */
    public function scopeForFarm($query, $farmId)
    {
        return $query->where('farm_id', $farmId);
    }

    /**
     * Scope to filter by multiple farms
     */
    public function scopeForFarms($query, array $farmIds)
    {
        return $query->whereIn('farm_id', $farmIds);
    }

    /**
     * Get farm name
     */
    public function getFarmNameAttribute(): ?string
    {
        return $this->farm?->name;
    }

    /**
     * Get farm code
     */
    public function getFarmCodeAttribute(): ?string
    {
        return $this->farm?->code;
    }
}

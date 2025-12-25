<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = [
        'name',
        'name_sw',
        'code',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }

    public function subvillages(): HasMany
    {
        return $this->hasMany(Subvillage::class);
    }

    public function farmers(): HasMany
    {
        return $this->hasMany(\App\Models\Farmers\Farmer::class);
    }

    public function farms(): HasMany
    {
        return $this->hasMany(\App\Models\Farms\Farm::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return $this->name_sw ?? $this->name;
    }

    public function getFarmerCountAttribute(): int
    {
        return $this->farmers()->count();
    }

    public function getFarmCountAttribute(): int
    {
        return $this->farms()->count();
    }
}

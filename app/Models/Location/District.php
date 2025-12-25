<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $fillable = [
        'region_id',
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

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
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

    public function scopeInRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return $this->name_sw ?? $this->name;
    }

    public function getFullNameAttribute(): string
    {
        return $this->name . ', ' . $this->region?->name;
    }
}

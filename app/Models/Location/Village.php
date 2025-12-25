<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    protected $fillable = [
        'ward_id',
        'district_id',
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

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function farmers(): HasMany
    {
        return $this->hasMany(\App\Models\Farmers\Farmer::class);
    }

    public function farms(): HasMany
    {
        return $this->hasMany(\App\Models\Farms\Farm::class);
    }

    public function subvillages(): HasMany
    {
        return $this->hasMany(Subvillage::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInWard($query, $wardId)
    {
        return $query->where('ward_id', $wardId);
    }

    public function scopeInDistrict($query, $districtId)
    {
        return $query->where('district_id', $districtId);
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
        return implode(', ', array_filter([
            $this->name,
            $this->ward?->name,
            $this->district?->name,
            $this->region?->name,
        ]));
    }

    public function getCoordinatesAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "{$this->latitude}, {$this->longitude}";
        }
        return null;
    }
}

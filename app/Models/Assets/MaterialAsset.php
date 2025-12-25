<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialAsset extends Model
{
    protected $fillable = [
        'asset_id',
        'material_type',
        'material_name',
        'brand',
        'batch_number',
        'quantity',
        'quantity_unit',
        'manufacture_date',
        'expiry_date',
        'is_organic_approved',
        'certification',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
        'is_organic_approved' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // ==================== ACCESSORS ====================

    public function getMaterialTypeLabelAttribute(): string
    {
        return match($this->material_type) {
            'seed' => 'Seeds (Mbegu)',
            'fertilizer' => 'Fertilizer (Mbolea)',
            'pesticide' => 'Pesticide (Dawa ya Wadudu)',
            'herbicide' => 'Herbicide (Dawa ya Magugu)',
            'organic_input' => 'Organic Input',
            'compost' => 'Compost (Mboji)',
            default => $this->material_type,
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date &&
               $this->expiry_date->isBetween(now(), now()->addMonths(3));
    }

    public function getDaysToExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }
        return now()->diffInDays($this->expiry_date, false);
    }

    public function getQuantityDisplayAttribute(): string
    {
        return $this->quantity . ' ' . $this->quantity_unit;
    }

    public function getOrganicStatusAttribute(): string
    {
        return $this->is_organic_approved ? 'Organic Approved' : 'Not Organic';
    }

    // ==================== SCOPES ====================

    public function scopeOfType($query, $type)
    {
        return $query->where('material_type', $type);
    }

    public function scopeSeeds($query)
    {
        return $query->where('material_type', 'seed');
    }

    public function scopeFertilizers($query)
    {
        return $query->where('material_type', 'fertilizer');
    }

    public function scopeOrganicApproved($query)
    {
        return $query->where('is_organic_approved', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', now());
        });
    }

    public function scopeExpiringSoon($query, $months = 3)
    {
        return $query->whereBetween('expiry_date', [now(), now()->addMonths($months)]);
    }
}

<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InputLog extends Model
{
    protected $fillable = [
        'activity_log_id',
        'input_type',
        'product_name',
        'brand',
        'active_ingredient',
        'quantity_applied',
        'quantity_unit',
        'application_rate',
        'rate_unit',
        'area_treated',
        'application_method',
        'target_pest',
        'weather_conditions',
        'pre_harvest_interval',
        'is_organic_approved',
    ];

    protected $casts = [
        'quantity_applied' => 'decimal:2',
        'application_rate' => 'decimal:4',
        'area_treated' => 'decimal:2',
        'pre_harvest_interval' => 'integer',
        'is_organic_approved' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function activityLog(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class);
    }

    // ==================== ACCESSORS ====================

    public function getInputTypeLabelAttribute(): string
    {
        return match($this->input_type) {
            'fertilizer' => 'Fertilizer (Mbolea)',
            'pesticide' => 'Pesticide (Dawa ya Wadudu)',
            'herbicide' => 'Herbicide (Dawa ya Magugu)',
            'fungicide' => 'Fungicide',
            'organic_input' => 'Organic Input',
            'compost' => 'Compost (Mboji)',
            default => $this->input_type,
        };
    }

    public function getApplicationMethodLabelAttribute(): string
    {
        return match($this->application_method) {
            'spray' => 'Spray Application',
            'broadcast' => 'Broadcast',
            'drip' => 'Drip/Fertigation',
            'foliar' => 'Foliar Application',
            'soil_drench' => 'Soil Drench',
            'injection' => 'Injection',
            default => $this->application_method ?? 'Not specified',
        };
    }

    public function getRateDisplayAttribute(): string
    {
        if (!$this->application_rate) {
            return 'Not specified';
        }
        return $this->application_rate . ' ' . ($this->rate_unit ?? '');
    }

    public function getQuantityDisplayAttribute(): string
    {
        return $this->quantity_applied . ' ' . $this->quantity_unit;
    }

    public function getOrganicStatusAttribute(): string
    {
        return $this->is_organic_approved ? 'Organic Approved' : 'Conventional';
    }

    public function getOrganicStatusColorAttribute(): string
    {
        return $this->is_organic_approved ? 'success' : 'warning';
    }

    // ==================== SCOPES ====================

    public function scopeOfInputType($query, $type)
    {
        return $query->where('input_type', $type);
    }

    public function scopeFertilizers($query)
    {
        return $query->where('input_type', 'fertilizer');
    }

    public function scopePesticides($query)
    {
        return $query->where('input_type', 'pesticide');
    }

    public function scopeOrganicApproved($query)
    {
        return $query->where('is_organic_approved', true);
    }

    public function scopeForProduct($query, $productName)
    {
        return $query->where('product_name', 'like', "%{$productName}%");
    }
}

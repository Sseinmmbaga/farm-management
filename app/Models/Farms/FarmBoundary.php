<?php

namespace App\Models\Farms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmBoundary extends Model
{
    protected $fillable = [
        'farm_id',
        'point_order',
        'latitude',
        'longitude',
        'altitude',
        'accuracy',
        'captured_at',
        'captured_by',
    ];

    protected $casts = [
        'point_order' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'altitude' => 'decimal:2',
        'accuracy' => 'decimal:2',
        'captured_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function capturedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captured_by');
    }

    // ==================== ACCESSORS ====================

    public function getCoordinatesAttribute(): string
    {
        return "{$this->latitude}, {$this->longitude}";
    }

    public function getCoordinatesArrayAttribute(): array
    {
        return [$this->longitude, $this->latitude];
    }

    public function getAccuracyLabelAttribute(): string
    {
        if (!$this->accuracy) return 'Unknown';
        if ($this->accuracy <= 5) return 'Excellent';
        if ($this->accuracy <= 10) return 'Good';
        if ($this->accuracy <= 20) return 'Fair';
        return 'Poor';
    }
}

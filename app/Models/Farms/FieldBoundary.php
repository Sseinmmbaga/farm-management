<?php

namespace App\Models\Farms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldBoundary extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'point_order',
        'latitude',
        'longitude',
        'altitude',
        'accuracy',
        'captured_at',
        'captured_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'altitude' => 'decimal:2',
        'accuracy' => 'decimal:2',
        'captured_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function capturedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'captured_by');
    }
}
<?php

namespace App\Models\Stock;

use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBatch extends Model
{
    use HasAuditTrail;

    protected $fillable = [
        'stock_item_id',
        'batch_number',
        'quantity',
        'quantity_remaining',
        'manufacture_date',
        'expiry_date',
        'received_date',
        'supplier',
        'unit_cost',
        'status',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'quantity_remaining' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
        'received_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    public function item(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'received_by');
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return $this->batch_number . ' (' . $this->item?->name . ')';
    }

    public function getExpiryStatusAttribute(): string
    {
        if (!$this->expiry_date) {
            return 'unknown';
        }

        $days = now()->diffInDays($this->expiry_date, false);

        if ($days < 0) {
            return 'expired';
        } elseif ($days <= 30) {
            return 'expiring_soon';
        } else {
            return 'valid';
        }
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_status === 'expired';
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_status === 'expiring_soon';
    }

    // ==================== SCOPES ====================

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where('quantity_remaining', '>', 0);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query)
    {
        return $query->whereBetween('expiry_date', [now(), now()->addDays(30)]);
    }

    public function scopeForItem($query, $stockItemId)
    {
        return $query->where('stock_item_id', $stockItemId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('batch_number', 'like', "%{$search}%")
                ->orWhere('supplier', 'like', "%{$search}%")
                ->orWhereHas('item', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
        });
    }
}
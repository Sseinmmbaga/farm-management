<?php

namespace App\Models\Stock;

use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockRequestItem extends Model
{
    use HasAuditTrail;

    protected $fillable = [
        'stock_request_id',
        'stock_item_id',
        'quantity_requested',
        'quantity_approved',
        'quantity_fulfilled',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'quantity_approved' => 'decimal:2',
        'quantity_fulfilled' => 'decimal:2',
    ];

    // ==================== RELATIONSHIPS ====================

    public function request(): BelongsTo
    {
        return $this->belongsTo(StockRequest::class, 'stock_request_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return $this->item?->name . ' (' . $this->quantity_requested . ' ' . $this->item?->unit . ')';
    }

    public function getPendingApprovalQuantityAttribute(): float
    {
        if ($this->quantity_approved === null) {
            return $this->quantity_requested;
        }
        return max($this->quantity_requested - $this->quantity_approved, 0);
    }

    public function getPendingFulfillmentQuantityAttribute(): float
    {
        if ($this->quantity_approved === null) {
            return 0;
        }
        return max($this->quantity_approved - $this->quantity_fulfilled, 0);
    }

    public function getIsFullyApprovedAttribute(): bool
    {
        return $this->quantity_approved !== null && $this->quantity_approved >= $this->quantity_requested;
    }

    public function getIsFullyFulfilledAttribute(): bool
    {
        return $this->quantity_fulfilled !== null && $this->quantity_fulfilled >= $this->quantity_approved;
    }

    // ==================== SCOPES ====================

    public function scopeForRequest($query, $requestId)
    {
        return $query->where('stock_request_id', $requestId);
    }

    public function scopeForItem($query, $itemId)
    {
        return $query->where('stock_item_id', $itemId);
    }

    public function scopePendingApproval($query)
    {
        return $query->whereNull('quantity_approved')
            ->orWhereColumn('quantity_approved', '<', 'quantity_requested');
    }

    public function scopePendingFulfillment($query)
    {
        return $query->whereNotNull('quantity_approved')
            ->whereColumn('quantity_fulfilled', '<', 'quantity_approved');
    }
}
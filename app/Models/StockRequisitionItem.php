<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockRequisitionItem extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stock_requisition_id',
        'stock_item_id',
        'quantity_requested',
        'quantity_issued',
        'unit_of_measure',
        'unit_price',
        'total_price',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity_requested' => 'decimal:3',
        'quantity_issued' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the requisition that owns this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requisition(): BelongsTo
    {
        return $this->belongsTo(StockRequisition::class, 'stock_requisition_id');
    }

    /**
     * Get the stock item associated with this requisition item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Stock\StockItem::class, 'stock_item_id');
    }

    /**
     * Calculate the total price based on quantity and unit price.
     *
     * @return float
     */
    public function calculateTotalPrice(): float
    {
        if ($this->unit_price && $this->quantity_requested) {
            return $this->unit_price * $this->quantity_requested;
        }
        return 0.0;
    }

    /**
     * Check if the item has been fully issued.
     *
     * @return bool
     */
    public function isFullyIssued(): bool
    {
        if ($this->quantity_issued === null) {
            return false;
        }
        return (float) $this->quantity_issued >= (float) $this->quantity_requested;
    }

    /**
     * Check if the item is partially issued.
     *
     * @return bool
     */
    public function isPartiallyIssued(): bool
    {
        if ($this->quantity_issued === null) {
            return false;
        }
        return (float) $this->quantity_issued > 0 && (float) $this->quantity_issued < (float) $this->quantity_requested;
    }

    /**
     * Get the remaining quantity to be issued.
     *
     * @return float
     */
    public function getRemainingQuantityAttribute(): float
    {
        $requested = (float) $this->quantity_requested;
        $issued = (float) $this->quantity_issued ?? 0;
        return max(0, $requested - $issued);
    }

    /**
     * Get the issued percentage.
     *
     * @return float
     */
    public function getIssuedPercentageAttribute(): float
    {
        if ($this->quantity_requested == 0) {
            return 0.0;
        }
        $issued = (float) $this->quantity_issued ?? 0;
        return ($issued / (float) $this->quantity_requested) * 100;
    }

    /**
     * Scope a query to only include items with remaining quantity.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRemaining($query)
    {
        return $query->whereRaw('quantity_issued IS NULL OR quantity_issued < quantity_requested');
    }

    /**
     * Scope a query to only include fully issued items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFullyIssued($query)
    {
        return $query->whereRaw('quantity_issued >= quantity_requested');
    }

    /**
     * Scope a query to only include items for a specific requisition.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|StockRequisition  $requisition
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForRequisition($query, $requisition)
    {
        $requisitionId = $requisition instanceof StockRequisition ? $requisition->id : $requisition;
        return $query->where('stock_requisition_id', $requisitionId);
    }

    /**
     * Scope a query to only include items for a specific stock item.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|\App\Models\Stock\StockItem  $stockItem
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForStockItem($query, $stockItem)
    {
        $stockItemId = $stockItem instanceof \App\Models\Stock\StockItem ? $stockItem->id : $stockItem;
        return $query->where('stock_item_id', $stockItemId);
    }
}

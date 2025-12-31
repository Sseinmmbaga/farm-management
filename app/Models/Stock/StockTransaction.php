<?php

namespace App\Models\Stock;

use App\Enums\StockTransactionType;
use App\Models\User;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    use HasAuditTrail;

    protected $fillable = [
        'stock_item_id',
        'stock_batch_id',
        'transaction_type',
        'reference_number',
        'quantity',
        'unit_cost',
        'total_cost',
        'balance_before',
        'balance_after',
        'transaction_date',
        'source',
        'destination',
        'farmer_id',
        'farm_id',
        'document_number',
        'document_type',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'transaction_type' => StockTransactionType::class,
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(StockBatch::class, 'stock_batch_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==================== ACCESSORS ====================

    public function getTypeLabelAttribute(): string
    {
        return $this->transaction_type->label();
    }

    public function getIsIncomingAttribute(): bool
    {
        return $this->transaction_type->isIncoming();
    }

    public function getIsOutgoingAttribute(): bool
    {
        return $this->transaction_type->isOutgoing();
    }

    public function getQuantityDisplayAttribute(): string
    {
        $prefix = $this->is_incoming ? '+' : '-';
        return $prefix . number_format($this->quantity, 2);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'completed' => 'success',
            default => 'secondary',
        };
    }

    // ==================== SCOPES ====================

    public function scopeOfType($query, StockTransactionType $type)
    {
        return $query->where('transaction_type', $type->value);
    }

    public function scopeIntakes($query)
    {
        return $query->where('transaction_type', StockTransactionType::INTAKE->value);
    }

    public function scopeIssuances($query)
    {
        return $query->where('transaction_type', StockTransactionType::ISSUANCE->value);
    }

    public function scopeDistributions($query)
    {
        return $query->where('transaction_type', StockTransactionType::DISTRIBUTION->value);
    }

    public function scopeForItem($query, $itemId)
    {
        return $query->where('stock_item_id', $itemId);
    }

    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('transaction_date', [$start, $end]);
    }

    // ==================== HELPERS ====================

    public static function generateReferenceNumber(): string
    {
        $prefix = 'TXN';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return $prefix . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

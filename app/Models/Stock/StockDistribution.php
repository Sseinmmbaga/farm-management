<?php

namespace App\Models\Stock;

use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Models\Farms\Season;
use App\Models\User;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockDistribution extends Model
{
    use HasAuditTrail;

    protected $fillable = [
        'stock_transaction_id',
        'farmer_id',
        'farm_id',
        'season_id',
        'distribution_type',
        'quantity',
        'value',
        'is_repaid',
        'amount_repaid',
        'due_date',
        'purpose',
        'acknowledgement_signature',
        'distributed_by',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'value' => 'decimal:2',
        'amount_repaid' => 'decimal:2',
        'is_repaid' => 'boolean',
        'due_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(StockTransaction::class, 'stock_transaction_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function distributedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return 'Distribution #' . $this->id . ' - ' . $this->farmer?->full_name;
    }

    public function getDistributionTypeDisplayAttribute(): string
    {
        return match($this->distribution_type) {
            'credit' => 'Credit',
            'cash' => 'Cash',
            'free' => 'Free',
            default => ucfirst($this->distribution_type),
        };
    }

    public function getOutstandingAmountAttribute(): float
    {
        return max($this->value - $this->amount_repaid, 0);
    }

    public function getIsFullyRepaidAttribute(): bool
    {
        return $this->is_repaid || $this->outstanding_amount <= 0;
    }

    public function getPurposeDisplayAttribute(): string
    {
        $purposes = [
            'seeding' => 'Seeding',
            'fertilizer_application' => 'Fertilizer Application',
            'pest_control' => 'Pest Control',
            'harvest' => 'Harvest',
            'other' => 'Other',
        ];

        return $purposes[$this->purpose] ?? ucfirst(str_replace('_', ' ', $this->purpose));
    }

    // ==================== SCOPES ====================

    public function scopeCredit($query)
    {
        return $query->where('distribution_type', 'credit');
    }

    public function scopeCash($query)
    {
        return $query->where('distribution_type', 'cash');
    }

    public function scopeFree($query)
    {
        return $query->where('distribution_type', 'free');
    }

    public function scopeUnrepaid($query)
    {
        return $query->where('is_repaid', false)
            ->where('distribution_type', 'credit');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('is_repaid', false)
            ->where('distribution_type', 'credit');
    }

    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    public function scopeForSeason($query, $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('farmer', function ($q2) use ($search) {
                $q2->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            })->orWhereHas('transaction', function ($q2) use ($search) {
                $q2->where('reference_number', 'like', "%{$search}%");
            });
        });
    }
}
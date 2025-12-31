<?php

namespace App\Models;

use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialRequest extends Model
{
    use HasAuditTrail, SoftDeletes;

    protected $fillable = [
        'request_number',
        'user_id',
        'type',
        'amount',
        'currency',
        'purpose',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'disbursed_by',
        'disbursed_at',
        'disbursement_notes',
        'repayment_schedule',
        'total_repayment_amount',
        'repayment_start_date',
        'repayment_end_date',
        'repayment_installments',
        'amount_repaid',
        'last_repayment_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'repayment_schedule' => 'array',
        'total_repayment_amount' => 'decimal:2',
        'repayment_start_date' => 'date',
        'repayment_end_date' => 'date',
        'amount_repaid' => 'decimal:2',
        'last_repayment_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function disbursedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return 'Financial Request #' . $this->request_number;
    }

    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'disbursed' => 'Disbursed',
            'repaid' => 'Fully Repaid',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'disbursed' => 'success',
            'repaid' => 'secondary',
            'cancelled' => 'dark',
            default => 'light',
        };
    }

    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'loan' => 'Loan',
            'salary_advance' => 'Salary Advance',
            'imprest' => 'Imprest',
            'other' => 'Other',
            default => ucfirst($this->type),
        };
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getIsDisbursedAttribute(): bool
    {
        return $this->status === 'disbursed';
    }

    public function getIsRepaidAttribute(): bool
    {
        return $this->status === 'repaid';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_repayment_amount ? ($this->total_repayment_amount - $this->amount_repaid) : 0;
    }

    public function getIsFullyRepaidAttribute(): bool
    {
        return $this->balance <= 0 && $this->total_repayment_amount > 0;
    }

    public function getCurrencySymbolAttribute(): string
    {
        return match($this->currency) {
            'TZS' => 'TSh',
            'USD' => '$',
            'EUR' => '€',
            default => $this->currency,
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->currency_symbol . ' ' . number_format($this->amount, 2);
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDisbursed($query)
    {
        return $query->where('status', 'disbursed');
    }

    public function scopeRepaid($query)
    {
        return $query->where('status', 'repaid');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAmountRange($query, $min, $max)
    {
        return $query->whereBetween('amount', [$min, $max]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('request_number', 'like', "%{$search}%")
                ->orWhere('purpose', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        });
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = static::generateRequestNumber();
            }
        });
    }

    public static function generateRequestNumber(): string
    {
        $prefix = 'FR-';
        $year = date('Y');
        $month = date('m');
        $latest = static::where('request_number', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('request_number', 'desc')
            ->first();

        if ($latest) {
            $lastSeq = intval(substr($latest->request_number, -4));
            $seq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $seq = '0001';
        }

        return "{$prefix}{$year}{$month}{$seq}";
    }
}
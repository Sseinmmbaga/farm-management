<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class StockRequisition extends Model
{
    use SoftDeletes;

    /**
     * The status constants.
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ISSUED = 'issued';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PARTIALLY_ISSUED = 'partially_issued';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'requisition_number',
        'user_id',
        'department_id',
        'status',
        'requested_date',
        'required_date',
        'purpose',
        'estimated_total',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'issued_by',
        'issued_at',
        'issue_notes',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requested_date' => 'date',
        'required_date' => 'date',
        'estimated_total' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'issued_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->requisition_number)) {
                $model->requisition_number = static::generateRequisitionNumber();
            }
        });
    }

    /**
     * Generate a unique requisition number.
     *
     * @return string
     */
    public static function generateRequisitionNumber(): string
    {
        $prefix = 'SR';
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $latest = static::where('requisition_number', 'like', "{$prefix}{$year}{$month}{$day}%")
            ->orderBy('requisition_number', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->requisition_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "{$prefix}{$year}{$month}{$day}{$nextNumber}";
    }

    /**
     * Get the user who created the requisition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department associated with the requisition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who approved the requisition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected the requisition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the user who issued the requisition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the items for the requisition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockRequisitionItem::class, 'stock_requisition_id');
    }

    /**
     * Scope a query to only include requisitions of a specific status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|array  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        if (is_array($status)) {
            return $query->whereIn('status', $status);
        }
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include requisitions for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|User  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $user)
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include requisitions for a specific department.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|Department  $department
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDepartment($query, $department)
    {
        $departmentId = $department instanceof Department ? $department->id : $department;
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to only include requisitions created between dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $start
     * @param  string  $end
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedBetween($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope a query to only include requisitions required by a specific date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiredBy($query, $date)
    {
        return $query->whereDate('required_date', '<=', $date);
    }

    /**
     * Check if the requisition is in draft status.
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if the requisition is pending approval.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the requisition is approved.
     *
     * @return bool
    */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the requisition is rejected.
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if the requisition is issued.
     *
     * @return bool
     */
    public function isIssued(): bool
    {
        return $this->status === self::STATUS_ISSUED;
    }

    /**
     * Check if the requisition is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if the requisition is partially issued.
     *
     * @return bool
     */
    public function isPartiallyIssued(): bool
    {
        return $this->status === self::STATUS_PARTIALLY_ISSUED;
    }

    /**
     * Check if the requisition can be edited.
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PENDING]);
    }

    /**
     * Check if the requisition can be approved.
     *
     * @return bool
     */
    public function canBeApproved(): bool
    {
        return $this->isPending();
    }

    /**
     * Check if the requisition can be rejected.
     *
     * @return bool
     */
    public function canBeRejected(): bool
    {
        return $this->isPending();
    }

    /**
     * Check if the requisition can be issued.
     *
     * @return bool
     */
    public function canBeIssued(): bool
    {
        return $this->isApproved() || $this->isPartiallyIssued();
    }

    /**
     * Check if the requisition can be cancelled.
     *
     * @return bool
     */
    public function canBeCancelled(): bool
    {
        return !in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_ISSUED]);
    }

    /**
     * Get the display label for the status.
     *
     * @return string
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_ISSUED => 'Issued',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_PARTIALLY_ISSUED => 'Partially Issued',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get the CSS color class for the status.
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_ISSUED => 'info',
            self::STATUS_CANCELLED => 'dark',
            self::STATUS_PARTIALLY_ISSUED => 'primary',
            default => 'light',
        };
    }

    /**
     * Get the total quantity requested across all items.
     *
     * @return float
     */
    public function getTotalQuantityRequestedAttribute(): float
    {
        return $this->items->sum('quantity_requested');
    }

    /**
     * Get the total quantity issued across all items.
     *
     * @return float
     */
    public function getTotalQuantityIssuedAttribute(): float
    {
        return $this->items->sum('quantity_issued');
    }

    /**
     * Get the total estimated price across all items.
     *
     * @return float
     */
    public function getTotalEstimatedPriceAttribute(): float
    {
        return $this->items->sum('total_price');
    }
}

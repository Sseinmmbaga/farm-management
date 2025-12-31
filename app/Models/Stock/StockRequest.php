<?php

namespace App\Models\Stock;

use App\Models\Farmers\Farmer;
use App\Models\Farms\Season;
use App\Models\User;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockRequest extends Model
{
    use HasAuditTrail;

    protected $fillable = [
        'request_number',
        'requested_by',
        'farmer_id',
        'season_id',
        'status',
        'priority',
        'needed_by',
        'purpose',
        'notes',
        'approved_by',
        'approved_at',
        'approval_notes',
        'fulfilled_by',
        'fulfilled_at',
    ];

    protected $casts = [
        'needed_by' => 'date',
        'approved_at' => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function fulfilledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockRequestItem::class);
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return 'Request #' . $this->request_number;
    }

    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'fulfilled' => 'Fulfilled',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'fulfilled' => 'primary',
            'cancelled' => 'warning',
            default => 'light',
        };
    }

    public function getPriorityDisplayAttribute(): string
    {
        return match($this->priority) {
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
            default => ucfirst($this->priority),
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'secondary',
            'normal' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'light',
        };
    }

    public function getTotalRequestedQuantityAttribute(): float
    {
        return $this->items->sum('quantity_requested');
    }

    public function getTotalApprovedQuantityAttribute(): float
    {
        return $this->items->sum('quantity_approved');
    }

    public function getTotalFulfilledQuantityAttribute(): float
    {
        return $this->items->sum('quantity_fulfilled');
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->status === 'draft';
    }

    public function getIsSubmittedAttribute(): bool
    {
        return $this->status === 'submitted';
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getIsFulfilledAttribute(): bool
    {
        return $this->status === 'fulfilled';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    // ==================== SCOPES ====================

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeFulfilled($query)
    {
        return $query->where('status', 'fulfilled');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    public function scopeForSeason($query, $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('request_number', 'like', "%{$search}%")
                ->orWhere('purpose', 'like', "%{$search}%")
                ->orWhereHas('farmer', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%");
                });
        });
    }
}
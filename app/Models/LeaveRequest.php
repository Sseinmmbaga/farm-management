<?php

namespace App\Models;

use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasAuditTrail, SoftDeletes;

    protected $fillable = [
        'request_number',
        'user_id',
        'type',
        'start_date',
        'end_date',
        'days',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
        'is_sick_sheet',
        'sick_sheet_file',
        'leave_balance_before',
        'leave_balance_after',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'is_sick_sheet' => 'boolean',
        'leave_balance_before' => 'decimal:2',
        'leave_balance_after' => 'decimal:2',
        'days' => 'integer',
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

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return 'Leave Request #' . $this->request_number;
    }

    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            'taken' => 'Taken',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            'taken' => 'info',
            default => 'light',
        };
    }

    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'annual' => 'Annual Leave',
            'sick' => 'Sick Leave',
            'personal' => 'Personal Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'study' => 'Study Leave',
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

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getIsTakenAttribute(): bool
    {
        return $this->status === 'taken';
    }

    public function getDurationAttribute(): string
    {
        $days = $this->days;
        if ($days == 1) {
            return '1 day';
        }
        return $days . ' days';
    }

    public function getHasSickSheetAttribute(): bool
    {
        return $this->is_sick_sheet && !empty($this->sick_sheet_file);
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

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeTaken($query)
    {
        return $query->where('status', 'taken');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForApprover($query, $userId)
    {
        return $query->where('approved_by', $userId)
            ->orWhere('rejected_by', $userId)
            ->orWhere('cancelled_by', $userId);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
                ->orWhereBetween('end_date', [$start, $end])
                ->orWhere(function ($q2) use ($start, $end) {
                    $q2->where('start_date', '<=', $start)
                        ->where('end_date', '>=', $end);
                });
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('request_number', 'like', "%{$search}%")
                ->orWhere('reason', 'like', "%{$search}%")
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
            // Calculate days if not set
            if (empty($model->days) && $model->start_date && $model->end_date) {
                $model->days = $model->start_date->diffInDays($model->end_date) + 1;
            }
        });

        static::updating(function ($model) {
            // Recalculate days if dates changed
            if ($model->isDirty(['start_date', 'end_date'])) {
                $model->days = $model->start_date->diffInDays($model->end_date) + 1;
            }
        });
    }

    public static function generateRequestNumber(): string
    {
        $prefix = 'LR-';
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
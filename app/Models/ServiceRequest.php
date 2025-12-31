<?php

namespace App\Models;

use App\Models\Farmers\Farmer;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use HasAuditTrail, SoftDeletes;

    protected $fillable = [
        'request_number',
        'farmer_id',
        'type',
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'requested_by',
        'resolved_at',
        'resolved_notes',
        'notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return 'Service Request #' . $this->request_number;
    }

    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open' => 'info',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            'cancelled' => 'danger',
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

    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'support' => 'Support',
            'training' => 'Training',
            'maintenance' => 'Maintenance',
            'other' => 'Other',
            default => ucfirst($this->type),
        };
    }

    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'open';
    }

    public function getIsInProgressAttribute(): bool
    {
        return $this->status === 'in_progress';
    }

    public function getIsResolvedAttribute(): bool
    {
        return $this->status === 'resolved';
    }

    public function getIsClosedAttribute(): bool
    {
        return $this->status === 'closed';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    // ==================== SCOPES ====================

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    public function scopeForAssignedUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeForRequestedUser($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('request_number', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('farmer', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%");
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
        $prefix = 'SR-';
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

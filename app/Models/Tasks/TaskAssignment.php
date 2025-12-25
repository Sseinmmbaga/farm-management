<?php

namespace App\Models\Tasks;

use App\Models\User;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TaskAssignment extends Model
{
    use HasFactory, SoftDeletes, HasAuditTrail;

    protected $fillable = [
        'task_id',
        'assignee_type',
        'assignee_id',
        'assigned_by',
        'assigned_at',
        'accepted_at',
        'started_at',
        'completed_at',
        'status',
        'notes',
        'rejection_reason',
        'estimated_hours',
        'actual_hours',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    // ==================== RELATIONSHIPS ====================

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function assignee(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // ==================== ACCESSORS ====================

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending Acceptance',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'accepted' => 'info',
            'rejected' => 'danger',
            'in_progress' => 'primary',
            'completed' => 'success',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    public function getAssigneeNameAttribute(): ?string
    {
        return $this->assignee?->name ?? $this->assignee?->full_name ?? 'Unknown';
    }

    public function getAssignedByNameAttribute(): ?string
    {
        return $this->assignedByUser?->name;
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForAssignee($query, $assigneeType, $assigneeId)
    {
        return $query->where('assignee_type', $assigneeType)
                     ->where('assignee_id', $assigneeId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('assignee_type', User::class)
                     ->where('assignee_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'accepted', 'in_progress']);
    }

    // ==================== HELPERS ====================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function accept(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'accepted';
        $this->accepted_at = now();
        return $this->save();
    }

    public function reject(string $reason): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        return $this->save();
    }

    public function start(): bool
    {
        if (!in_array($this->status, ['pending', 'accepted'])) {
            return false;
        }

        $this->status = 'in_progress';
        $this->started_at = now();
        if (!$this->accepted_at) {
            $this->accepted_at = now();
        }
        return $this->save();
    }

    public function complete(?float $actualHours = null): bool
    {
        if ($this->status !== 'in_progress') {
            return false;
        }

        $this->status = 'completed';
        $this->completed_at = now();
        if ($actualHours !== null) {
            $this->actual_hours = $actualHours;
        }
        return $this->save();
    }

    public function cancel(): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }

        $this->status = 'cancelled';
        return $this->save();
    }
}

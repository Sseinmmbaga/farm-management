<?php

namespace App\Models\Tasks;

use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskType;
use App\Models\User;
use App\Models\Farms\Farm;
use App\Models\Farms\Field;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Season;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory, SoftDeletes, HasAuditTrail;

    protected $fillable = [
        'code',
        'title',
        'description',
        'type',
        'status',
        'priority',
        'farm_id',
        'field_id',
        'farmer_id',
        'season_id',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'estimated_hours',
        'actual_hours',
        'estimated_cost',
        'actual_cost',
        'notes',
        'completion_notes',
        'equipment_required',
        'materials_required',
        'assigned_by',
        'completed_by',
        'completed_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'is_recurring',
        'recurrence_pattern',
        'parent_task_id',
    ];

    protected $casts = [
        'type' => TaskType::class,
        'status' => TaskStatus::class,
        'priority' => TaskPriority::class,
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'datetime',
        'actual_end_date' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'equipment_required' => 'array',
        'materials_required' => 'array',
        'recurrence_pattern' => 'array',
        'is_recurring' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function laborRecords(): HasMany
    {
        return $this->hasMany(Labor::class);
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return $this->title ?? "Task {$this->code}";
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getStatusIconAttribute(): string
    {
        return $this->status->icon();
    }

    public function getPriorityLabelAttribute(): string
    {
        return $this->priority->label();
    }

    public function getPriorityColorAttribute(): string
    {
        return $this->priority->color();
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    public function getTypeIconAttribute(): string
    {
        return $this->type->icon();
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === TaskStatus::COMPLETED || $this->status === TaskStatus::CANCELLED) {
            return false;
        }

        return $this->planned_end_date && now()->startOfDay()->greaterThan($this->planned_end_date);
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->planned_end_date) {
            return null;
        }

        return now()->diffInDays($this->planned_end_date, false);
    }

    public function getDurationDaysAttribute(): ?int
    {
        if (!$this->planned_start_date || !$this->planned_end_date) {
            return null;
        }

        return $this->planned_start_date->diffInDays($this->planned_end_date);
    }

    public function getActualDurationAttribute(): ?int
    {
        if (!$this->actual_start_date || !$this->actual_end_date) {
            return null;
        }

        return $this->actual_start_date->diffInDays($this->actual_end_date);
    }

    public function getProgressPercentageAttribute(): int
    {
        return match($this->status) {
            TaskStatus::PENDING => 0,
            TaskStatus::ASSIGNED => 10,
            TaskStatus::IN_PROGRESS => 50,
            TaskStatus::ON_HOLD => 50,
            TaskStatus::COMPLETED => 100,
            TaskStatus::CANCELLED => 0,
            TaskStatus::OVERDUE => 50,
        };
    }

    public function getTotalLaborHoursAttribute(): float
    {
        return $this->laborRecords()->sum('hours_worked');
    }

    public function getTotalLaborCostAttribute(): float
    {
        return $this->laborRecords()->sum('total_cost');
    }

    public function getAssigneeNamesAttribute(): string
    {
        return $this->assignments()
            ->with('assignee')
            ->get()
            ->pluck('assignee.name')
            ->filter()
            ->implode(', ');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->whereIn('status', TaskStatus::activeStatuses());
    }

    public function scopePending($query)
    {
        return $query->where('status', TaskStatus::PENDING);
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', TaskStatus::ASSIGNED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', TaskStatus::IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TaskStatus::COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', TaskStatus::CANCELLED);
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', TaskStatus::activeStatuses())
            ->whereNotNull('planned_end_date')
            ->where('planned_end_date', '<', now()->startOfDay());
    }

    public function scopeUpcoming($query, int $days = 7)
    {
        return $query->whereIn('status', TaskStatus::activeStatuses())
            ->whereNotNull('planned_start_date')
            ->whereBetween('planned_start_date', [now()->startOfDay(), now()->addDays($days)->endOfDay()]);
    }

    public function scopeDueToday($query)
    {
        return $query->whereIn('status', TaskStatus::activeStatuses())
            ->whereDate('planned_end_date', now()->toDateString());
    }

    public function scopeForFarm($query, $farmId)
    {
        return $query->where('farm_id', $farmId);
    }

    public function scopeForField($query, $fieldId)
    {
        return $query->where('field_id', $fieldId);
    }

    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    public function scopeForSeason($query, $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    public function scopeOfType($query, TaskType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfPriority($query, TaskPriority $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', TaskPriority::URGENT);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [TaskPriority::HIGH, TaskPriority::URGENT]);
    }

    public function scopeAssignedToUser($query, $userId)
    {
        return $query->whereHas('assignments', function ($q) use ($userId) {
            $q->where('assignee_id', $userId)->where('assignee_type', User::class);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    public function scopeOrderByPriority($query)
    {
        return $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')");
    }

    // ==================== HELPERS ====================

    public function isPending(): bool
    {
        return $this->status === TaskStatus::PENDING;
    }

    public function isAssigned(): bool
    {
        return $this->status === TaskStatus::ASSIGNED;
    }

    public function isInProgress(): bool
    {
        return $this->status === TaskStatus::IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this->status === TaskStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === TaskStatus::CANCELLED;
    }

    public function isOnHold(): bool
    {
        return $this->status === TaskStatus::ON_HOLD;
    }

    public function canBeStarted(): bool
    {
        return in_array($this->status, [TaskStatus::PENDING, TaskStatus::ASSIGNED, TaskStatus::ON_HOLD]);
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, [TaskStatus::IN_PROGRESS, TaskStatus::ON_HOLD]);
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, [TaskStatus::COMPLETED, TaskStatus::CANCELLED]);
    }

    public static function generateCode(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;

        return "TSK{$year}" . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function start(): bool
    {
        if (!$this->canBeStarted()) {
            return false;
        }

        $this->status = TaskStatus::IN_PROGRESS;
        $this->actual_start_date = now();
        return $this->save();
    }

    public function complete(?string $notes = null): bool
    {
        if (!$this->canBeCompleted()) {
            return false;
        }

        $this->status = TaskStatus::COMPLETED;
        $this->actual_end_date = now();
        $this->completed_at = now();
        $this->completed_by = auth()->id();
        $this->completion_notes = $notes;
        return $this->save();
    }

    public function cancel(string $reason): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->status = TaskStatus::CANCELLED;
        $this->cancelled_at = now();
        $this->cancelled_by = auth()->id();
        $this->cancellation_reason = $reason;
        return $this->save();
    }

    public function putOnHold(): bool
    {
        if ($this->status !== TaskStatus::IN_PROGRESS) {
            return false;
        }

        $this->status = TaskStatus::ON_HOLD;
        return $this->save();
    }

    public function resume(): bool
    {
        if ($this->status !== TaskStatus::ON_HOLD) {
            return false;
        }

        $this->status = TaskStatus::IN_PROGRESS;
        return $this->save();
    }

    public function assignTo($assignee, ?User $assignedBy = null): TaskAssignment
    {
        $assignment = $this->assignments()->create([
            'assignee_type' => get_class($assignee),
            'assignee_id' => $assignee->id,
            'assigned_by' => $assignedBy?->id ?? auth()->id(),
            'assigned_at' => now(),
            'status' => 'pending',
        ]);

        if ($this->status === TaskStatus::PENDING) {
            $this->status = TaskStatus::ASSIGNED;
            $this->assigned_by = $assignedBy?->id ?? auth()->id();
            $this->save();
        }

        return $assignment;
    }
}

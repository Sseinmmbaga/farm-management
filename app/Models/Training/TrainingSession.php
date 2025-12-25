<?php

namespace App\Models\Training;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    protected $table = 'training_sessions';

    protected $fillable = [
        'training_program_id',
        'season_id',
        'title',
        'description',
        'scheduled_date',
        'end_date',
        'duration_hours',
        'venue',
        'region_id',
        'district_id',
        'village_id',
        'latitude',
        'longitude',
        'trainer_id',
        'trainer_name',
        'trainer_organization',
        'trainer_contact',
        'max_participants',
        'registered_count',
        'attended_count',
        'status',
        'cancellation_reason',
        'materials',
        'equipment_needed',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'end_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'materials' => 'array',
        'equipment_needed' => 'array',
    ];

    // ==================== RELATIONSHIPS ====================

    public function program(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(TrainingAttendance::class);
    }

    // ==================== SCOPES ====================

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_date', '>=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // ==================== ACCESSORS ====================

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'Scheduled',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'postponed' => 'Postponed',
            default => $this->status,
        };
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_date >= now();
    }

    public function getIsPastAttribute(): bool
    {
        return $this->scheduled_date < now();
    }

    public function getAvailableSlotsAttribute(): ?int
    {
        if ($this->max_participants === null) {
            return null;
        }
        return max(0, $this->max_participants - $this->registered_count);
    }

    public function getAttendanceRateAttribute(): float
    {
        if ($this->registered_count === 0) {
            return 0;
        }
        return ($this->attended_count / $this->registered_count) * 100;
    }
}
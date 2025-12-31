<?php

namespace App\Models\Training;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingProgram extends Model
{
    protected $table = 'training_programs';

    protected $fillable = [
        'name',
        'name_sw',
        'code',
        'description',
        'objectives',
        'category',
        'duration_hours',
        'is_mandatory',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_mandatory' => 'boolean',
        'duration_hours' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class, 'training_program_id');
    }

    public function attendances()
    {
        return $this->hasManyThrough(
            TrainingAttendance::class,
            TrainingSession::class,
            'training_program_id', // Foreign key on TrainingSession
            'training_session_id', // Foreign key on TrainingAttendance
            'id', // Local key on TrainingProgram
            'id'  // Local key on TrainingSession
        );
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ==================== ACCESSORS ====================

    public function getTotalSessionsAttribute(): int
    {
        return $this->sessions()->count();
    }

    public function getTotalParticipantsAttribute(): int
    {
        return $this->sessions()->sum('registered_count');
    }

    public function getTotalAttendeesAttribute(): int
    {
        return $this->sessions()->sum('attended_count');
    }
}
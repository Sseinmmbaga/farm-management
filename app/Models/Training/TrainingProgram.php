<?php

namespace App\Models\Training;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingProgram extends Model
{
    protected $table = 'training_programs';

    protected $fillable = [
        'name',
        'description',
        'category',
        'target_audience',
        'duration_days',
        'is_active',
        'objectives',
        'curriculum',
        'prerequisites',
        'materials_provided',
        'certification_offered',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_days' => 'integer',
        'objectives' => 'array',
        'curriculum' => 'array',
        'prerequisites' => 'array',
        'materials_provided' => 'array',
    ];

    // ==================== RELATIONSHIPS ====================

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class, 'training_program_id');
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
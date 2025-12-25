<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingLog extends Model
{
    protected $fillable = [
        'activity_log_id',
        'training_type',
        'topic',
        'learning_objectives',
        'trainer_name',
        'trainer_organization',
        'duration_hours',
        'attendees_count',
        'male_attendees',
        'female_attendees',
        'venue',
        'materials_used',
        'feedback',
        'certificates_issued',
    ];

    protected $casts = [
        'duration_hours' => 'integer',
        'attendees_count' => 'integer',
        'male_attendees' => 'integer',
        'female_attendees' => 'integer',
        'materials_used' => 'array',
        'certificates_issued' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function activityLog(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class);
    }

    // ==================== ACCESSORS ====================

    public function getTrainingTypeLabelAttribute(): string
    {
        return match($this->training_type) {
            'workshop' => 'Workshop',
            'field_day' => 'Field Day',
            'demonstration' => 'Demonstration',
            'lecture' => 'Lecture/Classroom',
            'on_farm' => 'On-Farm Training',
            'online' => 'Online/Virtual',
            default => $this->training_type,
        };
    }

    public function getGenderRatioAttribute(): string
    {
        if (!$this->male_attendees && !$this->female_attendees) {
            return 'Not recorded';
        }
        return "M: {$this->male_attendees}, F: {$this->female_attendees}";
    }

    public function getFemalePercentageAttribute(): ?float
    {
        if (!$this->attendees_count) {
            return null;
        }
        return round(($this->female_attendees / $this->attendees_count) * 100, 1);
    }

    public function getDurationDisplayAttribute(): string
    {
        if (!$this->duration_hours) {
            return 'Not specified';
        }
        return $this->duration_hours . ' hour' . ($this->duration_hours > 1 ? 's' : '');
    }

    // ==================== SCOPES ====================

    public function scopeOfType($query, $type)
    {
        return $query->where('training_type', $type);
    }

    public function scopeWorkshops($query)
    {
        return $query->where('training_type', 'workshop');
    }

    public function scopeFieldDays($query)
    {
        return $query->where('training_type', 'field_day');
    }

    public function scopeWithCertificates($query)
    {
        return $query->where('certificates_issued', true);
    }

    public function scopeByTrainer($query, $trainerName)
    {
        return $query->where('trainer_name', 'like', "%{$trainerName}%");
    }
}

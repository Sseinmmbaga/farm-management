<?php

namespace App\Models\ICS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class CorrectiveAction extends Model
{
    use HasFactory;

    protected $table = 'corrective_actions';

    protected $fillable = [
        'finding_id',
        'action_number',
        'description',
        'description_sw',
        'action_type',
        'planned_date',
        'completion_date',
        'responsible_person_id',
        'responsible_person_name',
        'evidence_of_completion',
        'images',
        'status',
        'verification_notes',
        'verified_by',
        'verified_at',
        'is_effective',
        'created_by',
    ];

    protected $casts = [
        'planned_date' => 'datetime',
        'completion_date' => 'date',
        'verified_at' => 'datetime',
        'images' => 'array',
        'is_effective' => 'boolean',
    ];

    /**
     * Get the finding that owns the corrective action.
     */
    public function finding(): BelongsTo
    {
        return $this->belongsTo(InspectionFinding::class);
    }

    /**
     * Get the responsible person.
     */
    public function responsiblePerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_person_id');
    }

    /**
     * Get the user who verified the corrective action.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the user who created the corrective action.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include planned actions.
     */
    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    /**
     * Scope a query to only include in-progress actions.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed actions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get the action type label.
     */
    public function getActionTypeLabelAttribute(): string
    {
        return str_replace('_', ' ', ucfirst($this->action_type));
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return str_replace('_', ' ', ucfirst($this->status));
    }

    /**
     * Get the status color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planned' => 'info',
            'in_progress' => 'warning',
            'completed' => 'success',
            'verified' => 'primary',
            'ineffective' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Check if the action is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->planned_date && $this->planned_date->isPast() && !in_array($this->status, ['completed', 'verified']);
    }
}
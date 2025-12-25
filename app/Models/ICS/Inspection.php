<?php

namespace App\Models\ICS;

use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inspection extends Model
{
    protected $table = 'inspections';

    protected $fillable = [
        'inspection_checklist_id',
        'farmer_id',
        'farm_id',
        'season_id',
        'inspection_number',
        'inspector_id',
        'inspector_name',
        'inspector_organization',
        'scheduled_date',
        'inspection_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'latitude',
        'longitude',
        'status',
        'result',
        'total_score',
        'max_possible_score',
        'percentage_score',
        'items_checked',
        'items_compliant',
        'items_non_compliant',
        'items_na',
        'critical_failures',
        'summary',
        'observations',
        'recommendations',
        'images',
        'requires_follow_up',
        'follow_up_date',
        'follow_up_inspection_id',
        'farmer_signature',
        'inspector_signature',
        'signed_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'inspection_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_score' => 'decimal:2',
        'max_possible_score' => 'decimal:2',
        'percentage_score' => 'decimal:2',
        'images' => 'array',
        'requires_follow_up' => 'boolean',
        'follow_up_date' => 'date',
        'signed_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // ==================== RELATIONSHIPS ====================

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(InspectionChecklist::class, 'inspection_checklist_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function followUpInspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'follow_up_inspection_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(Inspection::class, 'follow_up_inspection_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(InspectionFinding::class);
    }

    // ==================== SCOPES ====================

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('result', 'pending');
    }

    public function scopePassed($query)
    {
        return $query->where('result', 'passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('result', 'failed');
    }

    public function scopeRequiresFollowUp($query)
    {
        return $query->where('requires_follow_up', true);
    }

    // ==================== ACCESSORS ====================

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'Scheduled',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => $this->status,
        };
    }

    public function getResultLabelAttribute(): string
    {
        return match($this->result) {
            'pending' => 'Pending',
            'passed' => 'Passed',
            'failed' => 'Failed',
            'conditional' => 'Conditional',
            default => $this->result,
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->scheduled_date || $this->status !== 'scheduled') {
            return false;
        }
        return $this->scheduled_date->isPast();
    }

    public function getHasFollowUpAttribute(): bool
    {
        return $this->followUps()->exists();
    }
}
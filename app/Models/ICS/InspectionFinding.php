<?php

namespace App\Models\ICS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionFinding extends Model
{
    protected $table = 'findings';

    protected $fillable = [
        'inspection_id',
        'checklist_item_id',
        'finding_type',
        'description',
        'severity',
        'evidence',
        'recommendation',
        'due_date',
        'status',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'resolved_at' => 'datetime',
        'evidence' => 'array',
    ];

    // ==================== RELATIONSHIPS ====================

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class, 'checklist_item_id');
    }

    public function correctiveActions(): HasMany
    {
        return $this->hasMany(CorrectiveAction::class, 'finding_id');
    }

    // ==================== SCOPES ====================

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeMajor($query)
    {
        return $query->where('severity', 'major');
    }

    public function scopeMinor($query)
    {
        return $query->where('severity', 'minor');
    }

    // ==================== ACCESSORS ====================

    public function getSeverityLabelAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'Critical',
            'major' => 'Major',
            'minor' => 'Minor',
            'observation' => 'Observation',
            default => $this->severity,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => $this->status,
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date || $this->status === 'resolved' || $this->status === 'closed') {
            return false;
        }
        return $this->due_date->isPast();
    }
}

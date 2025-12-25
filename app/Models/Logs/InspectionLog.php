<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionLog extends Model
{
    protected $fillable = [
        'activity_log_id',
        'inspection_type',
        'inspector_name',
        'inspector_organization',
        'certification_body',
        'checklist_items',
        'total_items',
        'compliant_items',
        'non_compliant_items',
        'compliance_score',
        'result',
        'findings',
        'recommendations',
        'follow_up_date',
        'corrective_action_required',
    ];

    protected $casts = [
        'checklist_items' => 'array',
        'total_items' => 'integer',
        'compliant_items' => 'integer',
        'non_compliant_items' => 'integer',
        'compliance_score' => 'decimal:2',
        'follow_up_date' => 'date',
        'corrective_action_required' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function activityLog(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class);
    }

    // ==================== ACCESSORS ====================

    public function getInspectionTypeLabelAttribute(): string
    {
        return match($this->inspection_type) {
            'internal' => 'Internal Inspection',
            'external' => 'External Inspection',
            'routine' => 'Routine Check',
            'surprise' => 'Surprise Inspection',
            'annual' => 'Annual Audit',
            default => $this->inspection_type,
        };
    }

    public function getResultLabelAttribute(): string
    {
        return match($this->result) {
            'passed' => 'Passed',
            'failed' => 'Failed',
            'conditional' => 'Conditional Pass',
            'pending' => 'Pending Review',
            default => $this->result,
        };
    }

    public function getResultColorAttribute(): string
    {
        return match($this->result) {
            'passed' => 'success',
            'failed' => 'danger',
            'conditional' => 'warning',
            'pending' => 'secondary',
            default => 'secondary',
        };
    }

    public function getComplianceScoreDisplayAttribute(): string
    {
        if (!$this->compliance_score) {
            return 'Not calculated';
        }
        return number_format($this->compliance_score, 1) . '%';
    }

    public function getComplianceScoreColorAttribute(): string
    {
        if (!$this->compliance_score) {
            return 'secondary';
        }
        if ($this->compliance_score >= 90) return 'success';
        if ($this->compliance_score >= 70) return 'primary';
        if ($this->compliance_score >= 50) return 'warning';
        return 'danger';
    }

    public function getNonComplianceRateAttribute(): ?float
    {
        if (!$this->total_items) {
            return null;
        }
        return round(($this->non_compliant_items / $this->total_items) * 100, 1);
    }

    public function getIsFollowUpDueAttribute(): bool
    {
        return $this->follow_up_date && $this->follow_up_date->isPast();
    }

    public function getDaysToFollowUpAttribute(): ?int
    {
        if (!$this->follow_up_date) {
            return null;
        }
        return now()->diffInDays($this->follow_up_date, false);
    }

    // ==================== SCOPES ====================

    public function scopeOfType($query, $type)
    {
        return $query->where('inspection_type', $type);
    }

    public function scopeInternal($query)
    {
        return $query->where('inspection_type', 'internal');
    }

    public function scopeExternal($query)
    {
        return $query->where('inspection_type', 'external');
    }

    public function scopeByResult($query, $result)
    {
        return $query->where('result', $result);
    }

    public function scopePassed($query)
    {
        return $query->where('result', 'passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('result', 'failed');
    }

    public function scopeNeedingFollowUp($query)
    {
        return $query->where('corrective_action_required', true);
    }

    public function scopeFollowUpDue($query)
    {
        return $query->whereNotNull('follow_up_date')
                     ->where('follow_up_date', '<=', now());
    }
}

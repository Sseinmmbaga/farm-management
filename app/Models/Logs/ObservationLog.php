<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservationLog extends Model
{
    protected $fillable = [
        'activity_log_id',
        'observation_type',
        'category',
        'severity',
        'affected_area',
        'area_unit',
        'affected_percentage',
        'growth_stage',
        'plant_health',
        'symptoms',
        'recommendations',
        'requires_action',
        'action_taken',
    ];

    protected $casts = [
        'affected_area' => 'decimal:2',
        'affected_percentage' => 'decimal:2',
        'requires_action' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function activityLog(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class);
    }

    // ==================== ACCESSORS ====================

    public function getObservationTypeLabelAttribute(): string
    {
        return match($this->observation_type) {
            'crop_health' => 'Crop Health',
            'pest' => 'Pest Observation',
            'disease' => 'Disease Observation',
            'weather' => 'Weather Observation',
            'growth' => 'Growth Stage',
            'soil' => 'Soil Condition',
            'water' => 'Water/Irrigation',
            default => $this->observation_type,
        };
    }

    public function getSeverityLabelAttribute(): string
    {
        return match($this->severity) {
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
            default => $this->severity ?? 'Not assessed',
        };
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'dark',
            default => 'secondary',
        };
    }

    public function getPlantHealthLabelAttribute(): string
    {
        return match($this->plant_health) {
            'excellent' => 'Excellent',
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor',
            'critical' => 'Critical',
            default => $this->plant_health ?? 'Not assessed',
        };
    }

    public function getPlantHealthColorAttribute(): string
    {
        return match($this->plant_health) {
            'excellent' => 'success',
            'good' => 'primary',
            'fair' => 'warning',
            'poor' => 'danger',
            'critical' => 'dark',
            default => 'secondary',
        };
    }

    public function getAffectedDisplayAttribute(): string
    {
        if ($this->affected_percentage) {
            return $this->affected_percentage . '%';
        }
        if ($this->affected_area) {
            return $this->affected_area . ' ' . ($this->area_unit ?? 'ha');
        }
        return 'Not specified';
    }

    // ==================== SCOPES ====================

    public function scopeOfType($query, $type)
    {
        return $query->where('observation_type', $type);
    }

    public function scopePestObservations($query)
    {
        return $query->where('observation_type', 'pest');
    }

    public function scopeDiseaseObservations($query)
    {
        return $query->where('observation_type', 'disease');
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeCritical($query)
    {
        return $query->whereIn('severity', ['high', 'critical']);
    }

    public function scopeRequiringAction($query)
    {
        return $query->where('requires_action', true);
    }

    public function scopeActionPending($query)
    {
        return $query->where('requires_action', true)
                     ->whereNull('action_taken');
    }
}

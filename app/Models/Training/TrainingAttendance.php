<?php

namespace App\Models\Training;

use App\Models\Farmers\Farmer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingAttendance extends Model
{
    protected $table = 'training_attendances';

    protected $fillable = [
        'training_session_id',
        'farmer_id',
        'registration_status',
        'attended',
        'check_in_time',
        'check_out_time',
        'attendance_percentage',
        'completed',
        'score',
        'certificate_issued',
        'certificate_number',
        'certificate_date',
        'feedback',
        'rating',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'attended' => 'boolean',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'attendance_percentage' => 'decimal:2',
        'completed' => 'boolean',
        'score' => 'integer',
        'certificate_issued' => 'boolean',
        'certificate_date' => 'date',
        'rating' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ==================== SCOPES ====================

    public function scopeAttended($query)
    {
        return $query->where('attended', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    public function scopeCertificateIssued($query)
    {
        return $query->where('certificate_issued', true);
    }

    // ==================== ACCESSORS ====================

    public function getRegistrationStatusLabelAttribute(): string
    {
        return match($this->registration_status) {
            'registered' => 'Registered',
            'waitlist' => 'Waitlist',
            'cancelled' => 'Cancelled',
            default => $this->registration_status,
        };
    }

    public function getDurationAttribute(): ?int
    {
        if ($this->check_in_time && $this->check_out_time) {
            return $this->check_in_time->diffInMinutes($this->check_out_time);
        }
        return null;
    }

    public function getIsPresentAttribute(): bool
    {
        return $this->attended;
    }

    public function getIsCheckedInAttribute(): bool
    {
        return !is_null($this->check_in_time);
    }

    public function getIsCheckedOutAttribute(): bool
    {
        return !is_null($this->check_out_time);
    }
}
<?php

namespace App\Models\Training;

use App\Models\Farmers\Farmer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingCertificate extends Model
{
    protected $table = 'training_certificates';

    protected $fillable = [
        'farmer_id',
        'training_program_id',
        'training_session_id',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'file_path',
        'status',
        'issued_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now());
            });
    }

    // ==================== ACCESSORS ====================

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'expired' => 'Expired',
            'revoked' => 'Revoked',
            default => $this->status,
        };
    }

    public function getIsValidAttribute(): bool
    {
        return $this->status === 'active' &&
            (!$this->expiry_date || $this->expiry_date >= now());
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'expired' ||
            ($this->expiry_date && $this->expiry_date < now());
    }

    public function getDaysToExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }
        return now()->diffInDays($this->expiry_date, false);
    }

    public function getCertificateUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }
        return asset('storage/' . $this->file_path);
    }
}
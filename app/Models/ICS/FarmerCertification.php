<?php

namespace App\Models\ICS;

use App\Models\Farmers\Farmer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmerCertification extends Model
{
    protected $table = 'farmer_certifications';

    protected $fillable = [
        'farmer_id',
        'compliance_standard_id',
        'certificate_number',
        'status',
        'application_date',
        'certification_date',
        'expiry_date',
        'last_inspection_date',
        'last_inspection_id',
        'conversion_year',
        'notes',
        'approved_by',
    ];

    protected $casts = [
        'application_date' => 'date',
        'certification_date' => 'date',
        'expiry_date' => 'date',
        'last_inspection_date' => 'date',
        'conversion_year' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function lastInspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'last_inspection_id');
    }

    // ==================== ACCESSORS ====================

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'certified' &&
               ($this->expiry_date === null || $this->expiry_date->isFuture());
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->isPast();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'certified' => 'Certified',
            'in-conversion' => 'In Conversion',
            'suspended' => 'Suspended',
            'revoked' => 'Revoked',
            'expired' => 'Expired',
            'pending' => 'Pending',
            default => ucfirst($this->status ?? 'Unknown'),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'certified' => 'success',
            'in-conversion' => 'info',
            'suspended' => 'warning',
            'revoked' => 'danger',
            'expired' => 'secondary',
            'pending' => 'primary',
            default => 'secondary',
        };
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'certified')
                     ->where(function ($q) {
                         $q->whereNull('expiry_date')
                           ->orWhere('expiry_date', '>', now());
                     });
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('status', 'certified')
                     ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    public function scopeInConversion($query)
    {
        return $query->where('status', 'in-conversion');
    }
}

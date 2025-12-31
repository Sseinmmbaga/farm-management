<?php

namespace App\Models\Farms;

use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Models\Farms\Field;
use App\Models\User;
use App\Traits\HasAuditTrail;
use App\Traits\HasLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmVisit extends Model
{
    use HasFactory, SoftDeletes, HasAuditTrail, HasLocation;

    protected $fillable = [
        'visit_number',
        'farm_id',
        'field_id',
        'farmer_id',
        'supervisor_id',
        'region_id',
        'district_id',
        'village_id',
        'latitude',
        'longitude',
        'scheduled_date',
        'actual_date',
        'status',
        'purpose',
        'notes',
        'photos',
        'report',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'actual_date' => 'datetime',
        'photos' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
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

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'info',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getPurposeLabelAttribute(): string
    {
        return match($this->purpose) {
            'inspection' => 'Inspection',
            'training' => 'Training',
            'support' => 'Support',
            'monitoring' => 'Monitoring',
            'other' => 'Other',
            default => $this->purpose,
        };
    }

    public function getHasPhotosAttribute(): bool
    {
        return !empty($this->photos) && count($this->photos) > 0;
    }

    // ==================== SCOPES ====================

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForFarm($query, $farmId)
    {
        return $query->where('farm_id', $farmId);
    }

    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    public function scopeForSupervisor($query, $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('scheduled_date', '<', now());
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('visit_number', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%")
              ->orWhere('report', 'like', "%{$search}%");
        });
    }

    // ==================== HELPERS ====================

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'actual_date' => now(),
        ]);
    }

    public function markAsInProgress(): void
    {
        $this->update(['status' => 'in_progress']);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Generate unique visit number
     */
    public static function generateVisitNumber(): string
    {
        $prefix = 'VIS';
        $year = date('Y');
        $lastVisit = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastVisit ? ((int) substr($lastVisit->visit_number, -5)) + 1 : 1;

        return $prefix . $year . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}

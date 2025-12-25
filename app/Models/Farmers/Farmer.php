<?php

namespace App\Models\Farmers;

use App\Models\User;
use App\Models\Location\Region;
use App\Models\Location\District;
use App\Models\Location\Village;
use App\Traits\HasLocation;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farmer extends Model
{
    use HasFactory, SoftDeletes, HasLocation, HasAuditTrail;

    protected $fillable = [
        'registration_number',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'national_id',
        'phone',
        'phone_alt',
        'email',
        'spouse_name',
        'spouse_title',
        'region_id',
        'district_id',
        'village_id',
        'subvillage',
        'address',
        'latitude',
        'longitude',
        'farmer_group_id',
        'extension_officer_id',
        'status',
        'registration_date',
        'certification_date',
        'certification_status',
        'household_size',
        'education_level',
        'total_land_size',
        'notes',
        'photo',
        'land_size_description',
        'cotton_producers_count',
        'lead_farmer',
        'demo_farm',
        'owns_farming_tools',
        'last_prohibited_chemicals_use',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'registration_date' => 'date',
        'certification_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'total_land_size' => 'decimal:2',
        'household_size' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function group(): BelongsTo
    {
        return $this->belongsTo(FarmerGroup::class, 'farmer_group_id');
    }

    public function extensionOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'extension_officer_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(FarmerDocument::class);
    }

    public function farms(): HasMany
    {
        return $this->hasMany(\App\Models\Farms\Farm::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(\App\Models\Assets\Asset::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(\App\Models\Logs\ActivityLog::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(\App\Models\ICS\Inspection::class);
    }

    public function trainingAttendances(): HasMany
    {
        return $this->hasMany(\App\Models\Training\TrainingAttendance::class);
    }

    public function stockDistributions(): HasMany
    {
        return $this->hasMany(\App\Models\Stock\StockDistribution::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(\App\Models\ICS\FarmerCertification::class);
    }

    // ==================== ACCESSORS ====================

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    public function getShortNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'pending' => 'Pending Approval',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    }

    public function getCertificationStatusLabelAttribute(): string
    {
        return match($this->certification_status) {
            'organic' => 'Organic Certified',
            'in-conversion' => 'In Conversion',
            'conventional' => 'Conventional',
            default => $this->certification_status ?? 'Not Set',
        };
    }

    public function getTotalFarmsAttribute(): int
    {
        return $this->farms()->count();
    }

    public function getTotalFarmAreaAttribute(): float
    {
        return $this->farms()->sum('total_area') ?? 0;
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOrganic($query)
    {
        return $query->where('certification_status', 'organic');
    }

    public function scopeInConversion($query)
    {
        return $query->where('certification_status', 'in-conversion');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('extension_officer_id', $userId);
    }

    public function scopeInGroup($query, $groupId)
    {
        return $query->where('farmer_group_id', $groupId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('registration_number', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('national_id', 'like', "%{$search}%");
        });
    }

    // ==================== HELPERS ====================

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOrganic(): bool
    {
        return $this->certification_status === 'organic';
    }

    public function isInConversion(): bool
    {
        return $this->certification_status === 'in-conversion';
    }

    /**
     * Generate unique registration number
     */
    public static function generateRegistrationNumber(): string
    {
        $prefix = 'RMF'; // Remei Farmer
        $year = date('Y');
        $lastFarmer = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastFarmer ? ((int) substr($lastFarmer->registration_number, -5)) + 1 : 1;

        return $prefix . $year . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}

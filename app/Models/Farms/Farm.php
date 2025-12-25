<?php

namespace App\Models\Farms;

use App\Models\Farmers\Farmer;
use App\Traits\HasLocation;
use App\Traits\HasAuditTrail;
use App\Traits\BelongsToFarmer;
use App\Traits\HasQuantities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    use HasFactory, SoftDeletes, HasLocation, HasAuditTrail, BelongsToFarmer, HasQuantities;

    protected $fillable = [
        'farmer_id',
        'code',
        'name',
        'region_id',
        'district_id',
        'village_id',
        'latitude',
        'longitude',
        'boundary_coordinates',
        'total_area',
        'cultivated_area',
        'soil_type',
        'water_source',
        'terrain',
        'certification_status',
        'organic_since',
        'conversion_year',
        'status',
        'registration_date',
        'notes',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'boundary_coordinates' => 'array',
        'total_area' => 'decimal:2',
        'cultivated_area' => 'decimal:2',
        'organic_since' => 'datetime',
        'registration_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(FarmHistory::class);
    }

    public function boundaries(): HasMany
    {
        return $this->hasMany(FarmBoundary::class)->orderBy('point_order');
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(FarmSeason::class);
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

    public function farmRecords(): HasMany
    {
        return $this->hasMany(FarmRecord::class);
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? "Farm {$this->code}";
    }

    public function getCertificationStatusLabelAttribute(): string
    {
        return match($this->certification_status) {
            'organic' => 'Organic',
            'in-conversion' => 'In Conversion',
            'conventional' => 'Conventional',
            default => $this->certification_status ?? 'Not Set',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'abandoned' => 'Abandoned',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'abandoned' => 'danger',
            default => 'secondary',
        };
    }

    public function getYearsOrganicAttribute(): ?int
    {
        return $this->organic_since?->diffInYears(now());
    }

    public function getHasBoundaryAttribute(): bool
    {
        return $this->boundaries()->count() > 0 || !empty($this->boundary_coordinates);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrganic($query)
    {
        return $query->where('certification_status', 'organic');
    }

    public function scopeInConversion($query)
    {
        return $query->where('certification_status', 'in-conversion');
    }

    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhereHas('farmer', function ($farmerQuery) use ($search) {
                  $farmerQuery->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhereRaw("(first_name || ' ' || last_name) LIKE ?", ["%{$search}%"]);
              });
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

    public static function generateCode(int $farmerId): string
    {
        $farmer = Farmer::find($farmerId);
        $prefix = $farmer ? substr($farmer->registration_number, -5) : 'FARM';
        $count = self::where('farmer_id', $farmerId)->count() + 1;

        return "SH{$prefix}-" . str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Get GeoJSON representation of farm boundary
     */
    public function toGeoJson(): array
    {
        $coordinates = [];

        if ($this->boundary_coordinates) {
            $coordinates = $this->boundary_coordinates;
        } elseif ($this->boundaries->count() > 0) {
            $coordinates = $this->boundaries->map(function ($point) {
                return [$point->longitude, $point->latitude];
            })->toArray();
            // Close the polygon
            if (count($coordinates) > 0) {
                $coordinates[] = $coordinates[0];
            }
        }

        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->id,
                'code' => $this->code,
                'name' => $this->display_name,
                'farmer' => $this->farmer?->full_name,
                'area' => $this->total_area,
                'status' => $this->certification_status,
            ],
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => [$coordinates],
            ],
        ];
    }
}

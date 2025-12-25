<?php

namespace App\Models\Farms;

use App\Models\Farmers\Farmer;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use HasFactory, SoftDeletes, HasAuditTrail;

    protected $fillable = [
        'farm_id',
        'code',
        'name',
        'location_description',
        'latitude',
        'longitude',
        'boundary_coordinates',
        'total_area',
        'measurement_unit',
        'soil_type',
        'soil_ph',
        'soil_texture',
        'slope_percentage',
        'drainage',
        'current_crop_type',
        'crop_variety',
        'planting_date',
        'expected_harvest_date',
        'actual_harvest_date',
        'expected_yield',
        'actual_yield',
        'yield_unit',
        'status',
        'irrigation_type',
        'irrigation_source',
        'irrigation_frequency_days',
        'is_organic',
        'organic_certified_since',
        'certification_body',
        'certification_number',
        'previous_crop',
        'next_planned_crop',
        'rotation_date',
        'notes',
        'establishment_date',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'boundary_coordinates' => 'array',
        'total_area' => 'decimal:2',
        'soil_ph' => 'decimal:2',
        'slope_percentage' => 'decimal:2',
        'expected_yield' => 'decimal:2',
        'actual_yield' => 'decimal:2',
        'is_organic' => 'boolean',
        'organic_certified_since' => 'date',
        'planting_date' => 'datetime',
        'expected_harvest_date' => 'date',
        'actual_harvest_date' => 'date',
        'rotation_date' => 'date',
        'establishment_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(FieldHistory::class);
    }

    public function boundaries(): HasMany
    {
        return $this->hasMany(FieldBoundary::class)->orderBy('point_order');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Farms\Season::class);
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? "Field {$this->code}";
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'fallow' => 'Fallow',
            'prepared' => 'Prepared',
            'planted' => 'Planted',
            'growing' => 'Growing',
            'harvested' => 'Harvested',
            'abandoned' => 'Abandoned',
            'converted' => 'Converted',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'fallow' => 'secondary',
            'prepared' => 'info',
            'planted' => 'primary',
            'growing' => 'warning',
            'harvested' => 'success',
            'abandoned' => 'danger',
            'converted' => 'dark',
            default => 'secondary',
        };
    }

    public function getAreaWithUnitAttribute(): string
    {
        return $this->total_area . ' ' . $this->measurement_unit;
    }

    public function getYieldWithUnitAttribute(): ?string
    {
        if (!$this->actual_yield) {
            return null;
        }
        return $this->actual_yield . ' ' . $this->yield_unit;
    }

    public function getExpectedYieldWithUnitAttribute(): ?string
    {
        if (!$this->expected_yield) {
            return null;
        }
        return $this->expected_yield . ' ' . $this->yield_unit;
    }

    public function getHasBoundaryAttribute(): bool
    {
        return $this->boundaries()->count() > 0 || !empty($this->boundary_coordinates);
    }

    public function getDaysSincePlantingAttribute(): ?int
    {
        return $this->planting_date?->diffInDays(now());
    }

    public function getDaysToHarvestAttribute(): ?int
    {
        if (!$this->expected_harvest_date) {
            return null;
        }
        return now()->diffInDays($this->expected_harvest_date, false); // negative if past due
    }

    public function getIsHarvestDueAttribute(): bool
    {
        return $this->expected_harvest_date && now()->greaterThanOrEqualTo($this->expected_harvest_date);
    }

    public function getIsHarvestedAttribute(): bool
    {
        return $this->status === 'harvested' || $this->actual_harvest_date !== null;
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePlanted($query)
    {
        return $query->where('status', 'planted');
    }

    public function scopeGrowing($query)
    {
        return $query->where('status', 'growing');
    }

    public function scopeHarvested($query)
    {
        return $query->where('status', 'harvested');
    }

    public function scopeFallow($query)
    {
        return $query->where('status', 'fallow');
    }

    public function scopeOrganic($query)
    {
        return $query->where('is_organic', true);
    }

    public function scopeForFarm($query, $farmId)
    {
        return $query->where('farm_id', $farmId);
    }

    public function scopeWithCrop($query, $cropType)
    {
        return $query->where('current_crop_type', $cropType);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('current_crop_type', 'like', "%{$search}%");
        });
    }

    // ==================== HELPERS ====================

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPlanted(): bool
    {
        return $this->status === 'planted';
    }

    public function isGrowing(): bool
    {
        return $this->status === 'growing';
    }

    public function isHarvested(): bool
    {
        return $this->status === 'harvested';
    }

    public function isFallow(): bool
    {
        return $this->status === 'fallow';
    }

    public static function generateCode(int $farmId): string
    {
        $farm = Farm::find($farmId);
        $farmCode = $farm ? $farm->code : 'FARM';
        $count = self::where('farm_id', $farmId)->count() + 1;

        return "FLD-{$farmCode}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get GeoJSON representation of field boundary
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
                'farm' => $this->farm?->display_name,
                'area' => $this->total_area,
                'crop' => $this->current_crop_type,
                'status' => $this->status,
            ],
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => [$coordinates],
            ],
        ];
    }

    /**
     * Calculate total cultivated area for a farm
     */
    public static function getTotalCultivatedAreaForFarm(int $farmId): float
    {
        return self::where('farm_id', $farmId)
            ->whereIn('status', ['active', 'planted', 'growing', 'harvested'])
            ->sum('total_area');
    }

    /**
     * Get crop distribution for a farm
     */
    public static function getCropDistributionForFarm(int $farmId): array
    {
        return self::where('farm_id', $farmId)
            ->whereNotNull('current_crop_type')
            ->selectRaw('current_crop_type, SUM(total_area) as total_area')
            ->groupBy('current_crop_type')
            ->orderByDesc('total_area')
            ->get()
            ->toArray();
    }
}
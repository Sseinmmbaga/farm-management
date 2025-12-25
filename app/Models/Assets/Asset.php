<?php

namespace App\Models\Assets;

use App\Enums\AssetType;
use App\Enums\AssetStatus;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Traits\HasLocation;
use App\Traits\HasAuditTrail;
use App\Traits\HasQuantities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Asset extends Model
{
    use HasFactory, SoftDeletes, HasLocation, HasAuditTrail, HasQuantities;

    protected $fillable = [
        'type',
        'name',
        'code',
        'description',
        'status',
        'farmer_id',
        'farm_id',
        'region_id',
        'district_id',
        'village_id',
        'latitude',
        'longitude',
        'geometry',
        'parent_id',
        'acquired_date',
        'disposed_date',
        'data',
    ];

    protected $casts = [
        'type' => AssetType::class,
        'status' => AssetStatus::class,
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'geometry' => 'array',
        'data' => 'array',
        'acquired_date' => 'date',
        'disposed_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Asset::class, 'parent_id');
    }

    public function landAsset(): HasOne
    {
        return $this->hasOne(LandAsset::class);
    }

    public function cropAsset(): HasOne
    {
        return $this->hasOne(CropAsset::class);
    }

    public function equipmentAsset(): HasOne
    {
        return $this->hasOne(EquipmentAsset::class);
    }

    public function materialAsset(): HasOne
    {
        return $this->hasOne(MaterialAsset::class);
    }

    public function groupAsset(): HasOne
    {
        return $this->hasOne(GroupAsset::class);
    }

    public function activityLogs(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Logs\ActivityLog::class, 'activity_log_asset');
    }

    // ==================== ACCESSORS ====================

    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    public function getTypeIconAttribute(): string
    {
        return $this->type->icon();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getDetailAttribute()
    {
        return match($this->type) {
            AssetType::LAND => $this->landAsset,
            AssetType::CROP => $this->cropAsset,
            AssetType::EQUIPMENT => $this->equipmentAsset,
            AssetType::MATERIAL => $this->materialAsset,
            AssetType::GROUP => $this->groupAsset,
            default => null,
        };
    }

    // ==================== SCOPES ====================

    public function scopeOfType($query, AssetType $type)
    {
        return $query->where('type', $type->value);
    }

    public function scopeLand($query)
    {
        return $query->where('type', AssetType::LAND->value);
    }

    public function scopeCrops($query)
    {
        return $query->where('type', AssetType::CROP->value);
    }

    public function scopeEquipment($query)
    {
        return $query->where('type', AssetType::EQUIPMENT->value);
    }

    public function scopeMaterials($query)
    {
        return $query->where('type', AssetType::MATERIAL->value);
    }

    public function scopeGroups($query)
    {
        return $query->where('type', AssetType::GROUP->value);
    }

    public function scopeActive($query)
    {
        return $query->where('status', AssetStatus::ACTIVE->value);
    }

    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    public function scopeForFarm($query, $farmId)
    {
        return $query->where('farm_id', $farmId);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%");
        });
    }

    // ==================== HELPERS ====================

    public function isActive(): bool
    {
        return $this->status === AssetStatus::ACTIVE;
    }

    public static function generateCode(AssetType $type): string
    {
        $prefixes = [
            AssetType::LAND->value => 'LND',
            AssetType::CROP->value => 'CRP',
            AssetType::EQUIPMENT->value => 'EQP',
            AssetType::MATERIAL->value => 'MAT',
            AssetType::GROUP->value => 'GRP',
        ];

        $prefix = $prefixes[$type->value] ?? 'AST';
        $count = self::where('type', $type->value)->count() + 1;

        return $prefix . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get GeoJSON representation
     */
    public function toGeoJson(): array
    {
        $geometry = $this->geometry ?? [
            'type' => 'Point',
            'coordinates' => [$this->longitude ?? 0, $this->latitude ?? 0],
        ];

        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->id,
                'type' => $this->type->value,
                'name' => $this->name,
                'code' => $this->code,
                'status' => $this->status->value,
            ],
            'geometry' => $geometry,
        ];
    }
}

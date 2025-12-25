<?php

namespace App\Models\Farms;

use App\Models\Farmers\Farmer;
use App\Traits\HasAuditTrail;
use App\Traits\BelongsToFarmer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmRecord extends Model
{
    use HasFactory, SoftDeletes, HasAuditTrail, BelongsToFarmer;

    protected $fillable = [
        'farm_id',
        'farmer_id',
        'season_id',
        'record_type',
        'certification_status',
        'cattle_count',
        'goats_sheep_count',
        'oxen_count',
        'has_input_book',
        'has_pump',
        'has_chemical_seed_residue',
        'has_chemical_residue',
        'area_size',
        'land_bought',
        'land_sold',
        'land_borrowed',
        'land_lent',
        'registration_year',
        'impact_type',
        'notes',
    ];

    protected $casts = [
        'has_input_book' => 'boolean',
        'has_pump' => 'boolean',
        'has_chemical_seed_residue' => 'boolean',
        'has_chemical_residue' => 'boolean',
        'cattle_count' => 'integer',
        'goats_sheep_count' => 'integer',
        'oxen_count' => 'integer',
        'area_size' => 'decimal:2',
        'land_bought' => 'decimal:2',
        'land_sold' => 'decimal:2',
        'land_borrowed' => 'decimal:2',
        'land_lent' => 'decimal:2',
        'registration_year' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    // ==================== ACCESSORS ====================

    public function getRecordTypeLabelAttribute(): string
    {
        return match($this->record_type) {
            'new' => 'New Farm Record',
            'existing' => 'Existing Farm Record',
            default => $this->record_type,
        };
    }

    public function getCertificationStatusLabelAttribute(): string
    {
        return match($this->certification_status) {
            'C0' => 'C0 - Conventional',
            'C1' => 'C1 - Year 1 Conversion',
            'C2' => 'C2 - Year 2 Conversion',
            'O' => 'O - Organic',
            default => $this->certification_status ?? 'Not Set',
        };
    }

    public function getCertificationStatusColorAttribute(): string
    {
        return match($this->certification_status) {
            'C0' => 'secondary',
            'C1' => 'warning',
            'C2' => 'info',
            'O' => 'success',
            default => 'secondary',
        };
    }

    public function getTotalLivestockAttribute(): int
    {
        return $this->cattle_count + $this->goats_sheep_count + $this->oxen_count;
    }

    public function getNetLandChangeAttribute(): float
    {
        $bought = $this->land_bought ?? 0;
        $borrowed = $this->land_borrowed ?? 0;
        $sold = $this->land_sold ?? 0;
        $lent = $this->land_lent ?? 0;

        return ($bought + $borrowed) - ($sold + $lent);
    }

    public function getIsNewRecordAttribute(): bool
    {
        return $this->record_type === 'new';
    }

    public function getIsExistingRecordAttribute(): bool
    {
        return $this->record_type === 'existing';
    }

    public function getHasEquipmentAttribute(): bool
    {
        return $this->has_input_book || $this->has_pump;
    }

    public function getHasChemicalIssuesAttribute(): bool
    {
        return $this->has_chemical_seed_residue || $this->has_chemical_residue;
    }

    // ==================== SCOPES ====================

    public function scopeNewRecords($query)
    {
        return $query->where('record_type', 'new');
    }

    public function scopeExistingRecords($query)
    {
        return $query->where('record_type', 'existing');
    }

    public function scopeForSeason($query, $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    public function scopeForFarm($query, $farmId)
    {
        return $query->where('farm_id', $farmId);
    }

    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    public function scopeWithCertification($query, $status)
    {
        return $query->where('certification_status', $status);
    }

    public function scopeOrganic($query)
    {
        return $query->where('certification_status', 'O');
    }

    public function scopeInConversion($query)
    {
        return $query->whereIn('certification_status', ['C1', 'C2']);
    }

    public function scopeConventional($query)
    {
        return $query->where('certification_status', 'C0');
    }

    public function scopeCurrentSeason($query)
    {
        $currentSeason = Season::getCurrentSeason();
        return $currentSeason ? $query->where('season_id', $currentSeason->id) : $query;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('farm', function ($farmQuery) use ($search) {
                $farmQuery->where('code', 'like', "%{$search}%")
                          ->orWhere('name', 'like', "%{$search}%");
            })
            ->orWhereHas('farmer', function ($farmerQuery) use ($search) {
                $farmerQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
            });
        });
    }

    // ==================== HELPERS ====================

    public function isNewRecord(): bool
    {
        return $this->record_type === 'new';
    }

    public function isExistingRecord(): bool
    {
        return $this->record_type === 'existing';
    }

    public function isOrganic(): bool
    {
        return $this->certification_status === 'O';
    }

    public function isInConversion(): bool
    {
        return in_array($this->certification_status, ['C1', 'C2']);
    }

    public function isConventional(): bool
    {
        return $this->certification_status === 'C0';
    }

    public static function getForFarmAndSeason(int $farmId, int $seasonId, string $recordType): ?self
    {
        return self::where('farm_id', $farmId)
            ->where('season_id', $seasonId)
            ->where('record_type', $recordType)
            ->first();
    }

    public static function hasRecordForSeason(int $farmId, int $seasonId, string $recordType): bool
    {
        return self::where('farm_id', $farmId)
            ->where('season_id', $seasonId)
            ->where('record_type', $recordType)
            ->exists();
    }
}

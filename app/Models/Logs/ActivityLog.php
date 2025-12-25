<?php

namespace App\Models\Logs;

use App\Enums\LogType;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Models\Farms\Season;
use App\Models\Assets\Asset;
use App\Traits\HasLocation;
use App\Traits\HasAuditTrail;
use App\Traits\HasQuantities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ActivityLog extends Model
{
    use HasFactory, SoftDeletes, HasLocation, HasAuditTrail, HasQuantities;

    protected $table = 'activity_logs';

    protected $fillable = [
        'type',
        'name',
        'description',
        'log_date',
        'status',
        'farmer_id',
        'farm_id',
        'asset_id',
        'season_id',
        'region_id',
        'district_id',
        'village_id',
        'latitude',
        'longitude',
        'geometry',
        'data',
        'notes',
        'images',
        'is_flagged',
        'flag_reason',
    ];

    protected $casts = [
        'type' => LogType::class,
        'log_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'geometry' => 'array',
        'data' => 'array',
        'images' => 'array',
        'is_flagged' => 'boolean',
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

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'activity_log_asset');
    }

    // Type-specific details
    public function seedingLog(): HasOne
    {
        return $this->hasOne(SeedingLog::class);
    }

    public function inputLog(): HasOne
    {
        return $this->hasOne(InputLog::class);
    }

    public function observationLog(): HasOne
    {
        return $this->hasOne(ObservationLog::class);
    }

    public function harvestLog(): HasOne
    {
        return $this->hasOne(HarvestLog::class);
    }

    public function trainingLog(): HasOne
    {
        return $this->hasOne(TrainingLog::class);
    }

    public function inspectionLog(): HasOne
    {
        return $this->hasOne(InspectionLog::class);
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
        return match($this->status) {
            'pending' => 'Pending',
            'done' => 'Completed',
            'cancelled' => 'Cancelled',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'done' => 'success',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    public function getDetailAttribute()
    {
        return match($this->type) {
            LogType::SEEDING => $this->seedingLog,
            LogType::INPUT => $this->inputLog,
            LogType::OBSERVATION => $this->observationLog,
            LogType::HARVEST => $this->harvestLog,
            LogType::TRAINING => $this->trainingLog,
            LogType::INSPECTION => $this->inspectionLog,
            default => null,
        };
    }

    public function getImageCountAttribute(): int
    {
        return is_array($this->images) ? count($this->images) : 0;
    }

    // ==================== SCOPES ====================

    public function scopeOfType($query, LogType $type)
    {
        return $query->where('type', $type->value);
    }

    public function scopeSeeding($query)
    {
        return $query->where('type', LogType::SEEDING->value);
    }

    public function scopeInputs($query)
    {
        return $query->where('type', LogType::INPUT->value);
    }

    public function scopeObservations($query)
    {
        return $query->where('type', LogType::OBSERVATION->value);
    }

    public function scopeHarvests($query)
    {
        return $query->where('type', LogType::HARVEST->value);
    }

    public function scopeTraining($query)
    {
        return $query->where('type', LogType::TRAINING->value);
    }

    public function scopeInspections($query)
    {
        return $query->where('type', LogType::INSPECTION->value);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'done');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    public function scopeForFarm($query, $farmId)
    {
        return $query->where('farm_id', $farmId);
    }

    public function scopeForSeason($query, $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('log_date', [$start, $end]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('log_date', '>=', now()->subDays($days));
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    // ==================== HELPERS ====================

    public function isCompleted(): bool
    {
        return $this->status === 'done';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'done']);
    }

    public function flag(string $reason): void
    {
        $this->update([
            'is_flagged' => true,
            'flag_reason' => $reason,
        ]);
    }

    public function unflag(): void
    {
        $this->update([
            'is_flagged' => false,
            'flag_reason' => null,
        ]);
    }
}

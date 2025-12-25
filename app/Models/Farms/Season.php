<?php

namespace App\Models\Farms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farmSeasons(): HasMany
    {
        return $this->hasMany(FarmSeason::class);
    }

    public function farmHistories(): HasMany
    {
        return $this->hasMany(FarmHistory::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(\App\Models\Logs\ActivityLog::class);
    }

    public function farmRecords(): HasMany
    {
        return $this->hasMany(FarmRecord::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    // ==================== ACCESSORS ====================

    public function getDurationDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getIsOngoingAttribute(): bool
    {
        return now()->between($this->start_date, $this->end_date);
    }

    // ==================== HELPERS ====================

    public static function getCurrentSeason(): ?self
    {
        return self::where('is_current', true)->first()
            ?? self::where('start_date', '<=', now())
                   ->where('end_date', '>=', now())
                   ->first();
    }

    public function markAsCurrent(): void
    {
        self::where('is_current', true)->update(['is_current' => false]);
        $this->update(['is_current' => true]);
    }
}

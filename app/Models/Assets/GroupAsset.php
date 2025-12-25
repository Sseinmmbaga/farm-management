<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupAsset extends Model
{
    protected $fillable = [
        'asset_id',
        'group_type',
        'member_count',
        'leader_name',
        'leader_contact',
        'formation_date',
    ];

    protected $casts = [
        'member_count' => 'integer',
        'formation_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(AssetGroupMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(AssetGroupMember::class)->where('is_active', true);
    }

    // ==================== ACCESSORS ====================

    public function getGroupTypeLabelAttribute(): string
    {
        return match($this->group_type) {
            'farmer_group' => 'Farmer Group (Kikundi cha Wakulima)',
            'cooperative' => 'Cooperative (Ushirika)',
            'zone' => 'Zone',
            'association' => 'Association',
            default => $this->group_type,
        };
    }

    public function getAgeYearsAttribute(): ?int
    {
        return $this->formation_date?->diffInYears(now());
    }

    public function getActiveMemberCountAttribute(): int
    {
        return $this->activeMembers()->count();
    }

    // ==================== SCOPES ====================

    public function scopeOfType($query, $type)
    {
        return $query->where('group_type', $type);
    }

    public function scopeFarmerGroups($query)
    {
        return $query->where('group_type', 'farmer_group');
    }

    public function scopeCooperatives($query)
    {
        return $query->where('group_type', 'cooperative');
    }
}

<?php

namespace App\Models\Farmers;

use App\Traits\HasLocation;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmerGroup extends Model
{
    use SoftDeletes, HasLocation, HasAuditTrail;

    protected $fillable = [
        'name',
        'code',
        'group_type',
        'description',
        'region_id',
        'district_id',
        'village_id',
        'leader_name',
        'leader_phone',
        'established_date',
        'is_active',
    ];

    protected $casts = [
        'established_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function farmers(): HasMany
    {
        return $this->hasMany(Farmer::class);
    }

    // ==================== ACCESSORS ====================

    public function getMemberCountAttribute(): int
    {
        return $this->farmers()->count();
    }

    public function getActiveMemberCountAttribute(): int
    {
        return $this->farmers()->active()->count();
    }

    public function getGroupTypeLabelAttribute(): string
    {
        return match($this->group_type) {
            'simba' => 'Simba',
            'tembo' => 'Tembo Organic',
            default => ucfirst($this->group_type ?? 'Unknown'),
        };
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('leader_name', 'like', "%{$search}%");
        });
    }

    // ==================== HELPERS ====================

    public static function generateCode(): string
    {
        $prefix = 'GRP';
        $lastGroup = self::orderBy('id', 'desc')->first();
        $sequence = $lastGroup ? $lastGroup->id + 1 : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}

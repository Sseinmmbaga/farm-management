<?php

namespace App\Models\ICS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplianceStandard extends Model
{
    use SoftDeletes;

    protected $table = 'compliance_standards';

    protected $fillable = [
        'name',
        'code',
        'description',
        'version',
        'effective_date',
        'status',
        'requirements',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    public function checklists(): HasMany
    {
        return $this->hasMany(InspectionChecklist::class, 'compliance_standard_id');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(FarmerCertification::class, 'compliance_standard_id');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeLatestVersion($query)
    {
        return $query->orderBy('version', 'desc');
    }

    // ==================== ACCESSORS ====================

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} v{$this->version}";
    }
}
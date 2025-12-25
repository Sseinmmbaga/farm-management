<?php

namespace App\Models\ICS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionChecklist extends Model
{
    protected $table = 'inspection_checklists';

    protected $fillable = [
        'name',
        'description',
        'version',
        'is_active',
        'applicable_to',
        'total_points',
        'passing_score',
        'requires_signatures',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_points' => 'decimal:2',
        'passing_score' => 'decimal:2',
        'requires_signatures' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'checklist_id');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class, 'inspection_checklist_id');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} v{$this->version}";
    }
}
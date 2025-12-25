<?php

namespace App\Models\ICS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistItem extends Model
{
    protected $table = 'checklist_items';

    protected $fillable = [
        'checklist_id',
        'parent_id',
        'section',
        'item_number',
        'question',
        'description',
        'answer_type',
        'options',
        'points',
        'is_required',
        'is_critical',
        'sort_order',
        'help_text',
        'reference',
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'is_required' => 'boolean',
        'is_critical' => 'boolean',
        'options' => 'array',
        'sort_order' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(InspectionChecklist::class, 'checklist_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'parent_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(InspectionFinding::class, 'checklist_item_id');
    }

    // ==================== SCOPES ====================

    public function scopeRootItems($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    // ==================== ACCESSORS ====================

    public function getFullQuestionAttribute(): string
    {
        if ($this->item_number) {
            return "{$this->item_number}. {$this->question}";
        }
        return $this->question;
    }

    public function getHasChildrenAttribute(): bool
    {
        return $this->children()->exists();
    }
}
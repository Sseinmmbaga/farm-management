<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockCategory extends Model
{
    protected $fillable = [
        'name',
        'name_sw',
        'code',
        'description',
        'parent_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function parent(): BelongsTo
    {
        return $this->belongsTo(StockCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(StockCategory::class, 'parent_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockItem::class, 'category_id');
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return $this->name_sw ?? $this->name;
    }

    public function getFullNameAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->name . ' > ' . $this->name;
        }
        return $this->name;
    }

    public function getItemCountAttribute(): int
    {
        return $this->items()->count();
    }

    public function getTotalStockValueAttribute(): float
    {
        return $this->items()->sum(\DB::raw('quantity_on_hand * COALESCE(unit_cost, 0)'));
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

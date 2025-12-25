<?php

namespace App\Models\Stock;

use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    use SoftDeletes, HasAuditTrail;

    protected $fillable = [
        'category_id',
        'name',
        'name_sw',
        'code',
        'sku',
        'description',
        'quantity_on_hand',
        'quantity_reserved',
        'quantity_available',
        'unit',
        'reorder_level',
        'reorder_quantity',
        'unit_cost',
        'unit_price',
        'currency',
        'brand',
        'manufacturer',
        'is_organic_approved',
        'requires_batch_tracking',
        'is_active',
        'image',
        'warehouse_location',
        'bin_location',
    ];

    protected $casts = [
        'quantity_on_hand' => 'decimal:2',
        'quantity_reserved' => 'decimal:2',
        'quantity_available' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'reorder_quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'is_organic_approved' => 'boolean',
        'requires_batch_tracking' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(StockCategory::class, 'category_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(StockBatch::class);
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(StockDistribution::class);
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        return $this->name_sw ?? $this->name;
    }

    public function getQuantityDisplayAttribute(): string
    {
        return number_format($this->quantity_on_hand, 2) . ' ' . $this->unit;
    }

    public function getAvailableDisplayAttribute(): string
    {
        return number_format($this->quantity_available, 2) . ' ' . $this->unit;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity_available <= $this->reorder_level;
    }

    public function getIsCriticalStockAttribute(): bool
    {
        return $this->quantity_available <= ($this->reorder_level * 0.5);
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->quantity_available <= 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->is_out_of_stock) return 'Out of Stock';
        if ($this->is_critical_stock) return 'Critical';
        if ($this->is_low_stock) return 'Low Stock';
        return 'In Stock';
    }

    public function getStockStatusColorAttribute(): string
    {
        if ($this->is_out_of_stock) return 'danger';
        if ($this->is_critical_stock) return 'warning';
        if ($this->is_low_stock) return 'info';
        return 'success';
    }

    public function getStockValueAttribute(): float
    {
        return $this->quantity_on_hand * ($this->unit_cost ?? 0);
    }

    public function getOrganicStatusAttribute(): string
    {
        return $this->is_organic_approved ? 'Organic Approved' : 'Conventional';
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity_available <= reorder_level');
    }

    public function scopeCriticalStock($query)
    {
        return $query->whereRaw('quantity_available <= (reorder_level * 0.5)');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity_available', '<=', 0);
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity_available', '>', 0);
    }

    public function scopeOrganicApproved($query)
    {
        return $query->where('is_organic_approved', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('name_sw', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    // ==================== HELPERS ====================

    public function updateQuantities(): void
    {
        $this->quantity_available = $this->quantity_on_hand - $this->quantity_reserved;
        $this->save();
    }

    public function adjustStock(float $quantity, string $type = 'add'): void
    {
        if ($type === 'add') {
            $this->quantity_on_hand += $quantity;
        } else {
            $this->quantity_on_hand -= $quantity;
        }
        $this->updateQuantities();
    }

    public static function generateCode(): string
    {
        $prefix = 'STK';
        $count = self::count() + 1;
        return $prefix . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}

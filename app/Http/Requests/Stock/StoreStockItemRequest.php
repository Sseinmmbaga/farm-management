<?php

namespace App\Http\Requests\Stock;

use App\Models\Stock\StockItem;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:stock_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'name_sw' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100', 'unique:stock_items,code'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:stock_items,sku'],
            'description' => ['nullable', 'string'],
            'quantity_on_hand' => ['numeric', 'min:0', 'nullable'],
            'quantity_reserved' => ['numeric', 'min:0', 'nullable'],
            'quantity_available' => ['numeric', 'min:0', 'nullable'],
            'unit' => ['required', 'string', 'max:50'],
            'reorder_level' => ['numeric', 'min:0', 'nullable'],
            'reorder_quantity' => ['numeric', 'min:0', 'nullable'],
            'unit_cost' => ['numeric', 'min:0', 'nullable'],
            'unit_price' => ['numeric', 'min:0', 'nullable'],
            'currency' => ['string', 'max:3', 'nullable'],
            'brand' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'is_organic_approved' => ['boolean'],
            'requires_batch_tracking' => ['boolean'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
            'warehouse_location' => ['nullable', 'string', 'max:255'],
            'bin_location' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'name_sw' => 'name (Swahili)',
            'unit' => 'unit of measure',
            'reorder_level' => 'reorder level',
            'reorder_quantity' => 'reorder quantity',
            'unit_cost' => 'unit cost',
            'unit_price' => 'unit price',
            'is_organic_approved' => 'organic approved',
            'requires_batch_tracking' => 'requires batch tracking',
            'warehouse_location' => 'warehouse location',
            'bin_location' => 'bin location',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'quantity_on_hand' => $this->quantity_on_hand ?? 0,
            'quantity_reserved' => $this->quantity_reserved ?? 0,
            'quantity_available' => $this->quantity_available ?? 0,
            'reorder_level' => $this->reorder_level ?? 0,
            'reorder_quantity' => $this->reorder_quantity ?? 0,
            'unit_cost' => $this->unit_cost ?? 0,
            'unit_price' => $this->unit_price ?? 0,
            'currency' => $this->currency ?? 'TZS',
            'is_organic_approved' => $this->boolean('is_organic_approved'),
            'requires_batch_tracking' => $this->boolean('requires_batch_tracking'),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = StockItem::generateCode();
        }

        // Calculate quantity_available if not set
        if (!isset($validated['quantity_available'])) {
            $validated['quantity_available'] = ($validated['quantity_on_hand'] ?? 0) - ($validated['quantity_reserved'] ?? 0);
        }

        // Set created_by and updated_by
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        return $validated;
    }
}
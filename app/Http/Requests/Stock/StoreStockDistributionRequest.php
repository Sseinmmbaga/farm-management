<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockDistributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farmer_id' => ['required', 'exists:farmers,id'],
            'farm_id' => ['nullable', 'exists:farms,id'],
            'season_id' => ['nullable', 'exists:seasons,id'],
            'stock_item_id' => ['required', 'exists:stock_items,id'],
            'distribution_type' => ['required', Rule::in(['credit', 'cash', 'free'])],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date', 'after:today'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'farmer_id' => 'farmer',
            'farm_id' => 'farm',
            'season_id' => 'season',
            'stock_item_id' => 'stock item',
            'distribution_type' => 'distribution type',
            'due_date' => 'due date',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.min' => 'The quantity must be at least 0.01.',
            'due_date.after' => 'The due date must be a future date.',
            'distribution_type.in' => 'The distribution type must be credit, cash, or free.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if stock item has sufficient quantity
            if ($this->stock_item_id) {
                $stockItem = \App\Models\Stock\StockItem::find($this->stock_item_id);
                if ($stockItem && $stockItem->quantity_available < $this->quantity) {
                    $validator->errors()->add('quantity', 'Insufficient stock. Available: ' . $stockItem->quantity_available);
                }
            }

            // Due date is required for credit distributions
            if ($this->distribution_type === 'credit' && empty($this->due_date)) {
                $validator->errors()->add('due_date', 'Due date is required for credit distributions.');
            }
        });
    }
}

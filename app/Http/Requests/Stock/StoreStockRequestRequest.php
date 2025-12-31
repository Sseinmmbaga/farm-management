<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farmer_id' => ['nullable', 'exists:farmers,id'],
            'season_id' => ['nullable', 'exists:seasons,id'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'needed_by' => ['nullable', 'date', 'after_or_equal:today'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.stock_item_id' => ['required', 'exists:stock_items,id'],
            'items.*.quantity_requested' => ['required', 'numeric', 'min:0.01'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'farmer_id' => 'farmer',
            'season_id' => 'season',
            'needed_by' => 'needed by date',
            'items' => 'request items',
            'items.*.stock_item_id' => 'stock item',
            'items.*.quantity_requested' => 'requested quantity',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'At least one item is required for the request.',
            'items.min' => 'At least one item is required for the request.',
            'items.*.quantity_requested.min' => 'Each item must have a quantity of at least 0.01.',
            'priority.in' => 'Priority must be low, normal, high, or urgent.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'priority' => $this->priority ?? 'normal',
        ]);
    }
}

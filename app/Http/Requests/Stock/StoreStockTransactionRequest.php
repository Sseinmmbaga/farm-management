<?php

namespace App\Http\Requests\Stock;

use App\Enums\StockTransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'stock_item_id' => ['required', 'exists:stock_items,id'],
            'transaction_type' => ['required', Rule::in(array_column(StockTransactionType::cases(), 'value'))],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'transaction_date' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'farmer_id' => ['nullable', 'exists:farmers,id'],
            'farm_id' => ['nullable', 'exists:farms,id'],
            'batch_id' => ['nullable', 'exists:stock_batches,id'],
            'notes' => ['nullable', 'string'],
        ];

        // Destination is required for issuance and distribution
        if (in_array($this->transaction_type, ['issuance', 'distribution'])) {
            $rules['destination'] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'stock_item_id' => 'stock item',
            'transaction_type' => 'transaction type',
            'unit_cost' => 'unit cost',
            'reference_number' => 'reference number',
            'transaction_date' => 'transaction date',
            'farmer_id' => 'farmer',
            'farm_id' => 'farm',
            'batch_id' => 'batch',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.min' => 'The quantity must be at least 0.01.',
            'destination.required' => 'Destination is required for issuance and distribution transactions.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // For outgoing transactions, check if stock is sufficient
            if (in_array($this->transaction_type, ['issuance', 'distribution', 'transfer'])) {
                $stockItem = \App\Models\Stock\StockItem::find($this->stock_item_id);
                if ($stockItem && $stockItem->quantity_available < $this->quantity) {
                    $validator->errors()->add(
                        'quantity',
                        "Insufficient stock. Available: {$stockItem->quantity_available}"
                    );
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'transaction_date' => $this->transaction_date ?? now()->toDateString(),
        ]);
    }
}

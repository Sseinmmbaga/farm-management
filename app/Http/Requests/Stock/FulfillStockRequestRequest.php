<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class FulfillStockRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:stock_request_items,id'],
            'items.*.quantity_fulfilled' => ['required', 'numeric', 'min:0'],
            'fulfillment_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'items' => 'request items',
            'items.*.id' => 'request item',
            'items.*.quantity_fulfilled' => 'fulfilled quantity',
            'fulfillment_notes' => 'fulfillment notes',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Please specify the items to fulfill.',
            'items.*.quantity_fulfilled.min' => 'Fulfilled quantity cannot be negative.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (is_array($this->items)) {
                foreach ($this->items as $index => $item) {
                    if (!empty($item['id']) && isset($item['quantity_fulfilled'])) {
                        $requestItem = \App\Models\Stock\StockRequestItem::with('stockItem')->find($item['id']);

                        if ($requestItem) {
                            // Check if fulfilled quantity exceeds approved quantity
                            if ($item['quantity_fulfilled'] > $requestItem->quantity_approved) {
                                $validator->errors()->add(
                                    "items.{$index}.quantity_fulfilled",
                                    "Fulfilled quantity cannot exceed approved quantity ({$requestItem->quantity_approved})."
                                );
                            }

                            // Check if sufficient stock is available
                            if ($requestItem->stockItem && $item['quantity_fulfilled'] > $requestItem->stockItem->quantity_available) {
                                $validator->errors()->add(
                                    "items.{$index}.quantity_fulfilled",
                                    "Insufficient stock. Available: {$requestItem->stockItem->quantity_available}."
                                );
                            }
                        }
                    }
                }
            }
        });
    }
}

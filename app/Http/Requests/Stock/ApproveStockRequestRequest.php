<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class ApproveStockRequestRequest extends FormRequest
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
            'items.*.quantity_approved' => ['required', 'numeric', 'min:0'],
            'approval_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'items' => 'request items',
            'items.*.id' => 'request item',
            'items.*.quantity_approved' => 'approved quantity',
            'approval_notes' => 'approval notes',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Please specify the items to approve.',
            'items.*.quantity_approved.min' => 'Approved quantity cannot be negative.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that approved quantity doesn't exceed requested
            if (is_array($this->items)) {
                foreach ($this->items as $index => $item) {
                    if (!empty($item['id']) && isset($item['quantity_approved'])) {
                        $requestItem = \App\Models\Stock\StockRequestItem::find($item['id']);
                        if ($requestItem && $item['quantity_approved'] > $requestItem->quantity_requested) {
                            $validator->errors()->add(
                                "items.{$index}.quantity_approved",
                                "Approved quantity cannot exceed requested quantity ({$requestItem->quantity_requested})."
                            );
                        }
                    }
                }
            }
        });
    }
}

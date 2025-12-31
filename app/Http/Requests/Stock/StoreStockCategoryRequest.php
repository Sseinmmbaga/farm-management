<?php

namespace App\Http\Requests\Stock;

use App\Models\Stock\StockCategory;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'name_sw' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:stock_categories,code'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:stock_categories,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name_sw' => 'name (Swahili)',
            'parent_id' => 'parent category',
            'is_active' => 'active status',
            'sort_order' => 'sort order',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'sort_order' => $this->sort_order ?? 0,
        ]);
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = $this->generateCode();
        }

        return $validated;
    }

    private function generateCode(): string
    {
        $prefix = 'CAT';
        $count = StockCategory::count() + 1;
        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
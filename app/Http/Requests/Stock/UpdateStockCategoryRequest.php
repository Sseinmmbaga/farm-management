<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStockCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category') ?? $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'name_sw' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('stock_categories')->ignore($categoryId)],
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
}
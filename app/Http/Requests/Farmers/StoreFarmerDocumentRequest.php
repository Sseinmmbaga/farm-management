<?php

namespace App\Http\Requests\Farmers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFarmerDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in([
                'national_id',
                'certificate',
                'contract',
                'photo',
                'land_title',
                'other'
            ])],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'], // 10MB max
            'issue_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => 'document type',
            'title' => 'document title',
            'file' => 'document file',
            'issue_date' => 'issue date',
            'expiry_date' => 'expiry date',
            'notes' => 'notes',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'uploaded_by' => auth()->id(),
        ]);
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Remove file from validated array as we will handle separately
        unset($validated['file']);

        // Ensure farmer_id is present from route parameter
        if ($this->route('farmer')) {
            $validated['farmer_id'] = $this->route('farmer')->id;
        }

        return $validated;
    }
}
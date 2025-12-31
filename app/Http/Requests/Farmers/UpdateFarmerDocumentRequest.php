<?php

namespace App\Http\Requests\Farmers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFarmerDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'required', 'string', Rule::in([
                'national_id',
                'certificate',
                'contract',
                'photo',
                'land_title',
                'other'
            ])],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'max:10240'], // 10MB max
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

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Remove file from validated array as we will handle separately
        if (isset($validated['file'])) {
            unset($validated['file']);
        }

        return $validated;
    }
}
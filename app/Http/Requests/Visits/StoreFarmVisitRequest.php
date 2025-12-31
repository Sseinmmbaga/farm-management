<?php

namespace App\Http\Requests\Visits;

use App\Models\Farms\FarmVisit;
use Illuminate\Foundation\Http\FormRequest;

class StoreFarmVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farm_id' => ['required', 'exists:farms,id'],
            'field_id' => ['nullable', 'exists:fields,id'],
            'farmer_id' => ['nullable', 'exists:farmers,id'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'village_id' => ['nullable', 'exists:villages,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'scheduled_date' => ['required', 'date'],
            'actual_date' => ['nullable', 'date'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
            'purpose' => ['required', 'in:inspection,training,support,monitoring,other'],
            'notes' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['nullable', 'image', 'max:5120'],
            'report' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'farm_id' => 'farm',
            'field_id' => 'field',
            'farmer_id' => 'farmer',
            'supervisor_id' => 'supervisor',
            'region_id' => 'region',
            'district_id' => 'district',
            'village_id' => 'village',
            'scheduled_date' => 'scheduled date',
            'actual_date' => 'actual date',
            'photos' => 'photos',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->status ?? 'scheduled',
            'purpose' => $this->purpose ?? 'inspection',
        ]);
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Generate visit number if not provided
        if (empty($validated['visit_number'])) {
            $validated['visit_number'] = FarmVisit::generateVisitNumber();
        }

        // Set created_by and updated_by
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        return $validated;
    }
}
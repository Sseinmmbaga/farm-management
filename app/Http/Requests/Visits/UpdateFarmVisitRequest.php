<?php

namespace App\Http\Requests\Visits;

use App\Models\Farms\FarmVisit;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFarmVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $visit = $this->route('farm_visit') ?? $this->route('visit');
        $visitId = $visit instanceof FarmVisit ? $visit->id : $visit;

        return [
            'farm_id' => ['sometimes', 'exists:farms,id'],
            'field_id' => ['nullable', 'exists:fields,id'],
            'farmer_id' => ['nullable', 'exists:farmers,id'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'village_id' => ['nullable', 'exists:villages,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'scheduled_date' => ['sometimes', 'date'],
            'actual_date' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:scheduled,in_progress,completed,cancelled'],
            'purpose' => ['sometimes', 'in:inspection,training,support,monitoring,other'],
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
        // No need to set defaults
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Set updated_by
        $validated['updated_by'] = auth()->id();

        return $validated;
    }
}
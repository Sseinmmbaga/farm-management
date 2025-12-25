<?php

namespace App\Http\Requests\Tasks;

use App\Enums\TaskType;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('task'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'type' => 'required|in:' . implode(',', TaskType::values()),
            'status' => 'required|in:' . implode(',', TaskStatus::values()),
            'priority' => 'required|in:' . implode(',', TaskPriority::values()),
            'farm_id' => 'nullable|exists:farms,id',
            'field_id' => 'nullable|exists:fields,id',
            'farmer_id' => 'nullable|exists:farmers,id',
            'season_id' => 'nullable|exists:seasons,id',
            'planned_start_date' => 'nullable|date',
            'planned_end_date' => 'nullable|date|after_or_equal:planned_start_date',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
            'actual_hours' => 'nullable|numeric|min:0|max:9999',
            'estimated_cost' => 'nullable|numeric|min:0|max:9999999.99',
            'actual_cost' => 'nullable|numeric|min:0|max:9999999.99',
            'notes' => 'nullable|string|max:5000',
            'completion_notes' => 'nullable|string|max:5000',
            'equipment_required' => 'nullable|array',
            'equipment_required.*' => 'string|max:255',
            'materials_required' => 'nullable|array',
            'materials_required.*' => 'string|max:255',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'task title',
            'type' => 'task type',
            'status' => 'task status',
            'priority' => 'task priority',
            'farm_id' => 'farm',
            'field_id' => 'field',
            'farmer_id' => 'farmer',
            'season_id' => 'season',
            'planned_start_date' => 'planned start date',
            'planned_end_date' => 'planned end date',
            'estimated_hours' => 'estimated hours',
            'actual_hours' => 'actual hours',
            'estimated_cost' => 'estimated cost',
            'actual_cost' => 'actual cost',
            'equipment_required' => 'required equipment',
            'materials_required' => 'required materials',
            'completion_notes' => 'completion notes',
        ];
    }
}

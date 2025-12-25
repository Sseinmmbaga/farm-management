<?php

namespace App\Http\Requests\Tasks;

use App\Enums\LaborType;
use Illuminate\Foundation\Http\FormRequest;

class StoreLaborRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Tasks\Labor::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'task_id' => 'nullable|exists:tasks,id',
            'farm_id' => 'nullable|exists:farms,id',
            'field_id' => 'nullable|exists:fields,id',
            'worker_type' => 'nullable|string|in:App\\Models\\User,App\\Models\\Farmers\\Farmer',
            'worker_id' => 'nullable|integer|required_with:worker_type',
            'worker_name' => 'nullable|string|max:255|required_without_all:worker_type,worker_id',
            'labor_type' => 'required|in:' . implode(',', LaborType::values()),
            'work_date' => 'required|date|before_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'hours_worked' => 'required|numeric|min:0.25|max:24',
            'overtime_hours' => 'nullable|numeric|min:0|max:12',
            'break_minutes' => 'nullable|integer|min:0|max:480',
            'hourly_rate' => 'nullable|numeric|min:0|max:9999.99',
            'overtime_rate' => 'nullable|numeric|min:0|max:9999.99',
            'work_description' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'weather_conditions' => 'nullable|string|max:100',
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
            'task_id' => 'task',
            'farm_id' => 'farm',
            'field_id' => 'field',
            'worker_type' => 'worker type',
            'worker_id' => 'worker',
            'worker_name' => 'worker name',
            'labor_type' => 'labor type',
            'work_date' => 'work date',
            'start_time' => 'start time',
            'end_time' => 'end time',
            'hours_worked' => 'hours worked',
            'overtime_hours' => 'overtime hours',
            'break_minutes' => 'break time',
            'hourly_rate' => 'hourly rate',
            'overtime_rate' => 'overtime rate',
            'work_description' => 'work description',
            'weather_conditions' => 'weather conditions',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'worker_name.required_without_all' => 'Please provide either a worker name or select a worker from the system.',
            'worker_id.required_with' => 'Please select a worker when specifying a worker type.',
            'work_date.before_or_equal' => 'Work date cannot be in the future.',
            'end_time.after' => 'End time must be after the start time.',
            'hours_worked.min' => 'Hours worked must be at least 15 minutes (0.25 hours).',
        ];
    }
}

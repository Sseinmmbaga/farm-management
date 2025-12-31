<?php

namespace App\Http\Requests\Farmers;

use App\Models\Farmers\Farmer;
use Illuminate\Foundation\Http\FormRequest;

class StoreFarmerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Personal Information
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'phone' => ['required', 'string', 'max:20'],
            'phone_alt' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'spouse_title' => ['nullable', 'string', 'max:255'],

            // Location Information
            'region_id' => ['required', 'exists:regions,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'village_id' => ['required', 'exists:villages,id'],
            'subvillage' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],

            // Group & Assignment
            'group_id' => ['nullable', 'exists:farmer_groups,id'],
            'extension_officer_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:active,inactive,suspended,pending'],

            // ID Information
            'id_type' => ['nullable', 'string', 'max:50'],
            'id_number' => ['nullable', 'string', 'max:100'],

            // Additional Information
            'notes' => ['nullable', 'string'],
            'land_size_description' => ['nullable', 'string', 'max:255'],
            'cotton_producers_count' => ['nullable', 'string', 'max:255'],
            'lead_farmer' => ['nullable', 'string', 'max:255'],
            'demo_farm' => ['nullable', 'string', 'max:255'],
            'owns_farming_tools' => ['nullable', 'string', 'max:255'],
            'last_prohibited_chemicals_use' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'date_of_birth' => 'date of birth',
            'region_id' => 'region',
            'district_id' => 'district',
            'village_id' => 'village',
            'group_id' => 'farmer group',
            'extension_officer_id' => 'extension officer',
            'spouse_name' => 'Na. ya Bw/Bi Shamba',
            'spouse_title' => 'Bw/Bi Shamba',
            'land_size_description' => 'Ukubwa wa eneo',
            'cotton_producers_count' => 'Idadi ya wazalishaji wa pamba',
            'lead_farmer' => 'Mkulima Kiongozi',
            'demo_farm' => 'Shamba darasa',
            'owns_farming_tools' => 'Anamiliki zana za Kilimo',
            'last_prohibited_chemicals_use' => 'Mara ya mwisho kutumia madawa yasiyoruhusiwa',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'farmer_group_id' => $this->group_id,
            'national_id' => $this->id_number,
        ]);
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Map form field names to database field names
        if (isset($validated['group_id'])) {
            $validated['farmer_group_id'] = $validated['group_id'];
            unset($validated['group_id']);
        }

        if (isset($validated['id_number'])) {
            $validated['national_id'] = $validated['id_number'];
            unset($validated['id_number']);
            unset($validated['id_type']);
        }

        // Generate registration number
        $validated['registration_number'] = Farmer::generateRegistrationNumber();
        $validated['registration_date'] = now();

        // Keep password in validated data for FarmerService to use
        // It will be used to create the User account

        return $validated;
    }
}

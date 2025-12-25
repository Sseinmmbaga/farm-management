<?php

namespace App\Http\Requests\Farms;

use App\Enums\UserRole;
use App\Models\Farms\Farm;
use App\Models\Farmers\Farmer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreFarmRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farm_id' => [
                'required',
                'exists:farms,id',
                Rule::unique('farm_records')->where(function ($query) {
                    return $query->where('season_id', $this->season_id)
                                 ->where('record_type', $this->record_type)
                                 ->whereNull('deleted_at');
                }),
            ],
            'season_id' => ['required', 'exists:seasons,id'],
            'record_type' => ['required', 'in:new,existing'],

            // Certification
            'certification_status' => ['nullable', 'in:C0,C1,C2,O'],

            // Livestock
            'cattle_count' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'goats_sheep_count' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'oxen_count' => ['nullable', 'integer', 'min:0', 'max:10000'],

            // Equipment
            'has_input_book' => ['nullable', 'boolean'],
            'has_pump' => ['nullable', 'boolean'],

            // Chemical/Residue
            'has_chemical_seed_residue' => ['nullable', 'boolean'],
            'has_chemical_residue' => ['nullable', 'boolean'],

            // Area
            'area_size' => ['nullable', 'numeric', 'min:0', 'max:100000'],

            // Land changes (for existing farms)
            'land_bought' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'land_sold' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'land_borrowed' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'land_lent' => ['nullable', 'numeric', 'min:0', 'max:100000'],

            // Registration
            'registration_year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'impact_type' => ['nullable', 'string', 'max:255'],

            // Notes
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'farm_id' => 'farm',
            'season_id' => 'season',
            'record_type' => 'record type',
            'certification_status' => 'certification status',
            'cattle_count' => 'number of cattle',
            'goats_sheep_count' => 'number of goats/sheep',
            'oxen_count' => 'number of oxen',
            'has_input_book' => 'input book ownership',
            'has_pump' => 'pump ownership',
            'has_chemical_seed_residue' => 'chemical seed residue',
            'has_chemical_residue' => 'chemical residue',
            'area_size' => 'farm area size',
            'land_bought' => 'land bought',
            'land_sold' => 'land sold',
            'land_borrowed' => 'land borrowed',
            'land_lent' => 'land lent',
            'registration_year' => 'registration year',
            'impact_type' => 'impact type',
        ];
    }

    public function messages(): array
    {
        return [
            'farm_id.unique' => 'A record already exists for this farm, season, and record type.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Convert checkbox values to boolean
        $this->merge([
            'has_input_book' => $this->boolean('has_input_book'),
            'has_pump' => $this->boolean('has_pump'),
            'has_chemical_seed_residue' => $this->boolean('has_chemical_seed_residue'),
            'has_chemical_residue' => $this->boolean('has_chemical_residue'),
        ]);
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Get farmer_id from farm
        $farm = Farm::find($validated['farm_id']);
        $validated['farmer_id'] = $farm->farmer_id;

        // Set defaults for counts
        $validated['cattle_count'] = $validated['cattle_count'] ?? 0;
        $validated['goats_sheep_count'] = $validated['goats_sheep_count'] ?? 0;
        $validated['oxen_count'] = $validated['oxen_count'] ?? 0;

        return $validated;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateFarmerAccess($validator);
        });
    }

    private function validateFarmerAccess($validator): void
    {
        if (!$this->farm_id) {
            return;
        }

        $farm = Farm::find($this->farm_id);
        if (!$farm) {
            return;
        }

        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::SUPERVISOR)) {
            return;
        }

        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id')->toArray();
            if (!in_array($farm->farmer_id, $assignedFarmerIds)) {
                $validator->errors()->add('farm_id', 'You can only create records for farmers assigned to you.');
            }
            return;
        }

        if ($user->hasRole(UserRole::FARMER)) {
            if ($user->farmer->id != $farm->farmer_id) {
                $validator->errors()->add('farm_id', 'You can only create records for your own farms.');
            }
        }
    }
}

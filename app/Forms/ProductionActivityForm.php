<?php

namespace App\Forms;

class ProductionActivityForm extends FormTemplate
{
    public function __construct()
    {
        $this->formType = 'production';
        $this->formSubtype = 'activity';
        $this->title = 'Production Activity Record';
        $this->description = 'Record farming production activities (planting, cultivation, harvesting)';
    }

    public function getDefinition(): array
    {
        return [
            'form_type' => $this->formType,
            'form_subtype' => $this->formSubtype,
            'title' => $this->title,
            'description' => $this->description,
            'sections' => $this->getSections(),
            'required_fields' => $this->requiredFields,
        ];
    }

    public function getSections(): array
    {
        // Clear any existing sections
        $this->sections = [];
        
        // Activity Details Section
        $this->createSection(
            'activity_details',
            'Activity Details',
            'Basic information about the production activity',
            [
                $this->createField(
                    'activity_type',
                    'Activity Type',
                    'select',
                    [
                        'options' => [
                            'land_preparation' => 'Land Preparation',
                            'planting' => 'Planting',
                            'weeding' => 'Weeding',
                            'fertilizer_application' => 'Fertilizer Application',
                            'pesticide_application' => 'Pesticide Application',
                            'irrigation' => 'Irrigation',
                            'harvesting' => 'Harvesting',
                            'post_harvest' => 'Post-Harvest Processing',
                            'other' => 'Other Activity',
                        ]
                    ],
                    true,
                    'Type of production activity'
                ),
                $this->createField(
                    'activity_date',
                    'Activity Date',
                    'date',
                    [],
                    true,
                    'Date when activity was performed'
                ),
                $this->createField(
                    'start_time',
                    'Start Time',
                    'time',
                    [],
                    false,
                    'Activity start time'
                ),
                $this->createField(
                    'end_time',
                    'End Time',
                    'time',
                    [],
                    false,
                    'Activity end time'
                ),
                $this->createField(
                    'crop_type',
                    'Crop Type',
                    'select',
                    [
                        'options' => [
                            'cotton' => 'Cotton',
                            'sesame' => 'Sesame',
                            'maize' => 'Maize',
                            'sorghum' => 'Sorghum',
                            'millet' => 'Millet',
                            'groundnut' => 'Groundnut',
                            'soybean' => 'Soybean',
                            'vegetables' => 'Vegetables',
                            'other' => 'Other',
                        ]
                    ],
                    true,
                    'Crop involved in the activity'
                ),
                $this->createField(
                    'crop_variety',
                    'Crop Variety',
                    'text',
                    [],
                    false,
                    'Specific variety of the crop'
                ),
                $this->createField(
                    'growth_stage',
                    'Growth Stage',
                    'select',
                    [
                        'options' => [
                            'seedling' => 'Seedling',
                            'vegetative' => 'Vegetative',
                            'flowering' => 'Flowering',
                            'fruiting' => 'Fruiting',
                            'maturity' => 'Maturity',
                            'harvest' => 'Ready for Harvest',
                        ]
                    ],
                    false,
                    'Growth stage of the crop'
                ),
            ],
            1
        );

        // Area and Quantity Section
        $this->createSection(
            'area_quantity',
            'Area and Quantity',
            'Details of area covered and quantities involved',
            [
                $this->createField(
                    'area_covered',
                    'Area Covered',
                    'number',
                    ['min' => 0, 'step' => 0.01],
                    true,
                    'Area covered by this activity (in acres/hectares)'
                ),
                $this->createField(
                    'area_unit',
                    'Area Unit',
                    'select',
                    [
                        'options' => [
                            'acres' => 'Acres',
                            'hectares' => 'Hectares',
                        ]
                    ],
                    true,
                    'Unit of measurement for area'
                ),
                $this->createField(
                    'quantity_used',
                    'Quantity Used',
                    'number',
                    ['min' => 0, 'step' => 0.01],
                    false,
                    'Quantity of inputs/materials used (if applicable)'
                ),
                $this->createField(
                    'quantity_unit',
                    'Quantity Unit',
                    'text',
                    [],
                    false,
                    'Unit for quantity (kg, liters, bags, etc.)'
                ),
                $this->createField(
                    'yield_quantity',
                    'Yield Quantity',
                    'number',
                    ['min' => 0, 'step' => 0.01],
                    false,
                    'Quantity harvested (for harvesting activities)'
                ),
                $this->createField(
                    'yield_unit',
                    'Yield Unit',
                    'text',
                    [],
                    false,
                    'Unit for yield (kg, tons, bags, etc.)'
                ),
            ],
            2
        );

        // Inputs and Materials Section
        $this->createSection(
            'inputs_materials',
            'Inputs and Materials',
            'Details of inputs and materials used',
            [
                $this->createField(
                    'input_type',
                    'Input Type',
                    'select',
                    [
                        'options' => [
                            'fertilizer' => 'Fertilizer',
                            'pesticide' => 'Pesticide',
                            'herbicide' => 'Herbicide',
                            'fungicide' => 'Fungicide',
                            'seed' => 'Seed',
                            'water' => 'Water',
                            'fuel' => 'Fuel',
                            'other' => 'Other',
                        ]
                    ],
                    false,
                    'Type of input used'
                ),
                $this->createField(
                    'input_name',
                    'Input Name/Brand',
                    'text',
                    [],
                    false,
                    'Name or brand of the input'
                ),
                $this->createField(
                    'application_method',
                    'Application Method',
                    'select',
                    [
                        'options' => [
                            'broadcast' => 'Broadcast',
                            'banding' => 'Banding',
                            'foliar' => 'Foliar Spray',
                            'drip' => 'Drip Application',
                            'hand' => 'Hand Application',
                            'machine' => 'Machine Application',
                            'other' => 'Other',
                        ]
                    ],
                    false,
                    'Method of application'
                ),
                $this->createField(
                    'application_rate',
                    'Application Rate',
                    'text',
                    [],
                    false,
                    'Rate of application (e.g., 100kg/ha, 2L/acre)'
                ),
                $this->createField(
                    'equipment_used',
                    'Equipment Used',
                    'textarea',
                    ['rows' => 3],
                    false,
                    'Equipment or tools used for the activity'
                ),
            ],
            3
        );

        // Labor Section
        $this->createSection(
            'labor',
            'Labor Information',
            'Details of labor involved in the activity',
            [
                $this->createField(
                    'labor_type',
                    'Labor Type',
                    'select',
                    [
                        'options' => [
                            'family' => 'Family Labor',
                            'hired' => 'Hired Labor',
                            'exchange' => 'Labor Exchange',
                            'volunteer' => 'Volunteer',
                            'contract' => 'Contract Labor',
                        ]
                    ],
                    false,
                    'Type of labor used'
                ),
                $this->createField(
                    'number_of_workers',
                    'Number of Workers',
                    'number',
                    ['min' => 0],
                    false,
                    'Total number of workers involved'
                ),
                $this->createField(
                    'male_workers',
                    'Male Workers',
                    'number',
                    ['min' => 0],
                    false,
                    'Number of male workers'
                ),
                $this->createField(
                    'female_workers',
                    'Female Workers',
                    'number',
                    ['min' => 0],
                    false,
                    'Number of female workers'
                ),
                $this->createField(
                    'total_labor_hours',
                    'Total Labor Hours',
                    'number',
                    ['min' => 0, 'step' => 0.5],
                    false,
                    'Total hours of labor spent'
                ),
                $this->createField(
                    'labor_cost',
                    'Labor Cost',
                    'number',
                    ['min' => 0, 'step' => 0.01],
                    false,
                    'Total cost of labor (if applicable)'
                ),
                $this->createField(
                    'currency',
                    'Currency',
                    'text',
                    [],
                    false,
                    'Currency for costs (e.g., USD, EUR, local currency)'
                ),
            ],
            4
        );

        // Observations and Notes Section
        $this->createSection(
            'observations',
            'Observations and Notes',
            'Observations, challenges, and additional notes',
            [
                $this->createField(
                    'weather_conditions',
                    'Weather Conditions',
                    'textarea',
                    ['rows' => 2],
                    false,
                    'Weather conditions during the activity'
                ),
                $this->createField(
                    'crop_condition',
                    'Crop Condition',
                    'select',
                    [
                        'options' => [
                            'excellent' => 'Excellent',
                            'good' => 'Good',
                            'fair' => 'Fair',
                            'poor' => 'Poor',
                            'critical' => 'Critical',
                        ]
                    ],
                    false,
                    'Overall condition of the crop'
                ),
                $this->createField(
                    'pest_pressure',
                    'Pest Pressure',
                    'select',
                    [
                        'options' => [
                            'none' => 'None',
                            'low' => 'Low',
                            'moderate' => 'Moderate',
                            'high' => 'High',
                            'severe' => 'Severe',
                        ]
                    ],
                    false,
                    'Level of pest pressure observed'
                ),
                $this->createField(
                    'disease_incidence',
                    'Disease Incidence',
                    'select',
                    [
                        'options' => [
                            'none' => 'None',
                            'low' => 'Low',
                            'moderate' => 'Moderate',
                            'high' => 'High',
                            'severe' => 'Severe',
                        ]
                    ],
                    false,
                    'Level of disease incidence observed'
                ),
                $this->createField(
                    'challenges_encountered',
                    'Challenges Encountered',
                    'textarea',
                    ['rows' => 3],
                    false,
                    'Any challenges faced during the activity'
                ),
                $this->createField(
                    'additional_notes',
                    'Additional Notes',
                    'textarea',
                    ['rows' => 3],
                    false,
                    'Any additional notes or observations'
                ),
            ],
            5
        );

        return $this->sections;
    }

    public function getDefaultValues(): array
    {
        return [
            'activity_date' => date('Y-m-d'),
            'activity_type' => 'land_preparation',
            'crop_type' => 'cotton',
            'area_unit' => 'acres',
            'labor_type' => 'family',
            'crop_condition' => 'good',
            'pest_pressure' => 'low',
            'disease_incidence' => 'low',
        ];
    }

    public function validate(array $data): array
    {
        $errors = parent::validate($data);

        // Custom validation for production form
        if (isset($data['male_workers']) && isset($data['female_workers']) && isset($data['number_of_workers'])) {
            $sum = (int)$data['male_workers'] + (int)$data['female_workers'];
            if ($sum > (int)$data['number_of_workers']) {
                $errors['number_of_workers'] = 'Sum of male and female workers cannot exceed total workers';
            }
        }

        if (isset($data['area_covered']) && $data['area_covered'] <= 0) {
            $errors['area_covered'] = 'Area covered must be greater than 0';
        }

        return $errors;
    }

    public function processData(array $data): array
    {
        // Calculate total labor hours if start and end times are provided
        if (isset($data['start_time']) && isset($data['end_time'])) {
            $start = strtotime($data['start_time']);
            $end = strtotime($data['end_time']);
            if ($end > $start) {
                $duration = ($end - $start) / 3600; // Convert to hours
                if (isset($data['number_of_workers']) && $data['number_of_workers'] > 0) {
                    $data['total_labor_hours'] = round($duration * $data['number_of_workers'], 1);
                }
            }
        }

        // Ensure numeric fields are properly formatted
        $numericFields = [
            'area_covered', 'quantity_used', 'yield_quantity',
            'number_of_workers', 'male_workers', 'female_workers',
            'total_labor_hours', 'labor_cost'
        ];

        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = (float)$data[$field];
            }
        }

        return $data;
    }
}

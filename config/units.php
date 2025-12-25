<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Units of Measurement
    |--------------------------------------------------------------------------
    | Define all units used in the system organized by category
    */

    'categories' => [
        'weight' => 'Weight/Mass',
        'area' => 'Area',
        'volume' => 'Volume',
        'count' => 'Count/Quantity',
        'length' => 'Length/Distance',
    ],

    'units' => [

        // Weight Units
        'weight' => [
            [
                'name' => 'Kilogram',
                'name_sw' => 'Kilogramu',
                'symbol' => 'kg',
                'conversion_factor' => 1, // Base unit
                'is_base' => true,
            ],
            [
                'name' => 'Gram',
                'name_sw' => 'Gramu',
                'symbol' => 'g',
                'conversion_factor' => 0.001,
                'base_unit' => 'kg',
            ],
            [
                'name' => 'Metric Ton',
                'name_sw' => 'Tani',
                'symbol' => 'ton',
                'conversion_factor' => 1000,
                'base_unit' => 'kg',
            ],
            [
                'name' => 'Pound',
                'name_sw' => 'Paundi',
                'symbol' => 'lb',
                'conversion_factor' => 0.453592,
                'base_unit' => 'kg',
            ],
            [
                'name' => 'Bag (50kg)',
                'name_sw' => 'Mfuko (50kg)',
                'symbol' => 'bag',
                'conversion_factor' => 50,
                'base_unit' => 'kg',
            ],
        ],

        // Area Units
        'area' => [
            [
                'name' => 'Hectare',
                'name_sw' => 'Hekta',
                'symbol' => 'ha',
                'conversion_factor' => 1, // Base unit
                'is_base' => true,
            ],
            [
                'name' => 'Acre',
                'name_sw' => 'Ekari',
                'symbol' => 'acre',
                'conversion_factor' => 0.404686,
                'base_unit' => 'ha',
            ],
            [
                'name' => 'Square Meter',
                'name_sw' => 'Mita ya Mraba',
                'symbol' => 'm²',
                'conversion_factor' => 0.0001,
                'base_unit' => 'ha',
            ],
        ],

        // Volume Units
        'volume' => [
            [
                'name' => 'Liter',
                'name_sw' => 'Lita',
                'symbol' => 'L',
                'conversion_factor' => 1, // Base unit
                'is_base' => true,
            ],
            [
                'name' => 'Milliliter',
                'name_sw' => 'Mililita',
                'symbol' => 'ml',
                'conversion_factor' => 0.001,
                'base_unit' => 'L',
            ],
            [
                'name' => 'Cubic Meter',
                'name_sw' => 'Mita ya Ujazo',
                'symbol' => 'm³',
                'conversion_factor' => 1000,
                'base_unit' => 'L',
            ],
        ],

        // Count Units
        'count' => [
            [
                'name' => 'Piece',
                'name_sw' => 'Kipande',
                'symbol' => 'pc',
                'conversion_factor' => 1,
                'is_base' => true,
            ],
            [
                'name' => 'Dozen',
                'name_sw' => 'Dazeni',
                'symbol' => 'dz',
                'conversion_factor' => 12,
                'base_unit' => 'pc',
            ],
            [
                'name' => 'Pack',
                'name_sw' => 'Pakiti',
                'symbol' => 'pack',
                'conversion_factor' => 1,
                'base_unit' => 'pc',
            ],
        ],

        // Length Units
        'length' => [
            [
                'name' => 'Meter',
                'name_sw' => 'Mita',
                'symbol' => 'm',
                'conversion_factor' => 1, // Base unit
                'is_base' => true,
            ],
            [
                'name' => 'Centimeter',
                'name_sw' => 'Sentimita',
                'symbol' => 'cm',
                'conversion_factor' => 0.01,
                'base_unit' => 'm',
            ],
            [
                'name' => 'Kilometer',
                'name_sw' => 'Kilomita',
                'symbol' => 'km',
                'conversion_factor' => 1000,
                'base_unit' => 'm',
            ],
        ],

    ],

];

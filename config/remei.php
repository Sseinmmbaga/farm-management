<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    */
    'name' => env('REMEI_APP_NAME', 'Remei Farm OS'),

    /*
    |--------------------------------------------------------------------------
    | Organization
    |--------------------------------------------------------------------------
    */
    'organization' => [
        'name' => 'REMEI AG',
        'country' => 'Tanzania',
        'currency' => 'TZS',
        'currency_symbol' => 'TSh',
        'timezone' => 'Africa/Dar_es_Salaam',
        'locale' => 'sw',
        'fallback_locale' => 'en',
    ],

    /*
    |--------------------------------------------------------------------------
    | Primary Crops
    |--------------------------------------------------------------------------
    */
    'crops' => [
        'cotton' => [
            'name' => 'Cotton',
            'name_sw' => 'Pamba',
            'is_primary' => true,
            'default_yield_unit' => 'kg',
            'benchmark_yield_per_ha' => 800, // kg/ha
        ],
        'sesame' => [
            'name' => 'Sesame',
            'name_sw' => 'Ufuta',
            'is_primary' => true,
            'default_yield_unit' => 'kg',
            'benchmark_yield_per_ha' => 500,
        ],
        'sunflower' => [
            'name' => 'Sunflower',
            'name_sw' => 'Alizeti',
            'is_primary' => false,
            'default_yield_unit' => 'kg',
            'benchmark_yield_per_ha' => 1200,
        ],
        'maize' => [
            'name' => 'Maize',
            'name_sw' => 'Mahindi',
            'is_primary' => false,
            'default_yield_unit' => 'kg',
            'benchmark_yield_per_ha' => 2000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Farmer Settings
    |--------------------------------------------------------------------------
    */
    'farmer' => [
        'registration_prefix' => 'RMF', // Remei Farmer
        'statuses' => ['pending', 'active', 'inactive', 'suspended'],
        'certification_statuses' => ['pending', 'conventional', 'in-conversion', 'organic'],
        'conversion_years_required' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Farm Settings
    |--------------------------------------------------------------------------
    */
    'farm' => [
        'code_prefix' => 'SH', // Shamba
        'default_area_unit' => 'hectares',
        'soil_types' => ['clay', 'sandy', 'loam', 'silt', 'black_cotton'],
        'water_sources' => ['rainfed', 'river', 'well', 'dam', 'irrigation'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stock Settings
    |--------------------------------------------------------------------------
    */
    'stock' => [
        'low_stock_threshold_percent' => 20,
        'critical_stock_threshold_percent' => 10,
        'default_currency' => 'TZS',
    ],

    /*
    |--------------------------------------------------------------------------
    | ICS Settings
    |--------------------------------------------------------------------------
    */
    'ics' => [
        'passing_score_percent' => 80,
        'critical_failure_threshold' => 1,
        'follow_up_days_default' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Map Settings
    |--------------------------------------------------------------------------
    */
    'map' => [
        'default_center' => [
            'lat' => -6.3690,  // Tanzania center
            'lng' => 34.8888,
        ],
        'default_zoom' => 6,
        'tile_provider' => 'openstreetmap', // or 'mapbox'
        'mapbox_token' => env('MAPBOX_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'max_file_size' => 10240, // KB
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'allowed_document_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'farmer_photos_path' => 'farmers/photos',
        'farm_images_path' => 'farms/images',
        'documents_path' => 'documents',
    ],

];

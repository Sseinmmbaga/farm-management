<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Roles Configuration
    |--------------------------------------------------------------------------
    | Define all user roles and their permissions
    */

    'roles' => [
        'admin' => [
            'name' => 'Administrator',
            'name_sw' => 'Msimamizi Mkuu',
            'description' => 'Full system access',
            'permissions' => ['*'], // All permissions
        ],

        'supervisor' => [
            'name' => 'Supervisor',
            'name_sw' => 'Msimamizi',
            'description' => 'Team management and data review',
            'permissions' => [
                'farmers.view', 'farmers.create', 'farmers.edit',
                'farms.view', 'farms.create', 'farms.edit',
                'logs.view', 'logs.create', 'logs.edit', 'logs.approve',
                'assets.view', 'assets.create', 'assets.edit',
                'training.view', 'training.create',
                'users.view', 'users.assign',
                'reports.view', 'reports.export',
            ],
        ],

        'extension_officer' => [
            'name' => 'Field Extension Officer',
            'name_sw' => 'Afisa Ugani',
            'description' => 'Farmer data collection and field visits',
            'permissions' => [
                'farmers.view', 'farmers.create', 'farmers.edit',
                'farms.view', 'farms.create', 'farms.edit',
                'logs.view', 'logs.create', 'logs.edit',
                'assets.view', 'assets.create',
                'training.view', 'training.attend',
                'reports.view',
            ],
        ],

        'ics_inspector' => [
            'name' => 'ICS Inspector',
            'name_sw' => 'Mkaguzi wa ICS',
            'description' => 'Quality control and inspections',
            'permissions' => [
                'farmers.view',
                'farms.view',
                'inspections.view', 'inspections.create', 'inspections.edit', 'inspections.complete',
                'findings.view', 'findings.create', 'findings.edit',
                'corrective-actions.view', 'corrective-actions.create', 'corrective-actions.verify',
                'certifications.view', 'certifications.update',
                'reports.view', 'reports.export',
            ],
        ],

        'stock_manager' => [
            'name' => 'Stock Manager',
            'name_sw' => 'Meneja wa Ghala',
            'description' => 'Inventory and distribution management',
            'permissions' => [
                'stock.view', 'stock.create', 'stock.edit', 'stock.delete',
                'transactions.view', 'transactions.create', 'transactions.approve',
                'distributions.view', 'distributions.create',
                'requests.view', 'requests.approve', 'requests.fulfill',
                'farmers.view',
                'reports.view', 'reports.export',
            ],
        ],

        'accountant' => [
            'name' => 'Accountant',
            'name_sw' => 'Mhasibu',
            'description' => 'Financial data and reporting',
            'permissions' => [
                'farmers.view',
                'stock.view',
                'transactions.view',
                'distributions.view',
                'reports.view', 'reports.export', 'reports.financial',
            ],
        ],

        'training_coordinator' => [
            'name' => 'Training Coordinator',
            'name_sw' => 'Mratibu wa Mafunzo',
            'description' => 'Training program management',
            'permissions' => [
                'training.view', 'training.create', 'training.edit', 'training.delete',
                'attendance.view', 'attendance.create', 'attendance.edit',
                'certificates.issue',
                'farmers.view',
                'reports.view', 'reports.export',
            ],
        ],

        'production_manager' => [
            'name' => 'Production Manager',
            'name_sw' => 'Meneja wa Uzalishaji',
            'description' => 'Production oversight and planning',
            'permissions' => [
                'farmers.view',
                'farms.view',
                'logs.view',
                'assets.view',
                'harvests.view',
                'reports.view', 'reports.export', 'reports.production',
            ],
        ],

        'farmer' => [
            'name' => 'Farmer',
            'name_sw' => 'Mkulima',
            'description' => 'Farmer self-service portal',
            'permissions' => [
                'profile.view', 'profile.edit',
                'farms.view.own',
                'logs.view.own',
                'training.view.own',
                'distributions.view.own',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Role
    |--------------------------------------------------------------------------
    */
    'default_role' => 'farmer',

    /*
    |--------------------------------------------------------------------------
    | Super Admin Role
    |--------------------------------------------------------------------------
    */
    'super_admin_role' => 'admin',

];

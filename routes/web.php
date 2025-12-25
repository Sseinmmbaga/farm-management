<?php

use Illuminate\Support\Facades\Route;
use App\Models\Location\District;
use App\Models\Location\Village;

// Load all route files
require __DIR__.'/auth.php';
require __DIR__.'/dashboard.php';
require __DIR__.'/farmers.php';
require __DIR__.'/farms.php';
require __DIR__.'/fields.php';
require __DIR__.'/ics.php';
require __DIR__.'/logs.php';
require __DIR__.'/reports.php';
require __DIR__.'/stock.php';
require __DIR__.'/training.php';
require __DIR__.'/assets.php';
require __DIR__.'/field-entries.php';  // Add field entries routes
require __DIR__.'/tasks.php';  // Task & Labor Management (Phase 4)
require __DIR__.'/catchment-areas.php';  // Catchment Areas (Locations)
require __DIR__.'/users.php';  // User Management
require __DIR__.'/settings.php';  // System Settings

// API routes for dynamic dropdowns
Route::get('/api/districts', function () {
    $regionId = request('region_id');
    if (!$regionId) {
        return response()->json([]);
    }
    
    $districts = District::where('region_id', $regionId)
        ->orderBy('name')
        ->get(['id', 'name']);
    
    return response()->json($districts);
});

Route::get('/api/villages', function () {
    $districtId = request('district_id');
    if (!$districtId) {
        return response()->json([]);
    }
    
    $villages = Village::where('district_id', $districtId)
        ->orderBy('name')
        ->get(['id', 'name']);
    
    return response()->json($villages);
});

Route::get('/', function () {
    return view('welcome');
});

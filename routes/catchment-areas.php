<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Location\CatchmentAreaController;
use App\Http\Controllers\Location\RegionController;
use App\Http\Controllers\Location\DistrictController;
use App\Http\Controllers\Location\VillageController;
use App\Http\Controllers\Location\SubvillageController;
use App\Http\Controllers\Location\WardController;
use App\Models\Location\Ward;

Route::middleware(['auth'])->group(function () {
    // Catchment Areas Dashboard
    Route::get('/catchment-areas', [CatchmentAreaController::class, 'index'])->name('catchment-areas.index');

    // Regions
    Route::resource('regions', RegionController::class);

    // Districts
    Route::resource('districts', DistrictController::class);

    // Wards
    Route::resource('wards', WardController::class);

    // Villages
    Route::resource('villages', VillageController::class);

    // Subvillages
    Route::resource('subvillages', SubvillageController::class);

    // API for dynamic dropdowns in catchment area forms
    Route::get('/api/regions/{region}/districts', function ($regionId) {
        return \App\Models\Location\District::where('region_id', $regionId)
            ->orderBy('name')
            ->get(['id', 'name']);
    });

    Route::get('/api/districts/{district}/wards', function ($districtId) {
        return Ward::where('district_id', $districtId)
            ->orderBy('name')
            ->get(['id', 'name']);
    });

    Route::get('/api/districts/{district}/villages', function ($districtId) {
        return \App\Models\Location\Village::where('district_id', $districtId)
            ->orderBy('name')
            ->get(['id', 'name']);
    });

    Route::get('/api/wards/{ward}/villages', function ($wardId) {
        return \App\Models\Location\Village::where('ward_id', $wardId)
            ->orderBy('name')
            ->get(['id', 'name']);
    });

    Route::get('/api/villages/{village}/subvillages', function ($villageId) {
        return \App\Models\Location\Subvillage::where('village_id', $villageId)
            ->orderBy('name')
            ->get(['id', 'name']);
    });
});

<?php

use App\Http\Controllers\Assets\AssetController;
use App\Http\Controllers\Assets\LandAssetController;
use App\Http\Controllers\Assets\CropAssetController;
use App\Http\Controllers\Assets\EquipmentAssetController;
use App\Http\Controllers\Assets\MaterialAssetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Asset Management Routes (farmOS-style)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Asset CRUD
    Route::resource('assets', AssetController::class);

    // Asset type-specific views
    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('type/land', [AssetController::class, 'land'])->name('land');
        Route::get('type/crops', [AssetController::class, 'crops'])->name('crops');
        Route::get('type/equipment', [AssetController::class, 'equipment'])->name('equipment');
        Route::get('type/materials', [AssetController::class, 'materials'])->name('materials');
        Route::get('type/groups', [AssetController::class, 'groups'])->name('groups');

        // Map view
        Route::get('map/view', [AssetController::class, 'map'])->name('map');
    });

    // Land Assets
    Route::resource('land-assets', LandAssetController::class)->except(['index']);

    // Crop Assets
    Route::resource('crop-assets', CropAssetController::class)->except(['index']);
    Route::post('crop-assets/{cropAsset}/update-stage', [CropAssetController::class, 'updateStage'])->name('crop-assets.update-stage');

    // Equipment Assets
    Route::resource('equipment-assets', EquipmentAssetController::class)->except(['index']);
    Route::post('equipment-assets/{equipmentAsset}/maintenance', [EquipmentAssetController::class, 'recordMaintenance'])->name('equipment-assets.maintenance');

    // Material Assets
    Route::resource('material-assets', MaterialAssetController::class)->except(['index']);
});

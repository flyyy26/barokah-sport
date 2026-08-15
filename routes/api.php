<?php

use App\Http\Controllers\Api\RegionController;

Route::prefix('regions')->group(function () {
    Route::get('/provinces', [RegionController::class, 'provinces']);
    Route::get('/cities', [RegionController::class, 'cities']);
    Route::get('/districts', [RegionController::class, 'districts']);
    Route::get('/subdistricts', [RegionController::class, 'subdistricts']);
    Route::get('/search', [RegionController::class, 'search']);
});
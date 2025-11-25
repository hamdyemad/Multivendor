<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\AreaSettings\app\Http\Controllers\CountryController;
use Modules\AreaSettings\app\Http\Controllers\CityController;
use Modules\AreaSettings\app\Http\Controllers\RegionController;
use Modules\AreaSettings\app\Http\Controllers\SubRegionController;

Route::group(['prefix' => 'admin/area-settings', 'as' => 'admin.area-settings.'], function() {
    // Countries
    Route::get('countries/datatable', [CountryController::class, 'datatable'])->name('countries.datatable');
    Route::post('countries/{id}/change-status', [CountryController::class, 'changeStatus'])->name('countries.change-status');
    Route::resource('countries', CountryController::class);

    // Cities
    Route::get('cities/datatable', [CityController::class, 'datatable'])->name('cities.datatable');
    Route::post('cities/{id}/change-status', [CityController::class, 'changeStatus'])->name('cities.change-status');
    Route::get('cities/by-country/{id}', [CityController::class, 'getCitiesByCountry'])->name('cities.by-country');
    Route::resource('cities', CityController::class);

    // Regions
    Route::get('regions/datatable', [RegionController::class, 'datatable'])->name('regions.datatable');
    Route::post('regions/{id}/change-status', [RegionController::class, 'changeStatus'])->name('regions.change-status');
    Route::get('regions/by-city/{id}', [RegionController::class, 'getRegionsByCity'])->name('regions.by-city');
    Route::resource('regions', RegionController::class);

    // SubRegions
    Route::get('subregions/datatable', [SubRegionController::class, 'datatable'])->name('subregions.datatable');
    Route::post('subregions/{id}/change-status', [SubRegionController::class, 'changeStatus'])->name('subregions.change-status');
    Route::get('subregions/by-region/{id}', [SubRegionController::class, 'getSubRegionsByRegion'])->name('subregions.by-region');
    Route::resource('subregions', SubRegionController::class);
});

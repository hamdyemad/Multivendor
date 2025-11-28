<?php

use Illuminate\Support\Facades\Route;
use Modules\CatalogManagement\app\Http\Controllers\VariantsConfigurationController;
use Modules\CatalogManagement\app\Http\Controllers\Api\ProductApiController;

// Variant Configuration API Routes (for product form)
Route::prefix('variant-configurations')->group(function () {
    Route::get('key/{keyId}/tree', [VariantsConfigurationController::class, 'getKeyTree']);
    Route::get('{id}', [VariantsConfigurationController::class, 'show']);
});

// Product API Routes (Public)
Route::prefix('products')->group(function () {
    // Product listing and search
    Route::get('/', [ProductApiController::class, 'index']); // Done
    Route::get('/featured', [ProductApiController::class, 'featured']); // Done
    Route::get('/best-selling', [ProductApiController::class, 'bestSelling']); // Done
    Route::get('/latest', [ProductApiController::class, 'latest']); // Done
    Route::get('/special-offers', [ProductApiController::class, 'specialOffers']); // Done
    Route::get('/{departmentId}/department', [ProductApiController::class, 'getByDepartment']); // Done
    Route::get('/top', [ProductApiController::class, 'top']); // Done
    Route::get('/hot-deals', [ProductApiController::class, 'hotDeals']);

    // Filters
    Route::get('/filters', [ProductApiController::class, 'filters']);
    // TODO: Uncomment when Occasion model is created
    // Route::get('/filters/occasion/{id}', [ProductApiController::class, 'filtersByOccasion']);
    // TODO: Uncomment when BundleCategory model is created
    // Route::get('/filters/bundle-category/{id}', [ProductApiController::class, 'filtersByBundleCategory']);
    Route::get('/categories', [ProductApiController::class, 'categories']);
    Route::get('/brands', [ProductApiController::class, 'brands']);
    Route::get('/price-range', [ProductApiController::class, 'priceRange']);
    Route::get('/tags', [ProductApiController::class, 'tags']);
    Route::get('/inputs', [ProductApiController::class, 'inputs']);
    Route::get('/variants', [ProductApiController::class, 'variants']);

    // Product details
    Route::get('specific-product/{id}/{vendorId}', [ProductApiController::class, 'show']);
    Route::get('/{id}/variants-keys', [ProductApiController::class, 'variantsKeys']);
    Route::get('/{id}/sold-count', [ProductApiController::class, 'soldCount']);

    // Reviews (authenticated)
    // Route::post('/{id}/reviews', [ProductApiController::class, 'storeReview'])->middleware('auth:sanctum'); // Need Review Model
});

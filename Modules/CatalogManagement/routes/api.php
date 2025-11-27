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
    Route::get('/', [ProductApiController::class, 'index']);
    Route::get('/featured', [ProductApiController::class, 'featured']); // missing key in db (featured)
    Route::get('/best-selling', [ProductApiController::class, 'bestSelling']);   // missing key in db (sales)
    Route::get('/latest', [ProductApiController::class, 'latest']);
    Route::get('/special-offers', [ProductApiController::class, 'specialOffers']); // should make a seeder for the variants with the product to see result
    Route::get('/hot-deals', [ProductApiController::class, 'hotDeals']);
    Route::get('/top', [ProductApiController::class, 'top']); // missing key in db (sales)

    // Filters
    Route::get('/filters', [ProductApiController::class, 'filters']);
    // TODO: Uncomment when Occasion model is created
    // Route::get('/filters/occasion/{id}', [ProductApiController::class, 'filtersByOccasion']);
    // TODO: Uncomment when BundleCategory model is created
    // Route::get('/filters/bundle-category/{id}', [ProductApiController::class, 'filtersByBundleCategory']);
    Route::get('/categories', [ProductApiController::class, 'categories']);
    Route::get('/brands', [ProductApiController::class, 'brands']);
    Route::get('/price-range', [ProductApiController::class, 'priceRange']); // should make a seeder for the variants with the product to see result
    Route::get('/tags', [ProductApiController::class, 'tags']);
    Route::get('/inputs', [ProductApiController::class, 'inputs']);
    Route::get('/variants', [ProductApiController::class, 'variants']);  // should make a seeder for the variants with the product to see result

    // Product details
    Route::get('/{id}', [ProductApiController::class, 'show']);
    Route::get('/{id}/variants-keys', [ProductApiController::class, 'variantsKeys']); // should make a seeder for the variants with the product to see result
    Route::get('/{id}/sold-count', [ProductApiController::class, 'soldCount']);
    Route::get('/department/{id}', [ProductApiController::class, 'getByDepartment']);

    // Reviews (authenticated)
    // Route::post('/{id}/reviews', [ProductApiController::class, 'storeReview'])->middleware('auth:sanctum'); // Need Review Model
});

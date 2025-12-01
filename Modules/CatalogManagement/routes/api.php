<?php

use Illuminate\Support\Facades\Route;
use Modules\CatalogManagement\app\Http\Controllers\Api\BrandApiController;
use Modules\CatalogManagement\app\Http\Controllers\VariantsConfigurationController;
use Modules\CatalogManagement\app\Http\Controllers\Api\ProductApiController;
use Modules\CatalogManagement\app\Http\Controllers\Api\ReviewApiController;

// Variant Configuration API Routes (for product form)
Route::prefix('variant-configurations')->group(function () {
    Route::get('key/{keyId}/tree', [VariantsConfigurationController::class, 'getKeyTree']);
    Route::get('{id}', [VariantsConfigurationController::class, 'show']);
});

Route::apiResource('brands', BrandApiController::class);


Route::prefix('products')->group(function () {
    Route::get('/', [ProductApiController::class, 'index']); // Done
    Route::get('/featured', [ProductApiController::class, 'featured']); // Done
    Route::get('/best-selling', [ProductApiController::class, 'bestSelling']); // Done
    Route::get('/latest', [ProductApiController::class, 'latest']); // Done
    Route::get('/special-offers', [ProductApiController::class, 'specialOffers']); // Done
    Route::get('/{departmentId}/department', [ProductApiController::class, 'getByDepartment']); // Done
    Route::get('/top', [ProductApiController::class, 'top']); // Done
    Route::get('specific-product/{id}/{vendorId}', [ProductApiController::class, 'show']); // Done
    Route::get('/hot-deals', [ProductApiController::class, 'hotDeals']); // Done


    // Filters
    Route::get('/filters', [ProductApiController::class, 'filters']);

    // Variants
    Route::get('/variants', [ProductApiController::class, 'variants']);
    // TODO: Uncomment when Occasion model is created
    // Route::get('/filters/occasion/{id}', [ProductApiController::class, 'filtersByOccasion']);
    // TODO: Uncomment when BundleCategory model is created
    // Route::get('/filters/bundle-category/{id}', [ProductApiController::class, 'filtersByBundleCategory']);


});

Route::get('{reviewableType}/{reviewableId}/reviews', [ReviewApiController::class, 'getByReviewable']);

// Review Routes (authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/{reviewableType}/{reviewableId}/reviews', [ReviewApiController::class, 'store']);
    Route::get('/reviews/my-reviews', [ReviewApiController::class, 'getCustomerReviews']);
});

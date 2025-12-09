<?php

use Illuminate\Support\Facades\Route;
use Modules\CatalogManagement\app\Http\Controllers\Api\BrandApiController;
use Modules\CatalogManagement\app\Http\Controllers\Api\BundleCategoryApiController;
use Modules\CatalogManagement\app\Http\Controllers\Api\BundlesApiController;
use Modules\CatalogManagement\app\Http\Controllers\VariantsConfigurationController;
use Modules\CatalogManagement\app\Http\Controllers\Api\ProductApiController;
use Modules\CatalogManagement\app\Http\Controllers\Api\ReviewApiController;
use Modules\CatalogManagement\app\Http\Controllers\Api\OccasionApiController;

// Variant Configuration API Routes (for product form)
Route::prefix('variant-configurations')->group(function () {
    Route::get('key/{keyId}/tree', [VariantsConfigurationController::class, 'getKeyTree']);
    Route::get('{id}', [VariantsConfigurationController::class, 'show']);
});

Route::apiResource('brands', BrandApiController::class);


// Product Routes - Optional Authentication (handles both guests and authenticated users)
// Higher throttle limit (300/min) for public product endpoints
Route::prefix('products')->middleware(['auth.optional:sanctum', 'throttle:products'])->group(function () {
    Route::get('/', [ProductApiController::class, 'index']);
    Route::get('/featured', [ProductApiController::class, 'featured']);
    Route::get('/best-selling', [ProductApiController::class, 'bestSelling']);
    Route::get('/latest', [ProductApiController::class, 'latest']);
    Route::get('/special-offers', [ProductApiController::class, 'specialOffers']);
    Route::get('/variants-all', [ProductApiController::class, 'variantsAll']);
    Route::get('/{departmentId}/department', [ProductApiController::class, 'getByDepartment']);
    Route::get('/top', [ProductApiController::class, 'top']);
    Route::get('specific-product/{id}/{vendorId}', [ProductApiController::class, 'show']);
    Route::get('/hot-deals', [ProductApiController::class, 'hotDeals']);
    Route::get('/filters', [ProductApiController::class, 'filters']);
    Route::get('/variants', [ProductApiController::class, 'variants']);
});

Route::get('{reviewableType}/{reviewableId}/reviews', [ReviewApiController::class, 'getByReviewable']);

// Review Routes (authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/{reviewableType}/{reviewableId}/reviews', [ReviewApiController::class, 'store']);
    Route::get('/reviews/my-reviews', [ReviewApiController::class, 'getCustomerReviews']);
});

// Occasions API Routes (public and authenticated)
Route::prefix('occasions')->group(function () {
    Route::get('/', [OccasionApiController::class, 'index']);
    Route::get('/{id}', [OccasionApiController::class, 'show']);
});

// Bundle Categories API Routes (public and authenticated)
Route::prefix('bundle-categories')->group(function () {
    Route::get('/', [BundleCategoryApiController::class, 'index']);
    Route::get('/{id}', [BundleCategoryApiController::class, 'show']);
});

// Bundles API Routes (public and authenticated)
Route::prefix('bundles')->group(function () {
    Route::get('/', [BundlesApiController::class, 'index']);
    Route::get('/{id}', [BundlesApiController::class, 'show']);
});


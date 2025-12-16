<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemSetting\app\Http\Controllers\Api\AdApiController;
use Modules\SystemSetting\app\Http\Controllers\Api\FaqApiController;
use Modules\SystemSetting\app\Http\Controllers\Api\FeatureApiController;
use Modules\SystemSetting\app\Http\Controllers\Api\FooterContentApiController;
use Modules\SystemSetting\app\Http\Controllers\SystemSettingController;
use Modules\SystemSetting\app\Http\Controllers\Api\MessageApiController;
use Modules\SystemSetting\app\Http\Controllers\Api\SiteInformationController;
use Modules\SystemSetting\app\Http\Controllers\Api\SliderApiController;
use Modules\SystemSetting\app\Http\Controllers\Api\BlogCategoryApiController;
use Modules\SystemSetting\app\Http\Controllers\Api\BlogApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('systemsettings', SystemSettingController::class)->names('systemsetting');

});

Route::resource('ads', AdApiController::class);
Route::resource('features', FeatureApiController::class);
Route::resource('footer-content', FooterContentApiController::class);
Route::resource('faqs', FaqApiController::class);
Route::resource('sliders', SliderApiController::class);
Route::resource('site-information', SiteInformationController::class);


// Messages API
Route::post('messages/send', [MessageApiController::class, 'sendMessage']);

// Blog APIs
Route::resource('blog-categories', BlogCategoryApiController::class);
Route::resource('blogs', BlogApiController::class);

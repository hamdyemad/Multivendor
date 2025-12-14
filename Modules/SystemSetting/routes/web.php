<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemSetting\app\Http\Controllers\CurrencyController;
use Modules\SystemSetting\app\Http\Controllers\ActivityLogController;
use Modules\SystemSetting\app\Http\Controllers\MessageController;
use Modules\SystemSetting\app\Http\Controllers\PointsSettingController;
use Modules\SystemSetting\app\Http\Controllers\UserPointsController;
use Modules\SystemSetting\app\Http\Controllers\AdController;
use Modules\SystemSetting\app\Http\Controllers\FeatureController;
use Modules\SystemSetting\app\Http\Controllers\FooterContentController;
use Modules\SystemSetting\app\Http\Controllers\FaqController;
use Modules\SystemSetting\app\Http\Controllers\SliderController;
use Modules\SystemSetting\app\Http\Controllers\SiteInformationController;
use Modules\SystemSetting\app\Http\Controllers\ReturnPolicyController;
use Modules\SystemSetting\app\Http\Controllers\ServiceTermsController;
use Modules\SystemSetting\app\Http\Controllers\PrivacyPolicyController;
use Modules\SystemSetting\app\Http\Controllers\TermsConditionsController;

Route::group(['prefix' => 'system-settings', 'as' => 'system-settings.'], function() {
    // Currencies
    Route::get('currencies/datatable', [CurrencyController::class, 'datatable'])->name('currencies.datatable');
    Route::resource('currencies', CurrencyController::class);

    // Ads
    Route::get('ads/datatable', [AdController::class, 'datatable'])->name('ads.datatable');
    Route::resource('ads', AdController::class);

    // Features (Frontend Settings)
    Route::get('features', [FeatureController::class, 'index'])->name('features.index');
    Route::post('features', [FeatureController::class, 'store'])->name('features.store');

    // Footer Content (Frontend Settings)
    Route::get('footer-content', [FooterContentController::class, 'index'])->name('footer-content.index');
    Route::post('footer-content', [FooterContentController::class, 'store'])->name('footer-content.store');

    // FAQs (Frontend Settings)
    Route::get('faqs/datatable', [FaqController::class, 'datatable'])->name('faqs.datatable');
    Route::resource('faqs', FaqController::class);

    // Sliders (Frontend Settings)
    Route::get('sliders/datatable', [SliderController::class, 'datatable'])->name('sliders.datatable');
    Route::resource('sliders', SliderController::class);

    // Site Information (Frontend Settings)
    Route::get('site-information', [SiteInformationController::class, 'index'])->name('site-information.index');
    Route::put('site-information', [SiteInformationController::class, 'update'])->name('site-information.update');

    // Return Policy (Frontend Settings)
    Route::get('return-policy', [ReturnPolicyController::class, 'index'])->name('return-policy.index');
    Route::put('return-policy', [ReturnPolicyController::class, 'update'])->name('return-policy.update');

    // Service Terms (Frontend Settings)
    Route::get('service-terms', [ServiceTermsController::class, 'index'])->name('service-terms.index');
    Route::put('service-terms', [ServiceTermsController::class, 'update'])->name('service-terms.update');

    // Privacy Policy (Frontend Settings)
    Route::get('privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy.index');
    Route::put('privacy-policy', [PrivacyPolicyController::class, 'update'])->name('privacy-policy.update');

    // Terms & Conditions (Frontend Settings)
    Route::get('terms-conditions', [TermsConditionsController::class, 'index'])->name('terms-conditions.index');
    Route::put('terms-conditions', [TermsConditionsController::class, 'update'])->name('terms-conditions.update');

    // Activity Logs
    Route::get('activity-logs/datatable', [ActivityLogController::class, 'datatable'])->name('activity-logs.datatable');
    Route::get('activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

// Points Settings
Route::group(['prefix' => 'points-settings', 'as' => 'points-settings.'], function() {
    Route::get('', [PointsSettingController::class, 'index'])->name('index');
    Route::post('', [PointsSettingController::class, 'store'])->name('store');
    Route::put('{id}', [PointsSettingController::class, 'update'])->name('update');
    Route::post('points-system/toggle-enabled', [PointsSettingController::class, 'togglePointsSystemEnabled'])->name('points-system.toggle-enabled');

    // User Points - Admin routes
    Route::group(['prefix' => 'user-points', 'as' => 'user-points.'], function() {
        Route::get('', [UserPointsController::class, 'index'])->name('index');
        Route::get('datatable', [UserPointsController::class, 'datatable'])->name('datatable');
        Route::get('{id}', [UserPointsController::class, 'show'])->name('show');
        Route::get('{userId}/transactions', [UserPointsController::class, 'transactionsView'])->name('transactions');
        Route::get('{userId}/transactions/datatable', [UserPointsController::class, 'transactions'])->name('transactions.datatable');
        Route::post('{userId}/adjust', [UserPointsController::class, 'adjustPoints'])->name('adjust');
    });
});


// Messages
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function() {
    Route::get('messages/datatable', [MessageController::class, 'datatable'])->name('messages.datatable');
    Route::delete('messages/{id}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('messages/{id}/archive', [MessageController::class, 'archive'])->name('messages.archive');
    Route::get('messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
});

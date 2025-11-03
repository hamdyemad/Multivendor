<?php

use Illuminate\Support\Facades\Route;

Route::group(
[
	'prefix' => 'admin/category-management',
    'as' => 'admin.category-management.'
], function(){
    // Activities
    Route::get('activities/datatable', 'ActivityController@datatable')->name('activities.datatable');
    Route::get('activities/search', 'ActivityController@activitySearch')->name('activities.search');
    Route::resource('activities', 'ActivityController');
    
    // Departments
    Route::get('departments/datatable', 'DepartmentController@datatable')->name('departments.datatable');
    Route::get('departments/search-activities', 'DepartmentController@searchActivities')->name('departments.search-activities');
    Route::resource('departments', 'DepartmentController');
    
    // Categories
    Route::get('categories/datatable', 'CategoryController@datatable')->name('categories.datatable');
    Route::get('categories/search-activities', 'CategoryController@searchActivities')->name('categories.search-activities');
    Route::resource('categories', 'CategoryController');
    
    // Sub Categories
    Route::get('subcategories/datatable', 'SubCategoryController@datatable')->name('subcategories.datatable');
    Route::resource('subcategories', 'SubCategoryController');
});
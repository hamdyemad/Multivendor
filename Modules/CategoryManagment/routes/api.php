<?php

use Illuminate\Support\Facades\Route;
use Modules\CategoryManagment\app\Http\Api\Controllers\CategoryController;
use Modules\CategoryManagment\app\Http\Api\Controllers\SubCategoryController;
use Modules\CategoryManagment\app\Http\Controllers\Api\ActivityApiController;
use Modules\CategoryManagment\app\Http\Controllers\Api\CategoryApiController;
use Modules\CategoryManagment\app\Http\Controllers\Api\DepartmentApiController;
use Modules\CategoryManagment\app\Http\Controllers\Api\SubCategoryApiController;

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/sub-categories', [SubCategoryController::class, 'index']);


Route::apiResource('activities', ActivityApiController::class);
Route::apiResource('departments', DepartmentApiController::class);
Route::apiResource('categories', CategoryApiController::class);
Route::apiResource('subcategories', SubCategoryApiController::class);
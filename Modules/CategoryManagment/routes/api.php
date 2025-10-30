<?php

use Illuminate\Support\Facades\Route;
use Modules\CategoryManagment\app\Http\Api\Controllers\CategoryController;
use Modules\CategoryManagment\app\Http\Api\Controllers\SubCategoryController;



Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/sub-categories', [SubCategoryController::class, 'index']);

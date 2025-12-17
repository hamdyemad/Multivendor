<?php

namespace Modules\CategoryManagment\app\Http\Api\Controllers;

use App\Http\Controllers\Controller;
use Modules\CategoryManagment\app\Http\Requests\CategoryRequest;
use Modules\CategoryManagment\app\Services\CategoryService;
use Modules\CategoryManagment\app\Services\DepartmentService;
use Modules\CategoryManagment\app\Http\Resources\ActivityResource;
use App\Services\LanguageService;
use App\Traits\Res;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CategoryManagment\app\Http\Resources\DepartmentResource;
use Modules\CategoryManagment\app\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $departmentService;
    protected $languageService;
    use Res;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        try {
            // If requesting for dropdown (no pagination needed)
            $filters = $request->all();
            // Only show active categories in dropdown
            $filters['active'] = 1;

            $categories = $this->categoryService->getAllCategories($filters, 0);
            if($request->select2) {
                // Simple data structure for dropdown
                $data = $categories->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->getTranslation('name', app()->getLocale()) ?? 'No Name'
                    ];
                });
            } else {
                $data = CategoryResource::collection($categories);
            }
            return $this->sendRes(__('validation.success'), true, $data, [], 200);

        } catch (\Exception $e) {
            Log::error('CategoryController@index error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return $this->sendRes('Error fetching categories: ' . $e->getMessage(), false, [], [], 500);
        }
    }

}

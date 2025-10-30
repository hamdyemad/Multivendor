<?php

namespace Modules\CategoryManagment\app\Http\Api\Controllers;

use App\Http\Controllers\Controller;
use Modules\CategoryManagment\app\Http\Requests\CategoryRequest;
use Modules\CategoryManagment\app\Services\CategoryService;
use Modules\CategoryManagment\app\Services\DepartmentService;
use Modules\CategoryManagment\app\Services\ActivityService;
use Modules\CategoryManagment\app\Http\Resources\ActivityResource;
use App\Services\LanguageService;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\Http\Resources\DepartmentResource;
use Modules\CategoryManagment\app\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $departmentService;
    protected $activityService;
    protected $languageService;
    use Res;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        // If requesting for dropdown (no pagination needed)
        if ($request->has('department_id') && !$request->has('paginate')) {
            $filters = $request->all();
            // Only show active categories in dropdown
            $filters['active'] = 1;
            
            $categories = $this->categoryService->getAllCategories($filters, 0);
            $data = CategoryResource::collection($categories)->map(function($category) {
                return [
                    'id' => $category['id'],
                    'name' => $category['name']
                ];
            });
            return $this->sendRes(__('validation.success'), true, $data, [], 200);
        }
        
        // Regular paginated list
        $perPage = $request->input('per_page', 10);
        $categories = $this->categoryService->getAllCategories($request->all(), $perPage);
        return $this->sendRes(__('validation.success'), true, CategoryResource::collection($categories), [], 200);
    }

}

<?php

namespace Modules\CategoryManagment\app\Http\Api\Controllers;

use App\Http\Controllers\Controller;
use Modules\CategoryManagment\app\Services\SubCategoryService;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\Http\Resources\SubCategoryResource;

class SubCategoryController extends Controller
{
    use Res;

    public function __construct(protected SubCategoryService $subCategoryService)
    {
    }

    public function index(Request $request)
    {
        // If requesting for dropdown (no pagination needed)
        if ($request->has('category_id') && !$request->has('paginate')) {
            $filters = $request->all();
            // Only show active subcategories in dropdown
            $filters['active'] = 1;
            
            $subCategories = $this->subCategoryService->getAllSubCategories($filters, 0);
            $data = SubCategoryResource::collection($subCategories)->map(function($subCategory) {
                return [
                    'id' => $subCategory['id'],
                    'name' => $subCategory['name']
                ];
            });
            return $this->sendRes(__('validation.success'), true, $data, [], 200);
        }
        
        // Regular paginated list
        $perPage = $request->input('per_page', 10);
        $subCategories = $this->subCategoryService->getAllSubCategories($request->all(), $perPage);
        return $this->sendRes(__('validation.success'), true, SubCategoryResource::collection($subCategories), [], 200);
    }

}

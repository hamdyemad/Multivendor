<?php

namespace Modules\CategoryManagment\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\Http\Resources\Api\SubCategoryApiResource;
use Modules\CategoryManagment\app\Services\Api\SubCategoryApiService;

class SubCategoryApiController extends Controller
{
    use Res;
    public function __construct(protected SubCategoryApiService $SubCategoryService)
    {}

    public function index(Request $request)
    {
        $subcategories = $this->SubCategoryService->getAllSubCategories($request->all());

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, SubCategoryApiResource::collection($subcategories));
    }

    public function show(Request $request, $id)
    {
        $SubCategory = $this->SubCategoryService->find($request->all(), $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, SubCategoryApiResource::make($SubCategory));
    }
}

<?php

namespace Modules\CategoryManagment\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\Http\Resources\Api\CategoryApiResource;
use Modules\CategoryManagment\app\Services\Api\CategoryApiService;

class CategoryApiController extends Controller
{
    use Res;
    public function __construct(protected CategoryApiService $CategoryService)
    {}

    public function index(Request $request)
    {
        $categories = $this->CategoryService->getAllCategories($request->all());

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, CategoryApiResource::collection($categories));
    }

    public function show(Request $request, $id)
    {
        $Category = $this->CategoryService->find($request->all(), $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, CategoryApiResource::make($Category));
    }
}

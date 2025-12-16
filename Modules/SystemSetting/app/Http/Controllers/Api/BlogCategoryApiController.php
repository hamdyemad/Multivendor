<?php

namespace Modules\SystemSetting\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\SystemSetting\app\Services\Api\BlogCategoryApiService;
use Modules\SystemSetting\app\Http\Resources\Api\BlogCategoryResource;
use App\Traits\Res;
use Illuminate\Http\Request;

class BlogCategoryApiController extends Controller
{
    use Res;

    public function __construct(protected BlogCategoryApiService $blogCategoryService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = $this->blogCategoryService->getAll($request->all());
        return $this->sendRes(__('main.success'), true, BlogCategoryResource::collection($categories));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = $this->blogCategoryService->find($id);
        return $this->sendRes(__('main.success'), true, new BlogCategoryResource($category));
    }
}

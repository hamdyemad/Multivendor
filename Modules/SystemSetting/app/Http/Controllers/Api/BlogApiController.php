<?php

namespace Modules\SystemSetting\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\SystemSetting\app\Services\Api\BlogApiService;
use Modules\SystemSetting\app\Http\Resources\Api\BlogResource;
use Illuminate\Http\Request;
use App\Traits\Res;

class BlogApiController extends Controller
{
    use Res;

    public function __construct(protected BlogApiService $blogService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $blogs = $this->blogService->getAll($request->all());
        return $this->sendRes(__('main.success'), true, BlogResource::collection($blogs));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $blog = $this->blogService->find($id);
        return $this->sendRes(__('main.success'), true, new BlogResource($blog));
    }
}

<?php

namespace Modules\CatalogManagement\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\Http\Resources\Api\BundleCategoryResource;
use Modules\CatalogManagement\app\Http\Resources\BundleResource;
use Modules\CatalogManagement\app\Services\BundleCategoryService;
use Modules\CatalogManagement\app\Services\BundleService;

class BundleCategoryApiController extends Controller
{
    use Res;

    public function __construct(protected BundleCategoryService $bundleCategoryService)
    {
    }

    /**
     * Get all active bundles
     * GET /api/bundles
     */
    public function index(Request $request)
    {
        $per_page = $request->per_page ?? 10;
        $categories = $this->bundleCategoryService->getAll($request->all(), $per_page);
        return $this->sendRes(config('responses.success')[app()->getLocale()], true, BundleCategoryResource::collection($categories));
    }

    /**
     * Get single occasion by ID
     * GET /api/bundles/{id}
     */
    public function show(Request $request, $id)
    {
        $bundle = $this->bundleCategoryService->getBundleCategoryById($id);
        return $this->sendRes(config('responses.success')[app()->getLocale()], true, new BundleCategoryResource($bundle));

    }


}

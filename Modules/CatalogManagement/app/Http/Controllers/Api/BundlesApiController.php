<?php

namespace Modules\CatalogManagement\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\Http\Resources\Api\BundleResource;
use Modules\CatalogManagement\app\Repositories\Api\BundleApiRepository;
use Modules\CatalogManagement\app\Services\Api\BundleApiService;

class BundlesApiController extends Controller
{
    use Res;

    public function __construct(protected BundleApiService $bundleService)
    {
    }

    /**
     * Get all active bundles
     * GET /api/bundles
     */
    public function index(Request $request)
    {
        $per_page = $request->get('per_page', 10);

        $bundles = $this->bundleService->getAllBundles($request->all(), $per_page);
        return $this->sendRes(config('responses.success')[app()->getLocale()], true, BundleResource::collection($bundles));
    }

    /**
     * Get single occasion by ID
     * GET /api/bundles/{id}
     */
    public function show(Request $request, $id)
    {
        $filters = [
            'search' => $request->get('search'),
        ];
        
        $bundle = $this->bundleService->getBundleById($id, $filters);
        
        $resource = new BundleResource($bundle);
        $resource->includeProducts = true;
        
        return $this->sendRes(config('responses.success')[app()->getLocale()], true, $resource);
    }


}

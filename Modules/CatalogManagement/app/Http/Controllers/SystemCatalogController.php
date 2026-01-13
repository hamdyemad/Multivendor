<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\Services\SystemCatalogService;

class SystemCatalogController extends Controller
{
    public function __construct(
        protected SystemCatalogService $systemCatalogService
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('catalogmanagement::system-catalog.index');
    }

    /**
     * Get paginated departments data
     */
    public function departments(Request $request)
    {
        $data = $this->systemCatalogService->getDepartments($request->all());
        return response()->json($data);
    }

    /**
     * Get paginated categories data
     */
    public function categories(Request $request)
    {
        $data = $this->systemCatalogService->getCategories($request->all());
        return response()->json($data);
    }

    /**
     * Get paginated variants data
     */
    public function variants(Request $request)
    {
        $data = $this->systemCatalogService->getVariants($request->all());
        return response()->json($data);
    }

    /**
     * Get paginated brands data
     */
    public function brands(Request $request)
    {
        $data = $this->systemCatalogService->getBrands($request->all());
        return response()->json($data);
    }

    /**
     * Get paginated regions data
     */
    public function regions(Request $request)
    {
        $data = $this->systemCatalogService->getRegions($request->all());
        return response()->json($data);
    }

    /**
     * Get paginated vendors data (admin only)
     */
    public function vendors(Request $request)
    {
        if (!isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $data = $this->systemCatalogService->getVendors($request->all());
        return response()->json($data);
    }
}

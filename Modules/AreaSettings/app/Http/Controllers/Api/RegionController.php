<?php

namespace Modules\AreaSettings\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Modules\AreaSettings\app\Services\RegionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\AreaSettings\app\Resources\RegionResource;

class RegionController extends Controller
{
    use Res;
    public function __construct(
        protected RegionService $regionService
    ) {
        // Add any middleware if needed
        // $this->middleware('auth:api')->except(['index']);
    }

    /**
     * Get all regions for API requests
     * Used in product forms, dropdowns, etc.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $perPage = isset($request->per_page) ? $request->per_page : 10;
            $regions = $this->regionService->getAllRegions($filters, $perPage);
            $data = [];
            // Check if data is paginated
            $isPaginated = method_exists($regions, 'lastPage');
            // Full data structure for regular API calls
            if ($isPaginated) {
                $paginationMeta = $this->getPaginationMeta($regions);
                $data['items'] = RegionResource::collection($regions->getCollection());
                $data['pagination'] = $paginationMeta;
            } else {
                $data['items'] = RegionResource::collection($regions);
            }
            if($request->select2) {
                // Simple data structure for dropdown
                $items = $regions->getCollection()->map(function($region) {
                    return [
                        'id' => $region->id,
                        'name' => $region->getTranslation('name', app()->getLocale()) ?? 'No Name'
                    ];
                });
                $data['items'] = $items;
            }
            return $this->sendRes('Regions retrieved successfully', true, $data, [], 200);
        } catch (\Exception $e) {
            return $this->sendRes('Failed to retrieve regions', false, [], [$e->getMessage()], 500);
        }
    }

    /**
     * Get active regions only
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function active(Request $request): JsonResponse
    {
        try {
            $regions = $this->regionService->getActiveRegions();

            return response()->json([
                'success' => true,
                'message' => 'Active regions retrieved successfully',
                'data' => $regions->map(function ($region) {
                    return [
                        'id' => $region->id,
                        'name' => $region->name,
                        'city_id' => $region->city_id ?? null,
                        'city_name' => $region->city->name ?? null
                    ];
                })
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active regions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get regions by city ID
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function byCity(Request $request): JsonResponse
    {
        $cityId = $request->get('city_id');

        if (!$cityId) {
            return response()->json([
                'success' => false,
                'message' => 'City ID is required'
            ], 400);
        }

        try {
            $regions = $this->regionService->getRegionsByCity($cityId);

            return response()->json([
                'success' => true,
                'message' => 'Regions retrieved successfully',
                'data' => $regions->map(function ($region) {
                    return [
                        'id' => $region->id,
                        'name' => $region->name,
                        'city_id' => $region->city_id,
                        'active' => $region->active ?? true
                    ];
                })
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve regions for city',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

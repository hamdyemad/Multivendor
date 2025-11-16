<?php

namespace Modules\AreaSettings\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Resources\RegionResource;
use Modules\AreaSettings\app\Services\Api\RegionApiService;

class RegionApiController extends Controller
{
    use Res;
    public function __construct(
        protected RegionApiService $RegionService,
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $regions = $this->RegionService->getAll($request->all());

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, RegionResource::collection($regions)->additional($request->all()));
    }

    public function getRegionsByCity(Request $request, $id)
    {
        $regions = $this->RegionService->getRegionsByCity($request->all(), $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, RegionResource::collection($regions));
    }

}

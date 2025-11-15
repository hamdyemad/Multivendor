<?php

namespace Modules\CategoryManagment\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\Http\Resources\Api\ActivityApiResource;
use Modules\CategoryManagment\app\Services\Api\ActivityApiService;

class ActivityApiController extends Controller
{
    use Res;
    public function __construct(protected ActivityApiService $activityService)
    {}

    public function index(Request $request)
    {
        $activities = $this->activityService->getAllActivities($request->all());

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, ActivityApiResource::collection($activities));
    }

    public function show(Request $request, $id)
    {
        $activity = $this->activityService->find($request->all(), $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, ActivityApiResource::make($activity));
    }
}

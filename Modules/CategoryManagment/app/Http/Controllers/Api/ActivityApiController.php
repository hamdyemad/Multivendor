<?php

namespace Modules\CategoryManagment\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\DTOs\ActivityFilterDTO;
use Modules\CategoryManagment\app\Http\Resources\Api\ActivityApiResource;
use Modules\CategoryManagment\app\Services\Api\ActivityApiService;

class ActivityApiController extends Controller
{
    use Res;
    public function __construct(protected ActivityApiService $activityService)
    {}

    public function index(Request $request)
    {
        $dto = ActivityFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $activities = $this->activityService->getAllActivities($dto);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, ActivityApiResource::collection($activities));
    }

    public function show(Request $request, $id)
    {
        $dto = ActivityFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $activity = $this->activityService->find($dto, $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, ActivityApiResource::make($activity));
    }
}

<?php

namespace Modules\CategoryManagment\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\DTOs\CategoryFilterDTO;
use Modules\CategoryManagment\app\Http\Resources\Api\CategoryApiResource;
use Modules\CategoryManagment\app\Services\Api\CategoryApiService;

class CategoryApiController extends Controller
{
    use Res;
    public function __construct(protected CategoryApiService $CategoryService)
    {}

    public function index(Request $request)
    {
        $dto = CategoryFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $categories = $this->CategoryService->getAllCategories($dto);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, CategoryApiResource::collection($categories));
    }

    public function show(Request $request, $id)
    {
        $dto = CategoryFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $Category = $this->CategoryService->find($dto, $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, CategoryApiResource::make($Category));
    }
}

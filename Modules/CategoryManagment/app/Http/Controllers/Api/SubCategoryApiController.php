<?php

namespace Modules\CategoryManagment\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\DTOs\CategoryFilterDTO;
use Modules\CategoryManagment\app\Http\Resources\Api\SubCategoryApiResource;
use Modules\CategoryManagment\app\Services\Api\SubCategoryApiService;

class SubCategoryApiController extends Controller
{
    use Res;
    public function __construct(protected SubCategoryApiService $SubCategoryService)
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

        $subcategories = $this->SubCategoryService->getAllSubCategories($dto);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, SubCategoryApiResource::collection($subcategories));
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

        $SubCategory = $this->SubCategoryService->find($dto, $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, SubCategoryApiResource::make($SubCategory));
    }
}

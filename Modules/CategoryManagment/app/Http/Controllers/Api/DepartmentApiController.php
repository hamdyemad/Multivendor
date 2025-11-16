<?php

namespace Modules\CategoryManagment\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\Http\Resources\Api\DepartmentApiResource;
use Modules\CategoryManagment\app\Services\Api\DepartmentApiService;

class DepartmentApiController extends Controller
{
    use Res;
    public function __construct(protected DepartmentApiService $DepartmentService)
    {}

    public function index(Request $request)
    {
        $departments = $this->DepartmentService->getAllDepartments($request->all());
        $departments =  DepartmentApiResource::collection($departments)->additional($request->all());
        return $this->sendRes(config('responses.success')[app()->getLocale()], true, $departments);
    }

    public function show(Request $request, $id)
    {
        $department = $this->DepartmentService->find($request->all(), $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, DepartmentApiResource::make($department));
    }
}

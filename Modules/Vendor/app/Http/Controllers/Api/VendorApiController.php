<?php

namespace Modules\Vendor\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\Vendor\app\Http\Resources\Api\VendorApiResource;
use Modules\Vendor\app\Http\Resources\Api\VendorRequestResource;
use Modules\Vendor\app\Http\Requests\CreateVendorRequestRequest;
use Modules\Vendor\app\Services\Api\VendorApiService;

class VendorApiController extends Controller
{
    use Res;

    public function __construct(
        protected VendorApiService $VendorService
    )
    {}

    public function index(Request $request)
    {
        $vendors = $this->VendorService->getAllVendors($request->all());

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, VendorApiResource::collection($vendors));
    }

    public function show(Request $request, $id)
    {
        $vendor = $this->VendorService->find($request->all(), $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, VendorApiResource::make($vendor));
    }

    /**
     * Submit a vendor request
     *
     * @param CreateVendorRequestRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function vendorRequest(CreateVendorRequestRequest $request)
    {
        $vendorRequest = $this->VendorService->createVendorRequest($request->validated());

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            new VendorRequestResource($vendorRequest),
            [],
            201
        );
    }
}

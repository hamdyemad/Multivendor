<?php

namespace Modules\Vendor\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\Vendor\app\Http\Resources\Api\VendorApiResource;
use Modules\Vendor\app\Services\Api\VendorApiService;

class VendorApiController extends Controller
{
    use Res;
    
    public function __construct(protected VendorApiService $VendorService)
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
}

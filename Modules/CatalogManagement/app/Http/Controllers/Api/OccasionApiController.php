<?php

namespace Modules\CatalogManagement\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\Http\Resources\OccasionResource;
use Modules\CatalogManagement\app\Services\OccasionService;

class OccasionApiController extends Controller
{
    use Res;

    public function __construct(protected OccasionService $occasionService)
    {
    }

    /**
     * Get all active occasions
     * GET /api/occasions
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'active' => $request->get('active', true),
            'vendor_id' => $request->get('vendor_id'),
        ];
        $per_page = $request->get('per_page');

        $occasions = $this->occasionService->getAllOccasions($filters, $per_page);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, OccasionResource::collection($occasions));
    }

    /**
     * Get single occasion by ID
     * GET /api/occasions/{id}
     */
    public function show(Request $request, $id)
    {
        $occasion = $this->occasionService->getOccasionById($id);
        return $this->sendRes(config('responses.success')[app()->getLocale()], true, new OccasionResource($occasion));

    }


}

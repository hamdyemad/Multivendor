<?php

namespace Modules\Refund\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\Refund\app\Services\RefundRequestService;
use Modules\Refund\app\Http\Requests\Api\StoreRefundRequestRequest;
use Modules\Refund\app\Http\Requests\Api\UpdateRefundStatusRequest;
use Modules\Refund\app\Http\Resources\RefundRequestResource;
use Modules\Refund\app\Http\Resources\RefundRequestCollection;

class RefundRequestApiController extends Controller
{
    use Res;
    protected $refundService;

    public function __construct(RefundRequestService $refundService)
    {
        $this->refundService = $refundService;
    }

    /**
     * Display a listing of refund requests
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'status' => $request->get('status'),
                'customer_id' => $request->get('customer_id'),
                'vendor_id' => $request->get('vendor_id'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'search' => $request->get('search'),
                'show_parent' => $request->get('show_parent'), // For customer view
            ];

            $perPage = $request->get('per_page', 15);
            $refunds = $this->refundService->getAllRefunds($filters, $perPage);

            return new RefundRequestCollection($refunds);
        } catch (\Exception $e) {
            return $this->sendRes($e->getMessage(), false, [], [], 500);
        }
    }

    /**
     * Display the specified refund request
     */
    public function show($id)
    {
        try {
            $refund = $this->refundService->getRefundById($id);

            // Check authorization
            if (!$this->refundService->canUserAccessRefund($id, auth()->user())) {
                return $this->sendRes('Unauthorized', false, [], [], 403);
            }

            return $this->sendRes(
                'Refund request retrieved successfully',
                true,
                new RefundRequestResource($refund)
            );
        } catch (\Exception $e) {
            return $this->sendRes($e->getMessage(), false, [], [], 404);
        }
    }

    /**
     * Create a new refund request
     */
    public function store(StoreRefundRequestRequest $request)
    {
        try {
            $refund = $this->refundService->createRefund(
                $request->validated(),
                auth()->user()
            );

            return $this->sendRes(
                trans('refund::refund.messages.request_created'),
                true,
                new RefundRequestResource($refund),
                [],
                201
            );
        } catch (\Exception $e) {
            $code = $e->getMessage() === 'Unauthorized access to this order' ? 403 : 500;
            return $this->sendRes($e->getMessage(), false, [], [], $code);
        }
    }

    /**
     * Update refund request status
     */
    public function updateStatus(UpdateRefundStatusRequest $request, $id)
    {
        try {
            $refund = $this->refundService->updateRefundStatus(
                $id,
                $request->validated(),
                auth()->user()
            );

            return $this->sendRes(
                trans('refund::refund.messages.status_updated'),
                true,
                new RefundRequestResource($refund)
            );
        } catch (\Exception $e) {
            $code = $e->getMessage() === 'Unauthorized' ? 403 : 500;
            return $this->sendRes($e->getMessage(), false, [], [], $code);
        }
    }

    /**
     * Cancel refund request (customer only)
     */
    public function cancel($id)
    {
        try {
            $refund = $this->refundService->cancelRefund($id, auth()->user());

            return $this->sendRes(
                trans('refund::refund.messages.request_cancelled'),
                true,
                new RefundRequestResource($refund)
            );
        } catch (\Exception $e) {
            $code = $e->getMessage() === 'Cannot cancel refund request in current status' ? 400 : 500;
            return $this->sendRes($e->getMessage(), false, [], [], $code);
        }
    }

    /**
     * Get refund statistics
     */
    public function statistics(Request $request)
    {
        try {
            $filters = [
                'customer_id' => $request->get('customer_id'),
                'vendor_id' => $request->get('vendor_id'),
            ];

            $statistics = $this->refundService->getStatistics($filters);

            return $this->sendRes(
                'Statistics retrieved successfully',
                true,
                $statistics
            );
        } catch (\Exception $e) {
            return $this->sendRes($e->getMessage(), false, [], [], 500);
        }
    }
}

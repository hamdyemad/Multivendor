<?php

namespace Modules\Refund\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\Refund\app\Services\RefundRequestService;
use Modules\Refund\app\Http\Requests\Api\StoreRefundRequestRequest;
use Modules\Refund\app\Http\Resources\RefundRequestResource;

class RefundRequestApiController extends Controller
{
    use Res;
    protected $refundService;

    public function __construct(RefundRequestService $refundService)
    {
        $this->refundService = $refundService;
    }

    /**
     * Get localized message from config with fallback
     */
    private function getMessage(string $key): string
    {
        $locale = app()->getLocale();
        $messages = config('responses.' . $key);
        
        return $messages[$locale] ?? $messages['en'] ?? 'Success';
    }

    /**
     * Display a listing of refund requests
     */
    public function index(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'customer_id' => $request->get('customer_id'),
            'vendor_id' => $request->get('vendor_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'search' => $request->get('search'),
            'show_parent' => $request->get('show_parent'), // For customer view
        ];
        $perPage = $request->get('per_page', 12);
        $refunds = $this->refundService->getAllRefunds($filters, $perPage);
        
        return $this->sendRes(
            $this->getMessage('refund_requests_retrieved_successfully'),
            true,
            RefundRequestResource::collection($refunds)
        );
    }

    /**
     * Display the specified refund request
     */
    public function show($id)
    {
        $refund = $this->refundService->getRefundWithRelations($id, [
            'items.orderProduct.vendorProduct.product',
            'items.orderProduct.vendorProduct.vendor',
            'items.orderProduct.vendorProduct.variants.variantConfiguration.key',
            'items.orderProduct.vendorProduct.taxes',
            'items.orderProduct.vendorProductVariant',
            'history.user', 
            'order', 
            'customer', 
            'vendor'
        ]);

        // Check authorization
        if (!$this->refundService->canUserAccessRefund($id, auth()->user())) {
            return $this->sendRes('Unauthorized', false, [], [], 403);
        }
        
        return $this->sendRes(
            $this->getMessage('refund_request_retrieved_successfully'),
            true,
            new RefundRequestResource($refund)
        );
    }

    /**
     * Create a new refund request
     */
    public function store(StoreRefundRequestRequest $request)
    {
        $refunds = $this->refundService->createRefund(
            $request->validated(),
            auth()->user()
        );

        return $this->sendRes(
            $this->getMessage('refund_request_created_successfully'),
            true,
            [],
            [],
            201
        );
    }

    /**
     * Cancel refund request (customer only)
     */
    public function cancel($id)
    {
        $refund = $this->refundService->cancelRefund($id, auth()->user());
        
        return $this->sendRes(
            $this->getMessage('refund_request_cancelled_successfully'),
            true,
            new RefundRequestResource($refund)
        );
    }

    /**
     * Get refund statistics
     */
    public function statistics(Request $request)
    {
        $filters = [
            'customer_id' => $request->get('customer_id'),
            'vendor_id' => $request->get('vendor_id'),
        ];
        $statistics = $this->refundService->getStatistics($filters);
        
        return $this->sendRes(
            $this->getMessage('statistics_retrieved_successfully'),
            true,
            $statistics
        );
    }
}

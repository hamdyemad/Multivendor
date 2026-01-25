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
            'vendor_id' => $request->get('vendor_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'search' => $request->get('search'),
            'customer_id' => auth()->id(),
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
            'history.customer',
            'order', 
            'customer', 
            'vendor'
        ]);

        // Check authorization
        if (!$this->refundService->canUserAccessRefund($id, auth()->user())) {
            return $this->sendRes(
                trans('common.unauthorized'),
                false,
                [],
                [],
                403
            );
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
    public function cancel(Request $request, $id)
    {
        // Validate cancellation reason
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        // Check if user can cancel this refund
        $user = auth()->user();
        if (!$this->refundService->canUserAccessRefund($id, $user)) {
            return $this->sendRes(
                trans('common.unauthorized'),
                false,
                [],
                [],
                403
            );
        }

        // Check if user can cancel (only customer can cancel pending refunds)
        $refund = $this->refundService->getRefundById($id);
        if ($refund->customer_id !== $user->id) {
            return $this->sendRes(
                trans('refund::refund.messages.only_customer_can_cancel'),
                false,
                [],
                [],
                403
            );
        }

        if ($refund->status !== 'pending') {
            return $this->sendRes(
                trans('refund::refund.messages.cannot_cancel_refund'),
                false,
                [],
                [],
                400
            );
        }

        $refund = $this->refundService->cancelRefund($id, $request->input('cancellation_reason'));
        
        return $this->sendRes(
            trans('refund::refund.messages.cancelled_successfully'),
            true,
            []
        );
    }

    /**
     * Get refund statistics
     */
    public function statistics(Request $request)
    {
        $filters = [
            'customer_id' => auth()->id(),
        ];
        $statistics = $this->refundService->getStatistics($filters);
        
        return $this->sendRes(
            $this->getMessage('statistics_retrieved_successfully'),
            true,
            $statistics
        );
    }

    /**
     * Get all available refund statuses
     */
    public function statuses()
    {
        $statuses = [];
        
        foreach (\Modules\Refund\app\Models\RefundRequest::STATUSES as $key => $label) {
            $statuses[] = [
                'id' => $key,
                'value' => $key,
                'label' => trans('refund::refund.statuses.' . $key),
                'label_en' => trans('refund::refund.statuses.' . $key, [], 'en'),
                'label_ar' => trans('refund::refund.statuses.' . $key, [], 'ar'),
            ];
        }
        
        return $this->sendRes(
            $this->getMessage('statuses_retrieved_successfully'),
            true,
            $statuses
        );
    }
}

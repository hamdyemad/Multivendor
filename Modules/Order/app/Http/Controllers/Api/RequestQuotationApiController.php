<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Modules\Order\app\Services\Api\RequestQuotationApiService;
use Modules\Order\app\Http\Requests\Api\StoreRequestQuotationRequest;
use Modules\Order\app\Http\Requests\Api\RespondQuotationOfferRequest;
use Modules\Order\app\Http\Resources\RequestQuotationResource;

class RequestQuotationApiController extends Controller
{
    use Res;

    public function __construct(
        protected RequestQuotationApiService $service
    ) {}

    /**
     * Store a new quotation request
     */
    public function store(StoreRequestQuotationRequest $request)
    {
        $customerId = auth('sanctum')->check() ? auth('sanctum')->id() : null;

        $quotation = $this->service->createQuotation(
            $request->validated(),
            $request->file('file'),
            $customerId
        );

        return $this->sendRes(
            config('responses.quotation_created_successfully')[app()->getLocale()],
            true,
            new RequestQuotationResource($quotation),
            [],
            201
        );
    }

    /**
     * Get customer's quotations
     */
    public function index()
    {
        /** @var \Modules\Customer\app\Models\Customer $customer */
        $customer = auth('sanctum')->user();
        
        if (!$customer) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }
        
        $perPage = request()->input('per_page', 15);
        
        $filters = [
            'status' => request()->input('status'),
            'search' => request()->input('search'),
        ];
        
        $quotations = $this->service->getCustomerQuotations($customer, (int) $perPage, $filters);

        return $this->sendRes(
            config('responses.quotations_retrieved_successfully')[app()->getLocale()],
            true,
            RequestQuotationResource::collection($quotations)
        );
    }

    /**
     * Get single quotation details
     */
    public function show($id)
    {
        /** @var \Modules\Customer\app\Models\Customer $customer */
        $customer = auth('sanctum')->user();
        
        if (!$customer) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }
        
        $quotation = $this->service->getQuotationForCustomer((int) $id, $customer);

        if (!$quotation) {
            return $this->sendRes(
                config('responses.quotation_not_found')[app()->getLocale()],
                false,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            config('responses.quotation_retrieved_successfully')[app()->getLocale()],
            true,
            new RequestQuotationResource($quotation)
        );
    }

    /**
     * Respond to vendor offer (accept or reject)
     * This endpoint accepts/rejects a specific vendor's offer
     */
    public function respondToOffer(RespondQuotationOfferRequest $request, $id)
    {
        /** @var \Modules\Customer\app\Models\Customer $customer */
        $customer = auth('sanctum')->user();
        
        if (!$customer) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }
        
        $quotation = $this->service->getQuotationForCustomer((int) $id, $customer);

        if (!$quotation) {
            return $this->sendRes(
                config('responses.quotation_not_found')[app()->getLocale()],
                false,
                [],
                [],
                404
            );
        }

        // Validate vendor_id is provided
        if (!$request->has('vendor_id')) {
            return $this->sendRes(
                'Vendor ID is required',
                false,
                [],
                [],
                400
            );
        }

        $vendorId = (int) $request->input('vendor_id');
        $action = $request->action; // 'accept' or 'reject'

        // Use the multi-vendor accept/reject methods
        if ($action === 'accept') {
            $result = $this->service->acceptVendorOffer((int) $id, $vendorId, $customer);
        } else {
            $result = $this->service->rejectVendorOffer((int) $id, $vendorId, $customer);
        }

        if (!$result['success']) {
            return $this->sendRes(
                $result['message'],
                false,
                [],
                [],
                400
            );
        }

        $message = $action === 'accept' 
            ? __('order::request-quotation.offer_accepted_successfully')
            : __('order::request-quotation.offer_rejected_successfully');

        return $this->sendRes(
            $message,
            true,
            [
                'quotation_vendor' => new \Modules\Order\app\Http\Resources\RequestQuotationVendorResource($result['quotation_vendor']),
                'order' => isset($result['order']) && $result['order'] ? new \Modules\Order\app\Http\Resources\Api\OrderResource($result['order']) : null,
            ]
        );
    }

    /**
     * Get offers from vendors for a quotation
     */
    public function offers($id)
    {
        /** @var \Modules\Customer\app\Models\Customer $customer */
        $customer = auth('sanctum')->user();
        
        if (!$customer) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }
        
        $quotation = $this->service->getQuotationForCustomer((int) $id, $customer);

        if (!$quotation) {
            return $this->sendRes(
                config('responses.quotation_not_found')[app()->getLocale()],
                false,
                [],
                [],
                404
            );
        }

        // Load vendors with their orders
        $quotation->load(['vendors.vendor', 'vendors.order']);
        $vendors = $quotation->vendors;

        return $this->sendRes(
            'Offers retrieved successfully',
            true,
            \Modules\Order\app\Http\Resources\RequestQuotationVendorResource::collection($vendors)
        );
    }

    /**
     * Accept vendor offer
     */
    public function acceptOffer($quotationId, $vendorId)
    {
        /** @var \Modules\Customer\app\Models\Customer $customer */
        $customer = auth('sanctum')->user();
        
        if (!$customer) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }
        
        $result = $this->service->acceptVendorOffer((int) $quotationId, (int) $vendorId, $customer);

        if (!$result['success']) {
            return $this->sendRes(
                $result['message'],
                false,
                [],
                [],
                400
            );
        }

        return $this->sendRes(
            __('order::request-quotation.offer_accepted_successfully'),
            true,
            [
                'quotation_vendor' => new \Modules\Order\app\Http\Resources\RequestQuotationVendorResource($result['quotation_vendor']),
                'order' => $result['order'] ? new \Modules\Order\app\Http\Resources\Api\OrderResource($result['order']) : null,
            ]
        );
    }

    /**
     * Reject vendor offer
     */
    public function rejectOffer($quotationId, $vendorId)
    {
        /** @var \Modules\Customer\app\Models\Customer $customer */
        $customer = auth('sanctum')->user();
        
        if (!$customer) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }
        
        $result = $this->service->rejectVendorOffer((int) $quotationId, (int) $vendorId, $customer);

        if (!$result['success']) {
            return $this->sendRes(
                $result['message'],
                false,
                [],
                [],
                400
            );
        }

        return $this->sendRes(
            __('order::request-quotation.offer_rejected_successfully'),
            true,
            new \Modules\Order\app\Http\Resources\RequestQuotationVendorResource($result['quotation_vendor'])
        );
    }
}

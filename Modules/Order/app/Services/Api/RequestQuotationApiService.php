<?php

namespace Modules\Order\app\Services\Api;

use Modules\Order\app\Interfaces\Api\RequestQuotationApiRepositoryInterface;
use Modules\Order\app\Models\RequestQuotation;
use Modules\Customer\app\Models\Customer;

class RequestQuotationApiService
{
    public function __construct(
        protected RequestQuotationApiRepositoryInterface $repository
    ) {}

    public function getCustomerQuotations(Customer $customer, int $perPage = 15, array $filters = [])
    {
        return $this->repository->getCustomerQuotations($customer->id, $perPage, $filters);
    }

    public function getQuotationForCustomer(int $id, Customer $customer)
    {
        return $this->repository->findForCustomer($id, $customer->id);
    }

    public function createQuotation(array $data, $file = null, ?int $customerId = null)
    {
        if ($file) {
            $data['file'] = $file->store('request-quotations', 'public');
        }

        if ($customerId) {
            $data['customer_id'] = $customerId;
        }

        // Map address_id to customer_address_id
        if (isset($data['address_id'])) {
            $data['customer_address_id'] = $data['address_id'];
            unset($data['address_id']);
        }

        // Set default status
        $data['status'] = RequestQuotation::STATUS_PENDING;

        return $this->repository->create($data);
    }

    public function respondToOffer(RequestQuotation $quotation, Customer $customer, string $action): array
    {
        if (!$quotation->canRespondToOffer()) {
            return [
                'success' => false,
                'message_key' => 'cannot_respond_to_offer',
            ];
        }

        if ($action === 'accept') {
            // Notification is handled by NotificationObserver via HasNotifications trait
            $quotation = $this->repository->acceptOffer($quotation->id, $customer->id);

            return [
                'success' => true,
                'message_key' => 'offer_accepted_successfully',
                'quotation' => $quotation,
            ];
        } else {
            // Notification is handled by NotificationObserver via HasNotifications trait
            $quotation = $this->repository->rejectOffer($quotation->id, $customer->id);

            return [
                'success' => true,
                'message_key' => 'offer_rejected_successfully',
                'quotation' => $quotation,
            ];
        }
    }

    /**
     * Accept vendor offer (multi-vendor workflow)
     */
    public function acceptVendorOffer(int $quotationId, int $vendorId, Customer $customer): array
    {
        $quotation = $this->repository->findForCustomer($quotationId, $customer->id);

        if (!$quotation) {
            return [
                'success' => false,
                'message' => 'Quotation not found',
            ];
        }

        $quotationVendor = $quotation->vendors()
            ->where('vendor_id', $vendorId)
            ->first();

        if (!$quotationVendor) {
            return [
                'success' => false,
                'message' => 'Vendor offer not found',
            ];
        }

        if (!$quotationVendor->canRespondToOffer()) {
            return [
                'success' => false,
                'message' => 'Cannot respond to offer in current status',
            ];
        }

        try {
            \DB::beginTransaction();

            // Accept the offer
            $quotationVendor->acceptOffer();

            // Check if vendor already created an order (offer_sent status means order exists)
            if ($quotationVendor->order_id) {
                // Use the existing order created by vendor
                $order = \Modules\Order\app\Models\Order::find($quotationVendor->order_id);
            } else {
                // Fallback: Create order from the offer (for old workflow)
                $order = $this->createOrderFromOffer($quotationVendor, $customer);
            }

            // Mark as order created (customer accepted)
            $quotationVendor->markOrderCreated($order->id);

            // Send notification to vendor using AdminNotification
            $vendor = $quotationVendor->vendor;
            if ($vendor && $vendor->user) {
                \App\Models\AdminNotification::notify(
                    type: 'vendor_offer_accepted',
                    title: 'order::request-quotation.notification_vendor_offer_accepted_title',
                    description: 'order::request-quotation.notification_vendor_offer_accepted_message',
                    url: route('admin.orders.show', [
                        'lang' => app()->getLocale(),
                        'countryCode' => $quotationVendor->requestQuotation->country->code ?? 'eg',
                        'order' => $order->id,
                    ]),
                    icon: 'uil-check-circle',
                    color: 'success',
                    notifiable: $quotationVendor,
                    data: [
                        'customer' => $quotationVendor->requestQuotation->customer_name,
                        'order_number' => $order->order_number,
                        'quotation_vendor_id' => $quotationVendor->id,
                    ],
                    userId: $vendor->user->id,
                    vendorId: $vendor->id
                );
            }

            \DB::commit();

            return [
                'success' => true,
                'quotation_vendor' => $quotationVendor->fresh(),
                'order' => $order,
            ];

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Error accepting offer: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reject vendor offer (multi-vendor workflow)
     */
    public function rejectVendorOffer(int $quotationId, int $vendorId, Customer $customer): array
    {
        $quotation = $this->repository->findForCustomer($quotationId, $customer->id);

        if (!$quotation) {
            return [
                'success' => false,
                'message' => 'Quotation not found',
            ];
        }

        $quotationVendor = $quotation->vendors()
            ->where('vendor_id', $vendorId)
            ->first();

        if (!$quotationVendor) {
            return [
                'success' => false,
                'message' => 'Vendor offer not found',
            ];
        }

        if (!$quotationVendor->canRespondToOffer()) {
            return [
                'success' => false,
                'message' => 'Cannot respond to offer in current status',
            ];
        }

        try {
            \DB::beginTransaction();

            // Reject the offer
            $quotationVendor->rejectOffer();

            // Send notification to vendor using AdminNotification
            $vendor = $quotationVendor->vendor;
            if ($vendor && $vendor->user) {
                \App\Models\AdminNotification::notify(
                    type: 'vendor_offer_rejected',
                    title: 'order::request-quotation.notification_vendor_offer_rejected_title',
                    description: 'order::request-quotation.notification_vendor_offer_rejected_message',
                    url: route('admin.vendor.request-quotations.show', [
                        'lang' => app()->getLocale(),
                        'countryCode' => $quotationVendor->requestQuotation->country->code ?? 'eg',
                        'id' => $quotationVendor->id,
                    ]),
                    icon: 'uil-times-circle',
                    color: 'danger',
                    notifiable: $quotationVendor,
                    data: [
                        'customer' => $quotationVendor->requestQuotation->customer_name,
                        'quotation_vendor_id' => $quotationVendor->id,
                    ],
                    userId: $vendor->user->id,
                    vendorId: $vendor->id
                );
            }

            \DB::commit();

            return [
                'success' => true,
                'quotation_vendor' => $quotationVendor->fresh(),
            ];

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Error rejecting offer: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create order from accepted offer
     */
    protected function createOrderFromOffer($quotationVendor, Customer $customer)
    {
        $quotation = $quotationVendor->requestQuotation;
        
        // Create order data
        $orderData = [
            'customer_id' => $customer->id,
            'customer_name' => $customer->full_name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'customer_address' => $quotation->customerAddress?->address,
            'country_id' => $quotation->country_id,
            'city_id' => $quotation->customerAddress?->city_id,
            'region_id' => $quotation->customerAddress?->region_id,
            'total_price' => $quotationVendor->offer_price,
            'total_product_price' => $quotationVendor->offer_price,
            'payment_type' => 'cash_on_delivery', // Default
            'order_from' => 'quotation',
            'stage_id' => 1, // Default stage (pending/new)
            'items_count' => 1,
            'shipping' => 0,
            'total_tax' => 0,
            'total_fees' => 0,
            'total_discounts' => 0,
        ];

        // Create order
        $order = \Modules\Order\app\Models\Order::create($orderData);

        // Create vendor order stage
        \Modules\Order\app\Models\VendorOrderStage::create([
            'order_id' => $order->id,
            'vendor_id' => $quotationVendor->vendor_id,
            'stage_id' => 1, // Default stage
        ]);

        return $order;
    }
}

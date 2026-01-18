<?php

namespace Modules\Refund\app\Services;

use Illuminate\Support\Facades\Log;
use Modules\Refund\app\Models\RefundRequest;
use Modules\SystemSetting\app\Services\FirebaseService;
use Modules\Customer\app\Models\CustomerFcmToken;
use Modules\Vendor\app\Models\VendorFcmToken;

class RefundNotificationService
{
    public function __construct(
        protected FirebaseService $firebaseService
    ) {}

    /**
     * Notify customer about refund status change
     */
    public function notifyCustomerStatusChange(
        RefundRequest $refundRequest,
        string $oldStatus,
        string $newStatus
    ): void {
        $customer = $refundRequest->customer;
        
        if (!$customer) {
            return;
        }

        // Send Laravel notification if class exists
        if ($customer->user && class_exists('\Modules\Refund\app\Notifications\RefundStatusChangedNotification')) {
            try {
                $customer->user->notify(
                    new \Modules\Refund\app\Notifications\RefundStatusChangedNotification(
                        $refundRequest,
                        $oldStatus,
                        $newStatus
                    )
                );
            } catch (\Exception $e) {
                Log::error('Failed to send refund status notification: ' . $e->getMessage());
            }
        }

        // Send Firebase notification
        $this->sendFirebaseToCustomer(
            customer: $customer,
            title: trans('refund::refund.notifications.status_changed_title'),
            body: trans('refund::refund.notifications.status_changed_body', [
                'refund_number' => $refundRequest->refund_number,
                'status' => trans('refund::refund.statuses.' . $newStatus),
            ]),
            data: [
                'type' => 'refund_status_changed',
                'refund_id' => (string) $refundRequest->id,
                'refund_number' => $refundRequest->refund_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'order_id' => (string) $refundRequest->order_id,
            ]
        );
    }

    /**
     * Notify vendor about refund status change
     */
    public function notifyVendorStatusChange(
        RefundRequest $refundRequest,
        string $oldStatus,
        string $newStatus
    ): void {
        $vendor = $refundRequest->vendor;
        
        if (!$vendor) {
            return;
        }

        // Send Laravel notification if class exists
        if ($vendor->user && class_exists('\Modules\Refund\app\Notifications\RefundVendorStatusChangedNotification')) {
            try {
                $vendor->user->notify(
                    new \Modules\Refund\app\Notifications\RefundVendorStatusChangedNotification(
                        $refundRequest,
                        $oldStatus,
                        $newStatus
                    )
                );
            } catch (\Exception $e) {
                Log::error('Failed to send refund vendor status notification: ' . $e->getMessage());
            }
        }

        // Send Firebase notification
        $this->sendFirebaseToVendor(
            vendor: $vendor,
            title: trans('refund::refund.notifications.status_changed_title'),
            body: trans('refund::refund.notifications.status_changed_body', [
                'refund_number' => $refundRequest->refund_number,
                'status' => trans('refund::refund.statuses.' . $newStatus),
            ]),
            data: [
                'type' => 'refund_status_changed',
                'refund_id' => (string) $refundRequest->id,
                'refund_number' => $refundRequest->refund_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'order_id' => (string) $refundRequest->order_id,
            ]
        );
    }

    /**
     * Notify customer about new refund request created
     */
    public function notifyRefundCreated(RefundRequest $refundRequest): void
    {
        $customer = $refundRequest->customer;
        
        if (!$customer) {
            return;
        }

        $this->sendFirebaseToCustomer(
            customer: $customer,
            title: trans('refund::refund.notifications.refund_created_title'),
            body: trans('refund::refund.notifications.refund_created_body', [
                'refund_number' => $refundRequest->refund_number,
            ]),
            data: [
                'type' => 'refund_created',
                'refund_id' => (string) $refundRequest->id,
                'refund_number' => $refundRequest->refund_number,
                'order_id' => (string) $refundRequest->order_id,
            ]
        );
    }

    /**
     * Notify vendor about new refund request
     */
    public function notifyVendorNewRefund(RefundRequest $refundRequest): void
    {
        $vendor = $refundRequest->vendor;
        
        if (!$vendor) {
            return;
        }

        $this->sendFirebaseToVendor(
            vendor: $vendor,
            title: trans('refund::refund.notifications.new_refund_vendor_title'),
            body: trans('refund::refund.notifications.new_refund_vendor_body', [
                'refund_number' => $refundRequest->refund_number,
                'customer' => $refundRequest->customer->name ?? 'Customer',
            ]),
            data: [
                'type' => 'new_refund_request',
                'refund_id' => (string) $refundRequest->id,
                'refund_number' => $refundRequest->refund_number,
                'order_id' => (string) $refundRequest->order_id,
                'customer_id' => (string) $refundRequest->customer_id,
            ]
        );
    }

    /**
     * Send Firebase notification to customer
     */
    protected function sendFirebaseToCustomer(
        $customer,
        string $title,
        string $body,
        array $data = [],
        ?string $image = null
    ): void {
        try {
            $tokens = CustomerFcmToken::where('customer_id', $customer->id)
                ->pluck('fcm_token')
                ->toArray();

            if (empty($tokens)) {
                return;
            }

            $result = $this->firebaseService->sendToTokensBatch(
                tokens: $tokens,
                title: $title,
                body: $body,
                image: $image,
                data: $data
            );

            Log::info('Firebase refund notification sent to customer', [
                'customer_id' => $customer->id,
                'type' => $data['type'] ?? 'unknown',
                'success' => $result['success'],
                'failed' => $result['failed'],
            ]);

            // Clean up invalid tokens
            if (!empty($result['errors'])) {
                $invalidTokens = collect($result['errors'])->pluck('token')->toArray();
                CustomerFcmToken::where('customer_id', $customer->id)
                    ->whereIn('fcm_token', $invalidTokens)
                    ->delete();
            }
        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notification to customer: ' . $e->getMessage());
        }
    }

    /**
     * Send Firebase notification to vendor
     */
    protected function sendFirebaseToVendor(
        $vendor,
        string $title,
        string $body,
        array $data = [],
        ?string $image = null
    ): void {
        try {
            $tokens = VendorFcmToken::where('vendor_id', $vendor->id)
                ->pluck('fcm_token')
                ->toArray();

            if (empty($tokens)) {
                return;
            }

            $result = $this->firebaseService->sendToTokensBatch(
                tokens: $tokens,
                title: $title,
                body: $body,
                image: $image,
                data: $data
            );

            Log::info('Firebase refund notification sent to vendor', [
                'vendor_id' => $vendor->id,
                'type' => $data['type'] ?? 'unknown',
                'success' => $result['success'],
                'failed' => $result['failed'],
            ]);

            // Clean up invalid tokens
            if (!empty($result['errors'])) {
                $invalidTokens = collect($result['errors'])->pluck('token')->toArray();
                VendorFcmToken::where('vendor_id', $vendor->id)
                    ->whereIn('fcm_token', $invalidTokens)
                    ->delete();
            }
        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notification to vendor: ' . $e->getMessage());
        }
    }
}

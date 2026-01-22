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

        // Create admin notification for vendor dashboard
        $this->createAdminNotification(
            refundRequest: $refundRequest,
            type: 'refund_status_changed',
            title: 'refund::refund.notifications.status_changed_title',
            description: 'refund::refund.notifications.status_changed_body',
            icon: $this->getStatusIcon($newStatus),
            color: $this->getStatusColor($newStatus),
            vendorId: $vendor->id
        );

        // Send Firebase notification (use translated text for push notifications)
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
        
        // Load customer if not already loaded
        if (!$refundRequest->relationLoaded('customer')) {
            $refundRequest->load('customer');
        }
        
        $customer = $refundRequest->customer;
        $customerName = 'Customer';
        
        if ($customer) {
            // Try to get full_name (first_name + last_name)
            if (!empty($customer->first_name) && !empty($customer->last_name)) {
                $customerName = $customer->first_name . ' ' . $customer->last_name;
            } elseif (!empty($customer->first_name)) {
                $customerName = $customer->first_name;
            } elseif (!empty($customer->name)) {
                $customerName = $customer->name;
            }
        }
        
        // Create admin notification for vendor dashboard with translation keys
        $this->createAdminNotification(
            refundRequest: $refundRequest,
            type: 'new_refund_request',
            title: 'refund::refund.notifications.new_refund_vendor_title',
            description: 'refund::refund.notifications.new_refund_vendor_body',
            icon: 'uil-redo',
            color: 'warning',
            vendorId: $vendor->id
        );

        // Send Firebase notification (use translated text for push notifications)
        $this->sendFirebaseToVendor(
            vendor: $vendor,
            title: trans('refund::refund.notifications.new_refund_vendor_title'),
            body: trans('refund::refund.notifications.new_refund_vendor_body', [
                'refund_number' => $refundRequest->refund_number,
                'customer' => $customerName,
            ]),
            data: [
                'type' => 'new_refund_request',
                'refund_id' => (string) $refundRequest->id,
                'refund_number' => $refundRequest->refund_number,
                'order_number' => $refundRequest->order->order_number,
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

    /**
     * Create admin notification for dashboard
     */
    protected function createAdminNotification(
        RefundRequest $refundRequest,
        string $type,
        string $title,
        ?string $description = null,
        string $icon = 'uil-bell',
        string $color = 'primary',
        ?int $userId = null,
        ?int $vendorId = null
    ): void {
        $adminNotificationService = app(\App\Services\AdminNotificationService::class);
        
        // Prepare data array with translation keys
        $data = [
            'refund::refund.fields.refund_number' => $refundRequest->refund_number,
            'order::order.order_number' => $refundRequest->order->order_number,
        ];
        // Add status for status change notifications
        if ($type === 'refund_status_changed') {
            $data['admin.status'] = trans('refund::refund.statuses.' . $refundRequest->status);
        }
        // Add customer full name for new refund notifications
        if ($type === 'new_refund_request') {
            // Load customer if not already loaded
            if (!$refundRequest->relationLoaded('customer')) {
                $refundRequest->load('customer');
            }
            
            $customer = $refundRequest->customer;
            $customerName = 'Customer';
            
            if ($customer) {
                // Try to get full_name (first_name + last_name)
                if (!empty($customer->first_name) && !empty($customer->last_name)) {
                    $customerName = $customer->first_name . ' ' . $customer->last_name;
                } elseif (!empty($customer->first_name)) {
                    $customerName = $customer->first_name;
                } elseif (!empty($customer->name)) {
                    $customerName = $customer->name;
                }
            }
            
            // Use 'refund::refund.fields.customer' as key for the label translation
            // The placeholder ':customer' will still be replaced because we extract the last part 'customer'
            $data['refund::refund.fields.customer'] = $customerName;
        }
        
        $notification = $adminNotificationService->create(
            type: $type,
            title: $title,
            description: $description,
            url: $adminNotificationService->generateAdminUrl('admin.refunds.show', ['id' => $refundRequest->id]),
            icon: $icon,
            color: $color,
            notifiable: $refundRequest,
            data: $data,
            userId: $userId,
            vendorId: $vendorId
        );
        
        if ($notification) {
            Log::info('Admin notification created for refund', [
                'notification_id' => $notification->id,
                'refund_id' => $refundRequest->id,
                'vendor_id' => $vendorId,
                'type' => $type,
            ]);
        }
    }

    /**
     * Get icon for status
     */
    protected function getStatusIcon(string $status): string
    {
        return match($status) {
            'pending' => 'uil-clock',
            'approved' => 'uil-check',
            'in_progress' => 'uil-sync',
            'picked_up' => 'uil-package',
            'refunded' => 'uil-check-circle',
            'rejected' => 'uil-times-circle',
            'cancelled' => 'uil-ban',
            default => 'uil-bell',
        };
    }

    /**
     * Get color for status
     */
    protected function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'warning',
            'approved' => 'info',
            'in_progress' => 'primary',
            'picked_up' => 'secondary',
            'refunded' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'danger',
            default => 'primary',
        };
    }
}

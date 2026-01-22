<?php

namespace Modules\Withdraw\app\Observers;

use Modules\Withdraw\app\Models\Withdraw;
use App\Services\AdminNotificationService;

class WithdrawObserver
{
    public function __construct(protected AdminNotificationService $notificationService)
    {
    }

    /**
     * Handle the Withdraw "created" event.
     */
    public function created(Withdraw $withdraw): void
    {
        // Create admin notification for new withdraw request
        if ($withdraw->status === 'new') {
            $this->createWithdrawRequestNotification($withdraw);
        }
    }

    /**
     * Handle the Withdraw "updated" event.
     */
    public function updated(Withdraw $withdraw): void
    {
        // If withdraw status changed to accepted or rejected, create notification for vendor
        if ($withdraw->wasChanged('status') && in_array($withdraw->status, ['accepted', 'rejected'])) {
            $this->createWithdrawStatusNotification($withdraw);
        }
    }

    /**
     * Create admin notification for new withdraw request
     */
    protected function createWithdrawRequestNotification(Withdraw $withdraw): void
    {
        $vendorName = $withdraw->vendor?->name ?? trans('common.vendor');
        
        $this->notificationService->create(
            type: 'withdraw_request',
            title: 'menu.withdraw module.withdraw_request', // Translation key
            description: 'menu.withdraw module.vendor_sent_request', // Translation key with :vendor placeholder
            url: $this->notificationService->generateAdminUrl('admin.transactionsRequests', ['status' => 'new']),
            icon: 'uil-wallet',
            color: 'warning',
            notifiable: $withdraw,
            data: [
                'menu.withdraw module.withdraw_id' => $withdraw->id,
                'common.vendor' => $vendorName, // This will replace :vendor in the description
            ],
            vendorId: null // For admin only
        );
    }

    /**
     * Create vendor notification for withdraw status change
     */
    protected function createWithdrawStatusNotification(Withdraw $withdraw): void
    {
        $isAccepted = $withdraw->status === 'accepted';
        $vendorName = $withdraw->vendor?->name ?? trans('common.vendor');

        $this->notificationService->create(
            type: 'withdraw_status',
            title: $isAccepted 
                ? 'menu.withdraw module.bnaia_sent_money' // Translation key
                : 'menu.withdraw module.bnaia_rejected_request', // Translation key
            description: $isAccepted
                ? 'menu.withdraw module.request_accepted' // Translation key
                : 'menu.withdraw module.request_rejected', // Translation key
            url: $this->notificationService->generateAdminUrl('admin.transactionsRequests', [
                'status' => $isAccepted ? 'accepted' : 'rejected'
            ]),
            icon: 'uil-wallet',
            color: $isAccepted ? 'success' : 'danger',
            notifiable: $withdraw,
            data: [
                'menu.withdraw module.withdraw_id' => $withdraw->id,
                'menu.withdraw module.status' => $withdraw->status,
                'common.vendor' => $vendorName, // This will replace :vendor in the description
            ],
            vendorId: $withdraw->reciever_id // For specific vendor
        );
    }
}

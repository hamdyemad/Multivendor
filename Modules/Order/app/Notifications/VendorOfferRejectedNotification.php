<?php

namespace Modules\Order\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\Order\app\Models\RequestQuotationVendor;

class VendorOfferRejectedNotification extends Notification
{
    use Queueable;

    protected RequestQuotationVendor $quotationVendor;

    public function __construct(RequestQuotationVendor $quotationVendor)
    {
        $this->quotationVendor = $quotationVendor;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'vendor_offer_rejected',
            'title' => __('order::request-quotation.notification_vendor_offer_rejected_title'),
            'message' => __('order::request-quotation.notification_vendor_offer_rejected_message', [
                'customer' => $this->quotationVendor->requestQuotation->customer_name,
            ]),
            'quotation_id' => $this->quotationVendor->request_quotation_id,
        ];
    }
}

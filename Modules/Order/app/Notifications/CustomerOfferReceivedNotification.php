<?php

namespace Modules\Order\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\Order\app\Models\RequestQuotationVendor;

class CustomerOfferReceivedNotification extends Notification
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
            'type' => 'customer_offer_received',
            'title' => __('order::request-quotation.notification_customer_offer_title'),
            'message' => __('order::request-quotation.notification_customer_offer_message', [
                'vendor' => $this->quotationVendor->vendor->name ?? 'Vendor',
                'price' => number_format($this->quotationVendor->offer_price, 2),
            ]),
            'quotation_id' => $this->quotationVendor->request_quotation_id,
            'vendor_id' => $this->quotationVendor->vendor_id,
            'vendor_name' => $this->quotationVendor->vendor->name ?? null,
            'offer_price' => $this->quotationVendor->offer_price,
            'offer_notes' => $this->quotationVendor->offer_notes,
        ];
    }
}

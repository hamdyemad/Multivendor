<?php

namespace Modules\Order\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\Order\app\Models\RequestQuotationVendor;

class VendorOfferAcceptedNotification extends Notification
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
            'type' => 'vendor_offer_accepted',
            'title' => __('order::request-quotation.notification_vendor_offer_accepted_title'),
            'message' => __('order::request-quotation.notification_vendor_offer_accepted_message', [
                'customer' => $this->quotationVendor->requestQuotation->customer_name,
            ]),
            'quotation_id' => $this->quotationVendor->request_quotation_id,
            'order_id' => $this->quotationVendor->order_id,
            'order_number' => $this->quotationVendor->order?->order_number,
            'url' => $this->quotationVendor->order_id ? route('vendor.orders.show', [
                'lang' => app()->getLocale(),
                'countryCode' => $this->quotationVendor->requestQuotation->country->code ?? 'eg',
                'id' => $this->quotationVendor->order_id,
            ]) : null,
        ];
    }
}

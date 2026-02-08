<?php

namespace Modules\Order\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\Order\app\Models\RequestQuotationVendor;

class VendorQuotationRequestNotification extends Notification
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
        $quotation = $this->quotationVendor->requestQuotation;
        
        return [
            'type' => 'vendor_quotation_request',
            'title' => __('order::request-quotation.notification_vendor_new_request_title'),
            'message' => __('order::request-quotation.notification_vendor_new_request_message', [
                'customer' => $quotation->customer_name,
            ]),
            'quotation_vendor_id' => $this->quotationVendor->id,
            'quotation_id' => $quotation->id,
            'customer_name' => $quotation->customer_name,
            'customer_email' => $quotation->customer_email,
            'customer_phone' => $quotation->customer_phone,
            'notes' => $quotation->notes,
            'file' => $quotation->file,
            'url' => route('admin.vendor.request-quotations.show', [
                'lang' => app()->getLocale(),
                'countryCode' => $quotation->country->code ?? 'eg',
                'id' => $this->quotationVendor->id,
            ]),
        ];
    }
}

# Request Quotation Notifications - Fixed to Use AdminNotification

## Issue
The system was trying to use Laravel's default `notifications` table which doesn't exist. The project uses a custom `admin_notifications` table instead.

## Solution
Replaced all Laravel notification calls with `AdminNotification::notify()` method.

## Changes Made

### 1. VendorQuotationRequestNotification
**File:** `Modules/Order/app/Http/Controllers/RequestQuotationController.php`

**Before:**
```php
$vendor->user->notify(new VendorQuotationRequestNotification($quotation));
```

**After:**
```php
\App\Models\AdminNotification::notify(
    type: 'vendor_quotation_request',
    title: 'order::request-quotation.notification_vendor_new_request_title',
    description: 'order::request-quotation.notification_vendor_new_request_message',
    url: route('admin.vendor.request-quotations.show', [...]),
    icon: 'uil-file-question-alt',
    color: 'info',
    notifiable: $quotationVendor,
    data: ['customer' => $quotation->customer_name, ...],
    userId: $vendor->user->id,
    vendorId: $vendorId
);
```

### 2. CustomerOfferReceivedNotification
**File:** `Modules/Order/app/Http/Controllers/VendorRequestQuotationController.php`

**Before:**
```php
$customer->notify(new CustomerOfferReceivedNotification($quotationVendor));
```

**After:**
```php
\App\Models\AdminNotification::notify(
    type: 'customer_offer_received',
    title: 'order::request-quotation.notification_customer_offer_title',
    description: 'order::request-quotation.notification_customer_offer_message',
    url: route('admin.request-quotations.view-offers', [...]),
    icon: 'uil-envelope-receive',
    color: 'success',
    notifiable: $quotationVendor,
    data: ['vendor' => $vendor->name, 'price' => ...],
    userId: null, // For admin
    vendorId: null
);
```

### 3. VendorOfferAcceptedNotification
**File:** `Modules/Order/app/Services/Api/RequestQuotationApiService.php`

**Before:**
```php
$vendor->user->notify(new VendorOfferAcceptedNotification($quotationVendor));
```

**After:**
```php
\App\Models\AdminNotification::notify(
    type: 'vendor_offer_accepted',
    title: 'order::request-quotation.notification_vendor_offer_accepted_title',
    description: 'order::request-quotation.notification_vendor_offer_accepted_message',
    url: route('admin.vendor.orders.show', [...]),
    icon: 'uil-check-circle',
    color: 'success',
    notifiable: $quotationVendor,
    data: ['customer' => ..., 'order_number' => ...],
    userId: $vendor->user->id,
    vendorId: $vendor->id
);
```

### 4. VendorOfferRejectedNotification
**File:** `Modules/Order/app/Services/Api/RequestQuotationApiService.php`

**Before:**
```php
$vendor->user->notify(new VendorOfferRejectedNotification($quotationVendor));
```

**After:**
```php
\App\Models\AdminNotification::notify(
    type: 'vendor_offer_rejected',
    title: 'order::request-quotation.notification_vendor_offer_rejected_title',
    description: 'order::request-quotation.notification_vendor_offer_rejected_message',
    url: route('admin.vendor.request-quotations.show', [...]),
    icon: 'uil-times-circle',
    color: 'danger',
    notifiable: $quotationVendor,
    data: ['customer' => ...],
    userId: $vendor->user->id,
    vendorId: $vendor->id
);
```

## Benefits

1. **Uses Existing System**: Works with the existing `admin_notifications` table
2. **Consistent**: All notifications follow the same pattern
3. **Translatable**: Titles and descriptions use translation keys
4. **Targeted**: Can target specific users or vendors
5. **Rich Data**: Includes icons, colors, and custom data
6. **Proper URLs**: Direct links to relevant pages with localization

## Notification Types

| Type | Recipient | Trigger | Icon | Color |
|------|-----------|---------|------|-------|
| `vendor_quotation_request` | Vendor | Admin selects vendor | `uil-file-question-alt` | info |
| `customer_offer_received` | Admin | Vendor sends offer | `uil-envelope-receive` | success |
| `vendor_offer_accepted` | Vendor | Customer accepts offer | `uil-check-circle` | success |
| `vendor_offer_rejected` | Vendor | Customer rejects offer | `uil-times-circle` | danger |

## Status
✅ **COMPLETE** - All notifications now use AdminNotification system

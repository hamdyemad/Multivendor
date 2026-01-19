# Refund Helper Functions ✅

## Overview

Created helper functions to get refund days for vendor products with fallback to system defaults.

## Files Created

1. **`Modules/Refund/app/Helpers/RefundHelper.php`** - Helper class with static methods
2. **`Modules/Refund/app/Helpers/refund_helpers.php`** - Global helper functions
3. **Updated `Modules/Refund/composer.json`** - Autoload helper functions

## Helper Class Methods

### `RefundHelper::getRefundDays(?VendorProduct $vendorProduct): int`

Returns refund days for a vendor product:
- If vendor product has `refund_days` set → returns product-specific days
- Otherwise → returns system default from `refund_settings.refund_processing_days`
- Default fallback: 7 days

```php
use Modules\Refund\app\Helpers\RefundHelper;

$days = RefundHelper::getRefundDays($vendorProduct);
```

### `RefundHelper::isEligibleForRefund(?VendorProduct $vendorProduct, $deliveredAt): bool`

Checks if a product is eligible for refund based on delivery date:
- Calculates refund deadline using `getRefundDays()`
- Returns `true` if current date is within refund window
- Returns `false` if deadline passed or invalid data

```php
$isEligible = RefundHelper::isEligibleForRefund($vendorProduct, $order->delivered_at);
```

### `RefundHelper::getRefundDeadline(?VendorProduct $vendorProduct, $deliveredAt): ?Carbon`

Returns the refund deadline date:
- Calculates: `delivered_at + refund_days`
- Returns Carbon instance or null

```php
$deadline = RefundHelper::getRefundDeadline($vendorProduct, $order->delivered_at);
echo $deadline->format('Y-m-d'); // 2026-01-26
```

## Global Helper Functions

### `get_refund_days(?VendorProduct $vendorProduct): int`

Global function wrapper for `RefundHelper::getRefundDays()`.

**Usage:**
```php
// In controllers, views, or anywhere
$days = get_refund_days($vendorProduct);
```

**Examples:**
```php
// Product with custom refund days (30 days)
$vendorProduct->refund_days = 30;
$days = get_refund_days($vendorProduct); // Returns: 30

// Product without custom refund days (uses system default)
$vendorProduct->refund_days = null;
$days = get_refund_days($vendorProduct); // Returns: 7 (system default)

// Null vendor product (uses system default)
$days = get_refund_days(null); // Returns: 7 (system default)
```

### `is_eligible_for_refund(?VendorProduct $vendorProduct, $deliveredAt): bool`

Global function wrapper for `RefundHelper::isEligibleForRefund()`.

**Usage:**
```php
// Check if product can be refunded
if (is_eligible_for_refund($vendorProduct, $order->delivered_at)) {
    // Show refund button
}
```

### `get_refund_deadline(?VendorProduct $vendorProduct, $deliveredAt): ?Carbon`

Global function wrapper for `RefundHelper::getRefundDeadline()`.

**Usage:**
```php
// Get refund deadline
$deadline = get_refund_deadline($vendorProduct, $order->delivered_at);

// In Blade views
{{ $deadline?->format('Y-m-d') }}
{{ $deadline?->diffForHumans() }} // "in 5 days"
```

## Logic Flow

```
┌─────────────────────────────────────┐
│ get_refund_days($vendorProduct)     │
└──────────────┬──────────────────────┘
               │
               ▼
    ┌──────────────────────┐
    │ Has refund_days set? │
    └──────┬───────────────┘
           │
     ┌─────┴─────┐
     │           │
    YES         NO
     │           │
     ▼           ▼
┌─────────┐  ┌──────────────────┐
│ Product │  │ System Settings  │
│  Days   │  │ refund_processing│
│         │  │     _days        │
└─────────┘  └──────────────────┘
     │           │
     └─────┬─────┘
           │
           ▼
    ┌──────────────┐
    │ Return Days  │
    └──────────────┘
```

## Usage in Resources

You can use this in API resources:

```php
// In OrderProductResource.php
'refund_days' => get_refund_days($this->vendorProduct),
'is_eligible_for_refund' => is_eligible_for_refund($this->vendorProduct, $this->order->delivered_at),
'refund_deadline' => get_refund_deadline($this->vendorProduct, $this->order->delivered_at)?->format('Y-m-d'),
```

## Usage in Validation

You can use this in validation rules:

```php
// Check if product is still within refund window
$refundDays = get_refund_days($orderProduct->vendorProduct);
$deliveredAt = $order->delivered_at;
$deadline = $deliveredAt->copy()->addDays($refundDays);

if (now()->gt($deadline)) {
    $fail('Refund period has expired');
}
```

## Autoloading

The helper functions are automatically loaded via `composer.json`:

```json
"autoload": {
    "files": [
        "app/Helpers/refund_helpers.php"
    ]
}
```

After adding new helpers, run:
```bash
composer dump-autoload
```

## System Settings

Default refund days are stored in `refund_settings` table:
- Field: `refund_processing_days`
- Default: 7 days
- Configurable via: Dashboard → Refund Settings

## Product Settings

Product-specific refund days are stored in `vendor_products` table:
- Field: `refund_days`
- Nullable (uses system default if null)
- Configurable via: Product Edit → Refund Settings

## Status

✅ **COMPLETE** - Refund helper functions created and autoloaded

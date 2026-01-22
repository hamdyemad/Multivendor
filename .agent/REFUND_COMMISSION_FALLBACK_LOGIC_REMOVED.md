# Refund Commission Fallback Logic Removed

## Issue
User reported that "= Remaining" still showed 49.50 EGP instead of 0.00 EGP in the vendor section of order #249.

The 49.50 EGP was the old incorrect commission value (15% of 330 EGP).

## Root Cause
In the multi-vendor section of `orders/show.blade.php`, there was fallback logic that incorrectly used department commission when `order_products.commission = 0`:

```php
$commPercent = $orderProduct->commission > 0 
    ? $orderProduct->commission 
    : ($orderProduct->vendorProduct?->product?->department?->commission ?? 0);
```

This is wrong because:
- Commission = 0 is a **valid value** meaning "no commission"
- When order is paid with points, commission should be 0%
- Fallback to department commission should NEVER happen

## Solution
Removed the fallback logic and read commission directly from `order_products` table:

```php
// Get commission from order_products table (0 is valid, means no commission)
$commPercent = $orderProduct->commission ?? 0;

$itemRefundAmount = $item->total_price + $item->shipping_amount;
if ($itemRefundAmount > 0 && $commPercent > 0) {
    $vendorRefundedCommission += ($itemRefundAmount * $commPercent) / 100;
}
```

## Changes Made

### 1. File: `Modules/Order/resources/views/orders/show.blade.php`

#### Single Vendor Section (around line 1080-1095)

**Before**:
```php
foreach ($refund->items as $item) {
    $orderProduct = $item->orderProduct;
    if ($orderProduct) {
        $commPercent = $orderProduct->commission > 0 
            ? $orderProduct->commission 
            : ($orderProduct->vendorProduct?->product?->department?->commission ?? 0);
        
        $itemRefundAmount = $item->total_price + $item->shipping_amount;
        $vendorRefundedCommission += ($itemRefundAmount * $commPercent) / 100;
    }
}
```

**After**:
```php
foreach ($refund->items as $item) {
    $orderProduct = $item->orderProduct;
    if ($orderProduct) {
        // Get commission from order_products table (0 is valid, means no commission)
        $commPercent = $orderProduct->commission ?? 0;
        
        $itemRefundAmount = $item->total_price + $item->shipping_amount;
        if ($itemRefundAmount > 0 && $commPercent > 0) {
            $vendorRefundedCommission += ($itemRefundAmount * $commPercent) / 100;
        }
    }
}
```

#### 2. Multi-Vendor Section (around line 1300-1315)

**Before**:
```php
foreach ($refund->items as $item) {
    $orderProduct = $item->orderProduct;
    if ($orderProduct) {
        $commPercent = $orderProduct->commission > 0 
            ? $orderProduct->commission 
            : ($orderProduct->vendorProduct?->product?->department?->commission ?? 0);
        
        $itemRefundAmount = $item->total_price + $item->shipping_amount;
        $vendorRefundedCommission += ($itemRefundAmount * $commPercent) / 100;
    }
}
```

**After**:
```php
foreach ($refund->items as $item) {
    $orderProduct = $item->orderProduct;
    if ($orderProduct) {
        // Get commission from order_products table (0 is valid, means no commission)
        $commPercent = $orderProduct->commission ?? 0;
        
        $itemRefundAmount = $item->total_price + $item->shipping_amount;
        if ($itemRefundAmount > 0 && $commPercent > 0) {
            $vendorRefundedCommission += ($itemRefundAmount * $commPercent) / 100;
        }
    }
}
```

## Verification

Created and ran `debug_component_params.php` to verify the calculation:

```
=== Component Parameters ===
total: 330
commissionAmount: 0
refundedAmount: 330
refundedCommission: 0
finalCommission: 0
remaining: 0

✅ Remaining is 0 - CORRECT!
```

## Expected Result

For order #249 with vendor #107:
- Total: 330.00 EGP
- Commission: 0.00 EGP (paid with points)
- Refunded Amount: 330.00 EGP
- Refunded Commission: 0.00 EGP
- **Remaining: 0.00 EGP** ✅

## Note for User

If the page still shows 49.50 EGP, please:
1. Hard refresh the browser: **Ctrl + F5** (Windows) or **Cmd + Shift + R** (Mac)
2. Clear browser cache
3. The calculation is now correct on the backend

## Related Files
- `Modules/Order/resources/views/orders/show.blade.php` (single and multi-vendor sections)
- `Modules/Accounting/app/Services/AccountingService.php` (commission calculation)
- `debug_component_params.php` (verification script)

## Related Documentation
- `.agent/COMMISSION_FALLBACK_LOGIC_REMOVED.md` - Previous fix for similar issue
- `.agent/COMMISSION_ZERO_ON_POINTS_PAYMENT.md` - Commission = 0 is valid
- `.agent/REFUND_COMMISSION_FROM_REFUND_MODEL.md` - Read commission from refund model


### 2. File: `Modules/Accounting/app/Services/AccountingService.php`

#### First Occurrence (around line 99-105)

**Before**:
```php
// Calculate commission on total with extras
// commission field stores the percentage, fallback to department commission
$totalCommissionAmount = $products->sum(function($product) {
    $productTotal = $product->price + ($product->shipping_cost ?? 0);
    $commissionPercent = $product->commission > 0 
        ? $product->commission 
        : ($product->vendorProduct?->product?->department?->commission ?? 0);
    return $productTotal * ($commissionPercent / 100);
});
```

**After**:
```php
// Calculate commission on total with extras
// Commission = 0 is valid (means no commission), don't fallback to department
$totalCommissionAmount = $products->sum(function($product) {
    $productTotal = $product->price + ($product->shipping_cost ?? 0);
    $commissionPercent = $product->commission ?? 0;
    return $productTotal * ($commissionPercent / 100);
});
```

#### Second Occurrence (around line 267-273)

**Before**:
```php
// Calculate commission on total with extras
// commission field stores the percentage, fallback to department commission
$totalCommissionAmount = $products->sum(function($product) {
    $productTotal = $product->price + ($product->shipping_cost ?? 0);
    $commissionPercent = $product->commission > 0 
        ? $product->commission 
        : ($product->vendorProduct?->product?->department?->commission ?? 0);
    return $productTotal * ($commissionPercent / 100);
});
```

**After**:
```php
// Calculate commission on total with extras
// Commission = 0 is valid (means no commission), don't fallback to department
$totalCommissionAmount = $products->sum(function($product) {
    $productTotal = $product->price + ($product->shipping_cost ?? 0);
    $commissionPercent = $product->commission ?? 0;
    return $productTotal * ($commissionPercent / 100);
});
```

## Impact

These fixes ensure that:
1. **Order show page** displays correct remaining amount (0.00 EGP for fully refunded orders with 0% commission)
2. **Accounting entries** are created with correct commission amounts (0 when commission = 0%)
3. **No fallback to department commission** when order_products.commission = 0

This is critical for orders paid with points where commission should always be 0%.

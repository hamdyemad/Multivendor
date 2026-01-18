# Vendor Stage Validation - Per Product ✅

## Implementation Summary

Validation now checks if the **specific vendor** who owns each product has delivered their portion of the order, not all vendors.

## Validation Logic

The validation is now in the `items.*.order_product_id` rule, checking each product individually:

1. **Order Product Validation**: Verifies product belongs to the order
2. **Refund Status Check**: Ensures product not already refunded
3. **Pending Refund Check**: Ensures no pending refund for this product
4. **Vendor Assignment Check**: Verifies product has a vendor assigned
5. **Vendor Stage Check**: Ensures vendor has a delivery stage for this order
6. **Delivery Status Check**: Validates that the vendor's stage has `type='delivered'`

```php
// Get vendor_id from the product
$vendorId = $orderProduct->product->vendor_id ?? null;

if (!$vendorId) {
    $fail(trans('refund::refund.validation.product_no_vendor'));
    return;
}

// Check if this vendor has delivered their products
$vendorStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $orderId)
    ->where('vendor_id', $vendorId)
    ->with('stage')
    ->first();

if (!$vendorStage) {
    $fail(trans('refund::refund.validation.vendor_no_stage'));
    return;
}

// Check if vendor has delivered
if (!$vendorStage->stage || $vendorStage->stage->type !== 'delivered') {
    $fail(trans('refund::refund.validation.vendor_not_delivered'));
    return;
}
```

## Translation Keys Added

### English (`Modules/Refund/lang/en/refund.php`)
- `product_no_vendor`: "This product has no vendor assigned."
- `vendor_no_stage`: "The vendor has no delivery stage for this order."
- `vendor_not_delivered`: "The vendor must deliver the product before requesting a refund."

### Arabic (`Modules/Refund/lang/ar/refund.php`)
- `product_no_vendor`: "هذا المنتج ليس لديه مورد مخصص."
- `vendor_no_stage`: "المورد ليس لديه مرحلة تسليم لهذا الطلب."
- `vendor_not_delivered`: "يجب على المورد تسليم المنتج قبل طلب الاسترجاع."

## How It Works - Multi-Vendor Example

**Scenario**: Order #123 has 6 products from 3 vendors
- Vendor A: Products 1, 2 (delivered ✅)
- Vendor B: Products 3, 4 (in transit ❌)
- Vendor C: Products 5, 6 (delivered ✅)

**Customer tries to refund**:
- Products 1, 2 from Vendor A → ✅ Allowed (vendor delivered)
- Products 3, 4 from Vendor B → ❌ Blocked (vendor not delivered yet)
- Products 5, 6 from Vendor C → ✅ Allowed (vendor delivered)

Each product is validated individually based on its vendor's delivery status.

## Files Modified

- `Modules/Refund/app/Http/Requests/Api/StoreRefundRequestRequest.php`
- `Modules/Refund/lang/en/refund.php`
- `Modules/Refund/lang/ar/refund.php`

## Status

✅ **COMPLETE** - Per-product vendor stage validation implemented with translations


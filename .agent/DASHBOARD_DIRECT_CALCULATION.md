# Dashboard Direct Calculation (No Accounting Entries)

## Change Summary
Changed dashboard statistics calculation to compute **Total Income** and **Net Profit** directly from Orders and Refunds, instead of relying on Accounting Entries.

## Why This Change?
- **More Accurate**: Calculates from source data (orders/refunds) instead of intermediate accounting entries
- **Self-Correcting**: If accounting entries have errors, dashboard still shows correct numbers
- **Transparent**: Clear calculation logic that matches business rules

## Implementation

### Before (Using Accounting Entries)
```php
$totalIncome = AccountingEntry::where('type', 'income')->sum('amount');
$totalRefunds = AccountingEntry::where('type', 'refund')->sum('amount');
$netIncome = $totalIncome - $totalRefunds;
```

### After (Direct Calculation)
```php
// Get all delivered orders
$deliveredStage = OrderStage::withoutCountryFilter()->where('type', 'deliver')->first();

$deliveredOrders = VendorOrderStage::where('stage_id', $deliveredStage->id)
    ->whereHas('order')
    ->with(['order.products', 'order.products.taxes'])
    ->get();

$totalIncome = 0;
foreach ($deliveredOrders as $vendorStage) {
    $order = $vendorStage->order;
    $vendorId = $vendorStage->vendor_id;
    
    // Get vendor products
    $vendorProducts = $order->products->where('vendor_id', $vendorId);
    
    // Calculate: products + shipping + fees - discounts
    $vendorTotal = $vendorProducts->sum('price') + $vendorProducts->sum('shipping_cost');
    
    $vendorFees = OrderExtraFeeDiscount::where('order_id', $order->id)
        ->where('vendor_id', $vendorId)
        ->where('type', 'fee')
        ->sum('cost') ?? 0;
    
    $vendorDiscounts = OrderExtraFeeDiscount::where('order_id', $order->id)
        ->where('vendor_id', $vendorId)
        ->where('type', 'discount')
        ->sum('cost') ?? 0;
    
    $totalIncome += $vendorTotal + $vendorFees - $vendorDiscounts;
}

// Calculate refunds
$refundedRequests = RefundRequest::where('status', 'refunded')->get();

$totalRefunds = 0;
foreach ($refundedRequests as $refund) {
    // Vendor deduction: products + shipping + fees - discounts - return shipping
    $vendorDeduction = $refund->total_products_amount 
        + $refund->total_shipping_amount 
        + ($refund->vendor_fees_amount ?? 0)
        - ($refund->vendor_discounts_amount ?? 0)
        - ($refund->return_shipping_cost ?? 0);
    
    $totalRefunds += $vendorDeduction;
}

$netIncome = $totalIncome - $totalRefunds;
```

## Calculation Logic

### Total Income
For each delivered order, for each vendor:
```
Vendor Income = Products + Shipping + Fees - Discounts
```

Where:
- **Products**: Sum of all product prices (with tax) for this vendor
- **Shipping**: Sum of shipping costs for this vendor's products
- **Fees**: Extra fees allocated to this vendor (from `order_extra_fees_discounts`)
- **Discounts**: Discounts allocated to this vendor (from `order_extra_fees_discounts`)

### Total Refunds
For each refunded request:
```
Vendor Deduction = Products + Shipping + Fees - Discounts - Return Shipping
```

Where:
- **Products**: `total_products_amount` from refund request
- **Shipping**: `total_shipping_amount` from refund request
- **Fees**: `vendor_fees_amount` from refund request
- **Discounts**: `vendor_discounts_amount` from refund request
- **Return Shipping**: `return_shipping_cost` from refund request

### Net Income
```
Net Income = Total Income - Total Refunds
```

## Verification Results

### Order #253 Example
**Delivered Orders:**
- Vendor #38 (Total Tools): 207.35 EGP
- Vendor #107 (Value): 694.13 EGP
- Vendor #199 (sanipure): 3,511.40 EGP
- **Total Income**: 4,412.88 EGP

**Refunded Requests:**
- Refund #76 (Value): 462.75 EGP
- Refund #77 (Value): 231.38 EGP
- **Total Refunds**: 694.13 EGP

**Net Income**: 3,718.75 EGP ✅

### Comparison with Accounting Entries
- ✅ Income matches: 4,412.88 EGP
- ✅ Refunds match: 694.13 EGP
- ✅ Net Income matches: 3,718.75 EGP

## Files Changed
- `app/Services/DashboardService.php` - `getStatistics()` method (lines ~306-420)

## Benefits
1. **Accuracy**: Always calculates from source data
2. **Independence**: Not affected by accounting entry errors
3. **Transparency**: Clear business logic
4. **Maintainability**: Easier to understand and debug
5. **Consistency**: Same calculation logic as order show page

## Testing
Run `test_dashboard_direct_calculation.php` to verify calculations match accounting entries.

## Related Documentation
- `.agent/REFUND_REMAINING_CALCULATION_FIX.md` - Vendor deduction calculation
- `.agent/REFUND_FINAL_FIXES.md` - Complete refund system fixes

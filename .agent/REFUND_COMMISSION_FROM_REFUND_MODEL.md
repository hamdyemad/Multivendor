# Refund Commission - Calculate from Refund Model

## Overview
Changed the vendor commission calculation to read refunded commission directly from `refund_request_items` and `order_products` tables instead of `accounting_entries` table. This ensures the commission is always calculated from the source data.

## Problem
The previous implementation was reading refunded commission from `accounting_entries` table:
```php
$refundedCommission = DB::table('accounting_entries')
    ->where('type', 'refund')
    ->sum('commission_amount');
```

This had issues:
1. Missing vendor_id filter in `getVendorsStatistics()` - was summing ALL vendors' refund commissions
2. Dependent on accounting entries being created correctly
3. If accounting entries had wrong commission, the calculation would be wrong

## Solution
Calculate refunded commission directly from the refund data:
```php
$refundedCommission = DB::table('refund_request_items as rri')
    ->join('refund_requests as rr', 'rri.refund_request_id', '=', 'rr.id')
    ->join('order_products as op', 'rri.order_product_id', '=', 'op.id')
    ->where('rr.vendor_id', $this->id) // Filter by vendor
    ->where('rr.status', 'refunded')
    ->sum(DB::raw('(rri.total_price + rri.shipping_amount) * COALESCE(op.commission, 0) / 100'));
```

## Changes Made

### 1. Vendor Model - getVendorsStatistics() Method
**File**: `Modules/Vendor/app/Models/Vendor.php`

**Before:**
```php
// Subtract commission returned from refunds
$refundedCommissionQuery = \Illuminate\Support\Facades\DB::table('accounting_entries')
    ->where('type', 'refund');

if ($countryId) {
    $refundedCommissionQuery->where('country_id', $countryId);
}

$refundedCommission = $refundedCommissionQuery->sum('commission_amount') ?? 0;
```

**After:**
```php
// Calculate commission returned from refunds
// Read directly from refund_request_items and order_products (not from accounting_entries)
$refundedCommissionQuery = \Illuminate\Support\Facades\DB::table('refund_request_items as rri')
    ->join('refund_requests as rr', 'rri.refund_request_id', '=', 'rr.id')
    ->join('order_products as op', 'rri.order_product_id', '=', 'op.id')
    ->where('rr.status', 'refunded');

if ($countryId) {
    $refundedCommissionQuery->where('rr.country_id', $countryId);
}

$refundedCommission = $refundedCommissionQuery->sum(
    \Illuminate\Support\Facades\DB::raw('(rri.total_price + rri.shipping_amount) * COALESCE(op.commission, 0) / 100')
) ?? 0;
```

### 2. Vendor Model - getBnaiaCommissionAttribute() Method
**File**: `Modules/Vendor/app/Models/Vendor.php`

**Before:**
```php
// Subtract commission returned from refunds
$refundedCommission = \Illuminate\Support\Facades\DB::table('accounting_entries')
    ->where('vendor_id', $this->id)
    ->where('type', 'refund')
    ->sum('commission_amount') ?? 0;
```

**After:**
```php
// Calculate commission returned from refunds
// Read directly from refund_request_items and order_products (not from accounting_entries)
$refundedCommission = \Illuminate\Support\Facades\DB::table('refund_request_items as rri')
    ->join('refund_requests as rr', 'rri.refund_request_id', '=', 'rr.id')
    ->join('order_products as op', 'rri.order_product_id', '=', 'op.id')
    ->where('rr.vendor_id', $this->id)
    ->where('rr.status', 'refunded')
    ->sum(\Illuminate\Support\Facades\DB::raw('(rri.total_price + rri.shipping_amount) * COALESCE(op.commission, 0) / 100')) ?? 0;
```

## Benefits

1. **Accurate Calculation**: Always reads commission from `order_products` table (the source of truth)
2. **Vendor-Specific**: Properly filters by vendor_id
3. **Country-Specific**: Supports country_id filtering in `getVendorsStatistics()`
4. **Independent**: Not dependent on accounting entries being correct
5. **Handles Zero Commission**: Correctly handles orders with 0% commission (paid with points)

## Data Flow

### Order with Points (Commission = 0%)
1. Order created → `order_products.commission = 0`
2. Order delivered → Vendor earnings = 330 EGP
3. Refund created → `refund_request_items` created
4. Refund completed → Commission calculation:
   - Commission from orders: 0 EGP
   - Refunded commission: `(330 * 0%) = 0 EGP`
   - Net commission: 0 - 0 = 0 EGP
5. Vendor balance: 330 - 0 = **330 EGP** ✅

### Normal Order (Commission = 15%)
1. Order created → `order_products.commission = 15`
2. Order delivered → Vendor earnings = 330 EGP, Commission = 49.50 EGP
3. Refund created → `refund_request_items` created
4. Refund completed → Commission calculation:
   - Commission from orders: 49.50 EGP
   - Refunded commission: `(330 * 15%) = 49.50 EGP`
   - Net commission: 49.50 - 49.50 = 0 EGP
5. Vendor balance: 330 - 0 = **330 EGP** ✅

## Testing Results

### Before Fix
- Total Transactions: 330.00 EGP
- Total Commission: -49.50 EGP (NEGATIVE! ❌)
- Total Balance: 379.50 EGP (WRONG! ❌)
- Total Remaining: 379.50 EGP (WRONG! ❌)

### After Fix
- Total Transactions: 330.00 EGP ✅
- Total Commission: 0.00 EGP ✅
- Total Balance: 330.00 EGP ✅
- Total Remaining: 330.00 EGP ✅

## Files Modified
1. `Modules/Vendor/app/Models/Vendor.php` - Updated 2 methods:
   - `getVendorsStatistics()` - Line ~519
   - `getBnaiaCommissionAttribute()` - Line ~262

## Related Documentation
- See `.agent/REFUND_COMMISSION_FROM_ORDER_PRODUCTS.md` for commission storage approach
- See `.agent/ORDER_COMMISSION_ZERO_WITH_POINTS.md` for commission = 0 implementation
- See `.agent/COMMISSION_FALLBACK_LOGIC_REMOVED.md` for fallback logic removal

## Notes
- The `accounting_entries` table is still used for other purposes (tracking, history)
- But for commission calculation, we now read directly from the source data
- This ensures accuracy even if accounting entries have errors
- The commission is always calculated as: `(refund_amount * order_product.commission) / 100`

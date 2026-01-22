# Dashboard Charts Earnings Calculation Fix (CORRECTED)

## Issue
User reported that the Net Earnings graph shows incorrect values. The graph should show all zeros for 2026 since there's only one order that was fully refunded.

The graph displays three lines:
- **Earnings** (blue) - Total delivered orders value
- **Refunds** (red) - Total refunded amount  
- **Net Earnings** (green) - Earnings minus Refunds

## Root Cause Analysis

### Initial Misunderstanding
Initially thought that `promo_code_share` and `points_share` should be **added** to earnings because they represent what Bnaia pays to the vendor.

### Actual Understanding
After investigation, discovered that:
- `order_products.price` = Full product price (e.g., 230 EGP)
- `order_products.shipping_cost` = Shipping cost (e.g., 100 EGP)
- **Total = 330 EGP** (what vendor receives)

When customer uses promo code or points:
- `order.total_price` = 0 EGP (customer pays nothing)
- `order.customer_promo_code_amount` = 20 EGP
- `order.points_cost` = 310 EGP
- `vendor_order_stages.promo_code_share` = 20 EGP (Bnaia pays vendor)
- `vendor_order_stages.points_share` = 310 EGP (Bnaia pays vendor)

**Key Insight**: The shares are **payment source**, not additional income!
- Vendor receives: 330 EGP total
- Payment comes from: Customer (0) + Bnaia promo (20) + Bnaia points (310) = 330 EGP
- The `price` field already includes the full value

### Incorrect Calculation (First Attempt):
```php
$earnings = $productsShipping + $promoTotal + $pointsTotal;
// = 330 + 20 + 310 = 660 EGP ❌ (WRONG - double counting!)
```

### Correct Calculation:
```php
$earnings = $productsShipping;
// = 330 EGP ✅ (Correct - this is what vendor receives)
```

## Solution
Changed all three methods in `DashboardService.php` to use only `products + shipping` without adding shares.

## Changes Made

### File: `app/Services/DashboardService.php`

#### 1. Method: `getEarningsChartData()` (around line 676-695)

**Before (WRONG)**:
```php
// Vendor receives: price + shipping_cost + promo_code_share + points_share
$calcDeliveredEarnings = function($startTime, $endTime, $dateField = 'whereBetween') use ($deliveredStageId) {
    // ... get products + shipping ...
    // ... get promo and points shares ...
    
    return $productsShipping + $promoTotal + $pointsTotal; // ❌ Double counting!
};
```

**After (CORRECT)**:
```php
// Vendor receives: price + shipping_cost (shares are just payment source, not additional)
$calcDeliveredEarnings = function($startTime, $endTime, $dateField = 'whereBetween') use ($deliveredStageId) {
    // ... get products + shipping ...
    
    return $productsShipping; // ✅ Correct!
};
```

#### 2. Method: `getVendorEarningsChartData()` (around line 825-850)

**Before (WRONG)**:
```php
$calcVendorEarnings = function($startTime, $endTime, $dateField = 'whereBetween') use ($vendorId, $deliveredStageId) {
    // ... get products + shipping ...
    // ... get promo and points shares ...
    
    return $productsShipping + $promoTotal + $pointsTotal; // ❌
};
```

**After (CORRECT)**:
```php
$calcVendorEarnings = function($startTime, $endTime, $dateField = 'whereBetween') use ($vendorId, $deliveredStageId) {
    // ... get products + shipping ...
    
    return $productsShipping; // ✅
};
```

#### 3. Method: `getNetSalesChartData()` (around line 1515-1520)

**Before (WRONG)**:
```php
$deliveredEarnings = $productsShipping + $promoTotal + $pointsTotal; // ❌
```

**After (CORRECT)**:
```php
$deliveredEarnings = $productsShipping; // ✅
```

## Example: Order #249

### Order Details:
- Products: 230 EGP
- Shipping: 100 EGP
- **Total: 330 EGP**
- Customer paid: 0 EGP (used 20 promo + 310 points)
- Bnaia pays vendor: 20 + 310 = 330 EGP

### Calculation:
- **Earnings**: 330 EGP (products + shipping)
- **Refunds**: 330 EGP (full refund)
- **Net Earnings**: 330 - 330 = **0 EGP** ✅

### Before Fix:
- Earnings: 660 EGP (330 + 20 + 310) ❌ Double counting!
- Refunds: 330 EGP
- Net Earnings: 330 EGP ❌ Should be 0!

### After Fix:
- Earnings: 330 EGP ✅
- Refunds: 330 EGP ✅
- Net Earnings: 0 EGP ✅

## Impact

All dashboard charts now show correct values:
- **Earnings Chart**: Shows actual vendor earnings (products + shipping)
- **Net Earnings Chart**: Shows correct net (earnings - refunds)
- **For fully refunded orders**: Net Earnings = 0 ✅

## Key Takeaway

`promo_code_share` and `points_share` are **NOT additional income**. They are just the **payment source** that replaces customer payment. The vendor receives the same total amount regardless of who pays (customer or Bnaia).

**Formula**:
```
Vendor Receives = Products + Shipping
Payment Source = Customer Payment + Bnaia Promo Share + Bnaia Points Share
```

Both equal the same amount!

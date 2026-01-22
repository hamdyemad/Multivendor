# Refund Total Calculation Fix

## Problem
The `total_refund_amount` in `refund_requests` table was being calculated incorrectly. It was subtracting `promo_code_amount` and `points_used` from the refund amount, resulting in 0 EGP for orders paid with points/promo codes.

### Example (Order #249):
- Products: 230 EGP
- Shipping: 100 EGP
- **Total: 330 EGP**
- Promo Code: -20 EGP
- Points: -310 EGP
- Customer Paid: 0 EGP

**Old Calculation:**
```
total_refund_amount = 330 - 20 - 310 = 0 EGP ❌
```

This caused the "Remaining" calculation to be wrong:
- Remaining Before Refund: 330 EGP
- Total Refunded: 0 EGP
- **Final Remaining: 330 - 0 = 330 EGP** ❌ (should be 0)

## Solution
Removed the subtraction of `promo_code_amount` and `points_used` from the `calculateTotals()` method in `RefundRequest` model.

### Rationale
- `promo_code_amount` and `points_used` are **metadata** fields that track what the customer used
- The customer paid 0 EGP (or reduced amount) because of promo/points
- But the **vendor receives the full product value** from Bnaia (via promo_code_share and points_share)
- When refunding:
  - Customer gets back what they paid (0 EGP or reduced amount)
  - Bnaia deducts the promo_code_share/points_share from vendor's balance
- So `total_refund_amount` should be the **full product value** (what vendor receives/loses)

**New Calculation:**
```
total_refund_amount = 330 EGP ✅
```

This makes the "Remaining" calculation correct:
- Remaining Before Refund: 330 EGP (includes promo_code_share + points_share)
- Total Refunded: 330 EGP
- **Final Remaining: 330 - 330 = 0 EGP** ✅

## Changes Made

### 1. RefundRequest Model - calculateTotals() Method
**File**: `Modules/Refund/app/Models/RefundRequest.php`

**Before:**
```php
$subtotal = $this->total_products_amount 
    + $this->total_shipping_amount
    - $this->total_discount_amount;

$subtotal += ($this->vendor_fees_amount ?? 0);
$subtotal -= ($this->vendor_discounts_amount ?? 0);
$subtotal -= ($this->promo_code_amount ?? 0);  // ❌ WRONG
$subtotal -= ($this->points_used ?? 0);        // ❌ WRONG

$this->total_refund_amount = $subtotal - ($this->return_shipping_cost ?? 0);
```

**After:**
```php
$subtotal = $this->total_products_amount 
    + $this->total_shipping_amount
    - $this->total_discount_amount;

$subtotal += ($this->vendor_fees_amount ?? 0);
$subtotal -= ($this->vendor_discounts_amount ?? 0);

// NOTE: Do NOT subtract promo_code_amount and points_used here!
// These are metadata fields that track what the customer used, but:
// - The customer paid 0 EGP (or reduced amount) because of promo/points
// - But the vendor should receive the full product value from Bnaia
// - When refunding, the customer gets back what they paid (0 or reduced)
// - And Bnaia deducts the promo_code_share/points_share from vendor's balance
// So the total_refund_amount should be the full product value (what vendor receives)

$this->total_refund_amount = $subtotal - ($this->return_shipping_cost ?? 0);
```

### 2. Order Show Page - Remaining Calculation
**File**: `Modules/Order/resources/views/orders/show.blade.php`

Added vendor shares (promo_code_share + points_share) to the remaining calculation:

**Before:**
```php
$totalRemaining = $totalWithShippingOrder - $totalCommission;
```

**After:**
```php
// Get vendor shares (promo code and points that Bnaia pays to vendor)
$vendorOrderStages = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)->get();
$totalPromoCodeShare = $vendorOrderStages->sum('promo_code_share') ?? 0;
$totalPointsShare = $vendorOrderStages->sum('points_share') ?? 0;

// Recalculate remaining correctly: Total with Shipping + Vendor Shares - Commission
// Vendor shares (promo_code_share + points_share) are what Bnaia pays to vendor
$totalRemaining = $totalWithShippingOrder + $totalPromoCodeShare + $totalPointsShare - $totalCommission;
```

## Data Flow

### Order with Points (Commission = 0%)
1. Customer creates order:
   - Products: 230 EGP
   - Shipping: 100 EGP
   - Total: 330 EGP
   - Promo Code: -20 EGP
   - Points: -310 EGP
   - **Customer Pays: 0 EGP**

2. Order delivered:
   - Vendor receives: 330 EGP (from Bnaia)
   - Bnaia pays: 20 EGP (promo_code_share) + 310 EGP (points_share) = 330 EGP
   - Commission: 0 EGP
   - **Vendor Balance: +330 EGP**

3. Customer creates refund:
   - Refund Amount: 330 EGP (full product value)
   - Customer gets back: 0 EGP (what they paid)
   - Bnaia deducts from vendor: 330 EGP

4. Refund completed:
   - Vendor Balance: 330 - 330 = **0 EGP** ✅

### Order with Promo Code (Commission = 15%)
1. Customer creates order:
   - Products: 230 EGP
   - Shipping: 100 EGP
   - Total: 330 EGP
   - Promo Code: -30 EGP
   - **Customer Pays: 300 EGP**

2. Order delivered:
   - Vendor receives: 330 EGP (from customer + Bnaia)
   - Bnaia pays: 30 EGP (promo_code_share)
   - Commission: 49.50 EGP (15% of 330)
   - **Vendor Balance: +330 - 49.50 = 280.50 EGP**

3. Customer creates refund:
   - Refund Amount: 330 EGP (full product value)
   - Customer gets back: 300 EGP (what they paid)
   - Bnaia deducts from vendor: 330 EGP
   - Commission reversed: +49.50 EGP

4. Refund completed:
   - Vendor Balance: 280.50 - 330 + 49.50 = **0 EGP** ✅

## Testing Results

### Before Fix
- Total Refund Amount: 0.00 EGP ❌
- Remaining Before Refund: 330.00 EGP
- Net Refund Impact: 0.00 EGP
- **Final Remaining: 330.00 EGP** ❌

### After Fix
- Total Refund Amount: 330.00 EGP ✅
- Remaining Before Refund: 330.00 EGP
- Net Refund Impact: 330.00 EGP
- **Final Remaining: 0.00 EGP** ✅

## Files Modified
1. `Modules/Refund/app/Models/RefundRequest.php` - `calculateTotals()` method
2. `Modules/Order/resources/views/orders/show.blade.php` - Remaining calculation

## Migration Script
Created `fix_refund_72_totals.php` to recalculate totals for existing refund #72.

## Related Documentation
- See `.agent/REFUND_COMMISSION_FROM_REFUND_MODEL.md` for commission calculation
- See `.agent/ORDER_COMMISSION_ZERO_WITH_POINTS.md` for commission = 0 implementation

## Notes
- The `promo_code_amount` and `points_used` fields are kept for tracking purposes
- They show what the customer used, but don't affect the vendor's balance calculation
- The vendor's balance is affected by `promo_code_share` and `points_share` in `vendor_order_stages` table
- When refunding, the full product value is deducted from vendor's balance

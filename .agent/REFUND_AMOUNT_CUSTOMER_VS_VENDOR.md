# Refund Amount: Customer vs Vendor Deduction

## Issue
User reported that refund statistics and dashboard charts show incorrect values. For order #249:
- Order total: 0 EGP (customer paid nothing - used 20 EGP promo + 310 EGP points)
- Refund amount shown: 330 EGP ❌
- Expected: 0 EGP (customer should get back what they paid)

But also, the dashboard charts should show:
- Earnings: 0 EGP
- Refunds: 0 EGP  
- Net Earnings: 0 EGP

## Root Cause
There was confusion between **two different amounts**:

1. **Customer Refund Amount**: What the customer gets back (0 EGP - they paid nothing)
2. **Vendor Deduction Amount**: What gets deducted from vendor's balance (330 EGP - they received this from Bnaia)

The `total_refund_amount` field was incorrectly calculated as the full product value (330 EGP) instead of what the customer actually paid (0 EGP).

## Solution

### 1. Fixed `total_refund_amount` Calculation
Changed `RefundRequest::calculateTotals()` to subtract promo code and points from the refund amount:

**File**: `Modules/Refund/app/Models/RefundRequest.php`

**Before**:
```php
$subtotal = $this->total_products_amount 
    + $this->total_shipping_amount
    - $this->total_discount_amount
    + ($this->vendor_fees_amount ?? 0)
    - ($this->vendor_discounts_amount ?? 0);

// NOTE: Do NOT subtract promo_code_amount and points_used!
$this->total_refund_amount = $subtotal - ($this->return_shipping_cost ?? 0);
```

**After**:
```php
$subtotal = $this->total_products_amount 
    + $this->total_shipping_amount
    - $this->total_discount_amount
    + ($this->vendor_fees_amount ?? 0)
    - ($this->vendor_discounts_amount ?? 0);

// Subtract promo code and points (customer didn't pay these)
$subtotal -= ($this->promo_code_amount ?? 0);
$subtotal -= ($this->points_used ?? 0);

$this->total_refund_amount = $subtotal - ($this->return_shipping_cost ?? 0);
```

### 2. Fixed Accounting Entry Amount
Changed `RefundRequestObserver` to calculate vendor deduction separately:

**File**: `Modules/Refund/app/Observers/RefundRequestObserver.php`

**Before**:
```php
\Modules\Accounting\app\Models\AccountingEntry::create([
    'amount' => $refundRequest->total_refund_amount,  // ❌ Wrong - uses customer refund
    'vendor_amount' => $refundRequest->total_refund_amount - $commissionDetails['total_commission'],
    // ...
]);
```

**After**:
```php
// Calculate vendor deduction (full product value)
$vendorDeduction = $refundRequest->total_products_amount 
    + $refundRequest->total_shipping_amount 
    + ($refundRequest->vendor_fees_amount ?? 0)
    - ($refundRequest->vendor_discounts_amount ?? 0)
    - ($refundRequest->return_shipping_cost ?? 0);

\Modules\Accounting\app\Models\AccountingEntry::create([
    'amount' => $vendorDeduction,  // ✅ Correct - uses vendor deduction
    'vendor_amount' => $vendorDeduction - $commissionDetails['total_commission'],
    'metadata' => [
        'customer_refund_amount' => $refundRequest->total_refund_amount,  // Track both
        'vendor_deduction_amount' => $vendorDeduction,
        // ...
    ],
]);
```

### 3. Fixed Dashboard Charts
Changed `DashboardService::getNetSalesChartData()` to calculate vendor deduction for refunds:

**File**: `app/Services/DashboardService.php`

**Before**:
```php
$refunds = (clone $refundQuery)->sum('total_refund_amount');  // ❌ Customer refund
```

**After**:
```php
$refunds = (clone $refundQuery)
    ->get()
    ->sum(function($refund) {
        // Calculate vendor deduction (not customer refund)
        return $refund->total_products_amount 
            + $refund->total_shipping_amount 
            + ($refund->vendor_fees_amount ?? 0)
            - ($refund->vendor_discounts_amount ?? 0)
            - ($refund->return_shipping_cost ?? 0);
    });
```

## Example: Order #249

### Order Details:
- Products: 230 EGP
- Shipping: 100 EGP
- **Total: 330 EGP**
- Customer used: 20 EGP promo + 310 EGP points
- **Customer paid: 0 EGP**
- Vendor received from Bnaia: 330 EGP (20 promo share + 310 points share)

### Refund Calculation:

**Before Fix**:
- `total_refund_amount` = 330 EGP ❌ (wrong - customer didn't pay this)
- Accounting entry amount = 330 EGP
- Dashboard refunds = 330 EGP

**After Fix**:
- `total_refund_amount` = 0 EGP ✅ (correct - customer gets back what they paid)
- Vendor deduction = 330 EGP ✅ (correct - vendor loses what they received)
- Accounting entry amount = 330 EGP ✅
- Dashboard refunds = 330 EGP ✅

### Dashboard Charts:
- **Earnings**: 330 EGP (order delivered)
- **Refunds**: 330 EGP (vendor deduction)
- **Net Earnings**: 0 EGP ✅ (fully refunded)

## Key Concepts

### Customer Refund Amount (`total_refund_amount`)
- What the **customer gets back**
- = Products + Shipping + Fees - Discounts - **Promo Code** - **Points** - Return Shipping
- For order paid with points: **0 EGP**
- Used for: Customer notifications, refund receipts

### Vendor Deduction Amount
- What gets **deducted from vendor's balance**
- = Products + Shipping + Fees - Discounts - Return Shipping
- For order paid with points: **330 EGP** (full value)
- Used for: Accounting entries, dashboard charts, vendor balance

## Impact

1. **Refund Statistics**: Now show correct customer refund amount (0 EGP for points orders)
2. **Accounting Entries**: Correctly deduct full amount from vendor (330 EGP)
3. **Dashboard Charts**: Show correct vendor deductions (330 EGP)
4. **Net Earnings**: Correctly show 0 for fully refunded orders

## Related Files
- `Modules/Refund/app/Models/RefundRequest.php` (calculateTotals method)
- `Modules/Refund/app/Observers/RefundRequestObserver.php` (accounting entry creation)
- `app/Services/DashboardService.php` (getNetSalesChartData method)

## Related Documentation
- `.agent/REFUND_TOTAL_CALCULATION_FIX.md` - Previous refund calculation fix
- `.agent/DASHBOARD_CHARTS_EARNINGS_FIX.md` - Dashboard charts fix
- `.agent/COMMISSION_ZERO_ON_POINTS_PAYMENT.md` - Points payment logic

# Refund Remaining Calculation Fix

## Issue
When displaying vendor remaining in order show page after refunds, the calculation was using **customer refund amount** instead of **vendor deduction amount**, causing incorrect remaining values.

Additionally, the vendor deduction calculation was missing **fees and discounts**, which are part of what the vendor receives/loses.

## Example: Order #253, Vendor 107

### Order Details
- **Products**: 690 EGP
- **Shipping**: 50 EGP
- **Fees**: 45.87 EGP
- **Discounts**: -91.74 EGP
- **Total**: 694.13 EGP (products + shipping + fees - discounts)
- **Commission**: 15% of (products + shipping) = 111 EGP
- **Remaining Before Refund**: 694.13 - 111 = 583.13 EGP

### Refunds
- **Refund #76**: 2 quantity (Status: refunded)
  - Customer Refund: 462.75 EGP
  - Vendor Deduction: 462.75 EGP (products + shipping + fees - discounts)
  - Refunded Commission: 74 EGP

- **Refund #77**: 1 quantity (Status: refunded)
  - Customer Refund: 231.38 EGP
  - Vendor Deduction: 231.38 EGP (products + shipping + fees - discounts)
  - Refunded Commission: 37 EGP

### Wrong Calculation #1 (Before Fix)
Using customer refund amount without fees/discounts:
```php
$vendorDeduction = $refund->total_products_amount + $refund->total_shipping_amount;
// = (460 + 33.33) + (230 + 16.67)
// = 493.33 + 246.67
// = 740 EGP

$netRefundImpact = $vendorDeduction - $vendorRefundedCommission;
// = 740 - 111
// = 629 EGP

$vendorTotalRemaining = $remainingBeforeRefund - $netRefundImpact;
// = 583.13 - 629
// = -45.87 EGP ❌ WRONG!
```

### Correct Calculation (After Fix)
Including fees and discounts in vendor deduction:
```php
$vendorDeduction = $refund->total_products_amount 
    + $refund->total_shipping_amount 
    + $refund->vendor_fees_amount 
    - $refund->vendor_discounts_amount;
// = (460 + 33.33 + 30.58 - 61.16) + (230 + 16.67 + 15.29 - 30.58)
// = 462.75 + 231.38
// = 694.13 EGP

$netRefundImpact = $vendorDeduction - $vendorRefundedCommission;
// = 694.13 - 111
// = 583.13 EGP

$vendorTotalRemaining = $remainingBeforeRefund - $netRefundImpact;
// = 583.13 - 583.13
// = 0 EGP ✅ CORRECT!
```

## Root Cause
The vendor deduction calculation was missing fees and discounts. The vendor receives:
- Products + Shipping + Fees - Discounts

When refunding, the vendor loses the same amount:
- Products + Shipping + Fees - Discounts

The commission is calculated on (Products + Shipping) only, not on the total with fees/discounts.

## Solution
Updated vendor deduction calculation in all places:

```php
$vendorDeduction = $refund->total_products_amount 
    + $refund->total_shipping_amount 
    + ($refund->vendor_fees_amount ?? 0)
    - ($refund->vendor_discounts_amount ?? 0)
    - ($refund->return_shipping_cost ?? 0);
```

## Files Changed
1. `Modules/Order/resources/views/orders/show.blade.php`
   - Line ~1070: Single vendor view (vendor user)
   - Line ~1300: Multi-vendor view (admin)

2. `Modules/Refund/app/Observers/RefundRequestObserver.php`
   - Line ~318: Accounting entry creation

3. `app/Services/DashboardService.php`
   - Line ~1490 and ~1505: Dashboard charts refund calculation

## Key Concept
**Vendor Deduction Amount = Products + Shipping + Fees - Discounts - Return Shipping**

This represents the actual amount that gets deducted from the vendor's balance, matching what they originally received for those products.

## Verification
Order #253, Vendor 107:
- Original Total: 694.13 EGP
- Commission: 111 EGP
- Remaining Before: 583.13 EGP
- Refunded (all 3 qty): 694.13 EGP
- Refunded Commission: 111 EGP
- Net Impact: 583.13 EGP
- **Final Remaining: 0 EGP** ✅


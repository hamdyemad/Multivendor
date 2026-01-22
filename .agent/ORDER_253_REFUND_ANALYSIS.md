# Order #253 Refund Analysis

## Order Details
- **Order Number**: ORD-000001
- **Customer ID**: 656
- **Total Price**: 4412.88 EGP (customer paid)
- **Promo Code**: 0 EGP
- **Points**: 0 EGP

## Order Products

### Vendor 199
- **Product #546**: 3707.88 EGP + 50 EGP shipping = 3757.88 EGP
- **Commission**: 15% = 563.68 EGP
- **Remaining**: 3757.88 - 563.68 = **3194.20 EGP**

### Vendor 38
- **Product #547**: 115 EGP + 100 EGP shipping = 215 EGP
- **Commission**: 20% = 43 EGP
- **Remaining**: 215 - 43 = **172 EGP**

### Vendor 107
- **Product #548**: 690 EGP + 50 EGP shipping = 740 EGP
- **Quantity**: 3
- **Commission**: 15% = 111 EGP
- **Remaining BEFORE refund**: 740 - 111 = **629 EGP**

## Refund Request #74

### Details
- **Refund Number**: REF-20260122-0001
- **Vendor**: 107
- **Status**: pending
- **Product**: #548 (2 out of 3 quantity)

### Refund Amounts
- **Total Products Amount**: 460 EGP (2/3 of 690)
- **Total Shipping Amount**: 33.33 EGP (2/3 of 50)
- **Vendor Fees**: 30.58 EGP
- **Vendor Discounts**: 61.16 EGP
- **Return Shipping**: 0 EGP

### Customer Refund Calculation
```
Customer Refund = Products + Shipping + Fees - Discounts - Return Shipping
                = 460 + 33.33 + 30.58 - 61.16 - 0
                = 462.75 EGP ✅
```

### Vendor Deduction Calculation
```
Vendor Deduction = Products + Shipping - Return Shipping
                 = 460 + 33.33 - 0
                 = 493.33 EGP ✅
```

**Note**: Fees and discounts are NOT included in vendor deduction because they're already reflected in the customer refund amount.

### Commission Calculation
```
Refunded Commission = (Products + Shipping) × Commission%
                    = 493.33 × 15%
                    = 74.00 EGP
```

## Vendor 107 Remaining AFTER Refund

### Calculation
```
Original Total: 740 EGP
- Original Commission: 111 EGP
= Remaining Before Refund: 629 EGP

- Vendor Deduction: 493.33 EGP
+ Refunded Commission: 74.00 EGP
= Final Remaining: 209.67 EGP ✅
```

### Breakdown
- **Remaining from non-refunded product** (1/3 of original):
  - Product: 230 EGP (1/3 of 690)
  - Shipping: 16.67 EGP (1/3 of 50)
  - Total: 246.67 EGP
  - Commission: 37 EGP (15% of 246.67)
  - Remaining: 246.67 - 37 = **209.67 EGP** ✅

## Summary

### Before Refund
| Vendor | Total | Commission | Remaining |
|--------|-------|------------|-----------|
| 199 | 3757.88 | 563.68 | 3194.20 |
| 38 | 215.00 | 43.00 | 172.00 |
| 107 | 740.00 | 111.00 | **629.00** |

### After Refund (when status = 'refunded')
| Vendor | Total | Commission | Refunded | Remaining |
|--------|-------|------------|----------|-----------|
| 199 | 3757.88 | 563.68 | 0 | 3194.20 |
| 38 | 215.00 | 43.00 | 0 | 172.00 |
| 107 | 740.00 | 111.00 | 493.33 | **209.67** |

## Key Points

1. **Customer Refund** (462.75 EGP) includes fees and discounts
2. **Vendor Deduction** (493.33 EGP) is products + shipping only
3. **Refunded Commission** (74 EGP) is returned to vendor
4. **Net Impact** on vendor: 493.33 - 74 = 419.33 EGP loss
5. **Remaining** correctly shows 209.67 EGP (1/3 of original order)

## Fixes Applied

### 1. Vendor Deduction Calculation
**File**: `Modules/Refund/app/Observers/RefundRequestObserver.php`

**Before**:
```php
$vendorDeduction = $refundRequest->total_products_amount 
    + $refundRequest->total_shipping_amount 
    + ($refundRequest->vendor_fees_amount ?? 0)
    - ($refundRequest->vendor_discounts_amount ?? 0)
    - ($refundRequest->return_shipping_cost ?? 0);
```

**After**:
```php
$vendorDeduction = $refundRequest->total_products_amount 
    + $refundRequest->total_shipping_amount 
    - ($refundRequest->return_shipping_cost ?? 0);
```

### 2. Dashboard Charts
**File**: `app/Services/DashboardService.php`

Same fix applied to `getNetSalesChartData()` method.

## Verification

All calculations are now correct:
- ✅ Customer refund: 462.75 EGP
- ✅ Vendor deduction: 493.33 EGP
- ✅ Refunded commission: 74 EGP
- ✅ Final remaining: 209.67 EGP

The refund is currently **pending**. When approved and status changes to **refunded**, the remaining will automatically update to 209.67 EGP.

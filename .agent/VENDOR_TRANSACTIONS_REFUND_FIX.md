# Vendor Transactions Refund Calculation Fix

## Issue
Vendor money transactions were not accounting for refunds from the `refund_requests` table. The "Total Transactions" and "Total Remaining" were showing incorrect values because they didn't subtract completed refunds.

## Root Cause
The `getOrdersPriceAttribute()`, `getBnaiaCommissionAttribute()`, and `getVendorsStatistics()` methods in the Vendor model were trying to calculate refunds by:
1. Joining with refund_request_items to get refunded quantities
2. Calculating remaining quantities per product
3. Manually calculating proportional amounts

This approach was:
- Complex and error-prone
- Not using the already-calculated `total_refund_amount` from refund_requests
- Not properly accounting for all refund components (fees, discounts, promo codes, points, shipping)

## Solution

### 1. Simplified Orders Price Calculation (Single Vendor)
**File:** `Modules/Vendor/app/Models/Vendor.php`
**Method:** `getOrdersPriceAttribute()`

**New Approach:**
1. Calculate total from all delivered orders (products + shipping + fees - discounts - promo - points)
2. Subtract the SUM of `total_refund_amount` from completed refunds for this vendor
3. The `total_refund_amount` already includes all calculations

**Formula:**
```
Total Transactions = (Products + Shipping + Fees - Discounts - Promo - Points) - Total Refunded
```

### 2. Simplified Commission Calculation (Single Vendor)
**File:** `Modules/Vendor/app/Models/Vendor.php`
**Method:** `getBnaiaCommissionAttribute()`

**New Approach:**
1. Calculate commission from all delivered orders
2. Subtract the SUM of `commission_amount` from accounting_entries where type = 'refund' for this vendor
3. The accounting entries already have the correct commission reversal amounts

**Formula:**
```
Bnaia Commission = Total Commission from Orders - Commission Returned from Refunds
```

### 3. Simplified All Vendors Statistics (Admin Dashboard)
**File:** `Modules/Vendor/app/Models/Vendor.php`
**Method:** `getVendorsStatistics()`

**New Approach:**
1. Calculate total from all delivered orders across all vendors
2. Subtract the SUM of `total_refund_amount` from all completed refunds
3. Calculate commission from all delivered orders
4. Subtract the SUM of `commission_amount` from all refund accounting entries
5. Filter by country_id if provided

**Formula:**
```
Total Transactions (All Vendors) = Sum of All Orders - Sum of All Refunds
Total Commission (All Vendors) = Sum of All Commission - Sum of Refunded Commission
```

## Benefits

1. **Accuracy**: Uses the already-calculated `total_refund_amount` which includes all components
2. **Simplicity**: No complex quantity-based calculations
3. **Maintainability**: Single source of truth for refund amounts
4. **Consistency**: Uses the same refund amounts shown in refund requests
5. **Performance**: Simpler queries, no complex joins with subqueries
6. **Works for Both**: Same logic for single vendor and all vendors statistics

## Data Flow

### Orders Price (Total Transactions)
```
Delivered Orders Total → Subtract Refund Requests (total_refund_amount) → Final Total
```

### Commission
```
Orders Commission → Subtract Accounting Entries (commission_amount for refunds) → Final Commission
```

### Total Balance
```
Total Transactions - Bnaia Commission = Total Balance
```

### Total Remaining
```
Total Balance - Total Sent (withdrawals) = Total Remaining
```

## Testing

✓ Vendor transactions show correct total after refunds (single vendor)
✓ Admin dashboard shows correct totals for all vendors
✓ Commission is reduced by refunded commission
✓ Total remaining reflects actual amount owed to vendor(s)
✓ Calculations match refund_requests.total_refund_amount
✓ Calculations match accounting_entries.commission_amount
✓ Country filtering works correctly in admin dashboard

## Related Files

- `Modules/Vendor/app/Models/Vendor.php` - Vendor transaction calculations
- `Modules/Refund/app/Models/RefundRequest.php` - Refund amount calculations
- `Modules/Accounting/app/Models/AccountingEntry.php` - Commission tracking
- `Modules/Refund/app/Observers/RefundRequestObserver.php` - Creates accounting entries
- `resources/views/pages/dashboard/withdraw-transactions.blade.php` - Dashboard view

## Example

**Before Refund:**
- Total Transactions: 1000 EGP
- Commission (20%): 200 EGP
- Total Balance: 800 EGP
- Total Sent: 0 EGP
- Total Remaining: 800 EGP

**After Refund (230 EGP, 20% commission = 43 EGP):**
- Total Transactions: 1000 - 230 = 770 EGP
- Commission: 200 - 43 = 157 EGP
- Total Balance: 770 - 157 = 613 EGP
- Total Sent: 0 EGP
- Total Remaining: 613 EGP

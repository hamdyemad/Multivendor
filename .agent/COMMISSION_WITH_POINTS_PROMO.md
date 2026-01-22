# Commission Calculation with Points/Promo Code

## Business Logic

### Payment Flow
When customer uses points or promo code:
1. **Customer**: Pays little or nothing (points/promo cover the cost)
2. **Vendor**: Receives full amount from **Bnaia** (not from customer)
3. **Bnaia**: Pays vendor and takes commission

### Example: Order with Points
**Order Details:**
- Products + Shipping: 740 EGP
- Customer Paid: 0 EGP (used points)
- Promo Code Share: 67.39 EGP
- Points Share: 653.18 EGP

**Money Flow:**
```
Customer → Pays 0 EGP
Bnaia → Pays 740 EGP to Vendor (promo + points shares)
Bnaia → Takes 111 EGP commission (15% of 740)
Vendor → Receives 629 EGP net (740 - 111)
```

## Why Commission Must Be Calculated

### Reason 1: Bnaia Pays the Vendor
- Even though customer paid with points, vendor still receives money
- The money comes from Bnaia, not customer
- Bnaia needs to take commission on this transaction

### Reason 2: Refund Scenario
When refund happens:
- Vendor returns money to Bnaia (not customer)
- Commission should be returned to vendor
- If commission was 0%, vendor won't get commission back

**Example Refund:**
```
Original:
  Vendor received: 740 EGP from Bnaia
  Commission paid: 111 EGP to Bnaia
  Net: 629 EGP

After Refund:
  Vendor returns: 740 EGP to Bnaia
  Commission returned: 111 EGP from Bnaia
  Net impact: 629 EGP (vendor loses what they gained)
```

If commission was 0%:
```
Original:
  Vendor received: 740 EGP from Bnaia
  Commission paid: 0 EGP
  Net: 740 EGP

After Refund:
  Vendor returns: 740 EGP to Bnaia
  Commission returned: 0 EGP
  Net impact: 740 EGP (vendor loses MORE than they gained!)
```

## The Problem

### Old Logic (WRONG)
The `AdjustCommissionForPoints` pipeline was setting commission to 0% when points were used:

```php
// In AdjustCommissionForPoints.php
if ($pointsCost > 0) {
    // Set commission to 0 for all products
    $product['commission'] = 0;
}
```

**Why This Was Wrong:**
- Commission = 0% means Bnaia doesn't take commission
- But Bnaia IS paying the vendor, so should take commission
- On refund, vendor loses more than they gained

### New Logic (CORRECT)
Commission is calculated normally regardless of payment method:

```php
// Commission calculated from department/activity
$commission = $department->commission; // e.g., 15%

// Stored in order_products table
$orderProduct->commission = $commission;

// Used for:
// 1. Calculating vendor net amount
// 2. Calculating refund commission
```

## Implementation

### Change Made
Removed `AdjustCommissionForPoints` pipeline from order creation:

**File**: `Modules/Order/app/Services/Api/OrderApiService.php`

**Before:**
```php
->through([
    // ...
    CalculatePointsUsagePipeline::class,
    AdjustCommissionForPoints::class, // ❌ Sets commission to 0
    CalculateFinalTotal::class,
    // ...
])
```

**After:**
```php
->through([
    // ...
    CalculatePointsUsagePipeline::class,
    // AdjustCommissionForPoints::class, // ✅ REMOVED
    CalculateFinalTotal::class,
    // ...
])
```

## Verification

### Test Scenario
1. Create order with points/promo code
2. Check commission in `order_products` table
3. Expected: Commission = department commission (e.g., 15%)
4. NOT: Commission = 0%

### Order #254 Example
**Before Fix:**
- Product #549 (Vendor #107): Commission = 0% ❌
- Product #551 (Vendor #199): Commission = 0% ❌

**After Fix (New Orders):**
- Commission should be 15% (from department) ✅
- Refunds will calculate commission correctly ✅

## Impact on Existing Orders

### Orders Created Before Fix
- Commission = 0% in database
- Cannot be changed retroactively
- Refunds will have 0% commission (vendor loses more)

### Orders Created After Fix
- Commission = normal percentage (e.g., 15%)
- Refunds will calculate commission correctly
- Vendor gets fair treatment

## Related Files
- `Modules/Order/app/Pipelines/AdjustCommissionForPoints.php` (now unused)
- `Modules/Order/app/Services/Api/OrderApiService.php` (pipeline removed)
- `Modules/Refund/app/Observers/RefundRequestObserver.php` (uses commission for refunds)

## Key Takeaway
**Commission must ALWAYS be calculated, regardless of payment method, because Bnaia pays the vendor and takes commission.**

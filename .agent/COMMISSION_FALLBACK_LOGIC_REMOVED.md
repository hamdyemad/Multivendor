# Commission Fallback Logic Removed

## Issue
When orders were created with points, the commission was correctly stored as 0 in the `order_products` table. However, multiple places in the codebase had fallback logic that said "if commission is 0, use the department commission instead". This caused:

1. Order detail page showing incorrect commission (49.50 EGP instead of 0)
2. Vendor dashboard showing incorrect remaining balance (-49.50 EGP instead of 330 EGP)

## Root Cause
The fallback logic was implemented in multiple places:
```php
// WRONG: Falls back to department commission when 0
$commission = $product->commission > 0 
    ? $product->commission 
    : $department->commission;
```

This logic was originally intended to handle old orders that didn't have commission stored, but it incorrectly treated 0 as "not set" when 0 is actually a valid value (no commission).

## Solution
Changed all occurrences to use commission directly without fallback:
```php
// CORRECT: Uses commission as-is, 0 means no commission
$commission = $product->commission ?? 0;
```

## Files Fixed

### 1. Order Display View
**File**: `Modules/Order/resources/views/orders/show.blade.php`

**Fixed 3 locations**:
- Line ~785: Main order summary calculation
- Line ~1040: Vendor user view calculation  
- Line ~1285: Multi-vendor breakdown calculation

**Before**:
```php
$commPercent = $prod->commission > 0
    ? $prod->commission
    : $prod->vendorProduct?->product?->department?->commission ?? 0;
```

**After**:
```php
$commPercent = $prod->commission ?? 0;
```

### 2. Vendor Model - Single Vendor Commission
**File**: `Modules/Vendor/app/Models/Vendor.php`

**Method**: `getBnaiaCommissionAttribute()` (Line ~226)

**Before**:
```php
$commissionPercent = $product->product_commission > 0 
    ? $product->product_commission 
    : ($product->department_commission ?? 0);
```

**After**:
```php
$commissionPercent = $product->product_commission ?? 0;
```

### 3. Vendor Model - All Vendors Statistics
**File**: `Modules/Vendor/app/Models/Vendor.php`

**Method**: `getVendorsStatistics()` (Line ~505)

**Before**:
```sql
SUM(
    (op.price + op.shipping_cost) * 
    COALESCE(
        CASE WHEN op.commission > 0 THEN op.commission ELSE d.commission END,
        0
    ) / 100
) as total_commission
```

**After**:
```sql
SUM(
    (op.price + op.shipping_cost) * 
    COALESCE(op.commission, 0) / 100
) as total_commission
```

## Impact

### Before Fix
**Order with points (commission = 0 in database)**:
- Order detail page: Shows "Bnaia Commission: 15% (49.50 EGP)" ❌
- Vendor dashboard: Shows "Total Remaining: -49.50 EGP" ❌
- Vendor statistics: Shows "Bnaia Commission: 49.50 EGP" ❌

### After Fix
**Order with points (commission = 0 in database)**:
- Order detail page: Shows "Bnaia Commission: 0% (0.00 EGP)" ✅
- Vendor dashboard: Shows "Total Remaining: 330.00 EGP" ✅
- Vendor statistics: Shows "Bnaia Commission: 0.00 EGP" ✅

## Testing Checklist

- [x] Order detail page shows 0 commission for orders with points
- [x] Vendor dashboard shows correct remaining balance
- [x] Admin dashboard vendor statistics show correct commission
- [ ] Old orders (without commission stored) still work correctly
- [ ] Orders without points still show correct commission

## Notes

1. **Database is correct**: The commission was always stored correctly as 0 in `order_products` table
2. **Display was wrong**: The views and calculations were incorrectly falling back to department commission
3. **Consistent behavior**: Now all places use commission directly from `order_products` table
4. **0 is valid**: Commission = 0 is a valid value meaning "no commission", not "commission not set"

## Related Changes
- `Modules/Order/app/Pipelines/AdjustCommissionForPoints.php` - Sets commission to 0 when points used
- `Modules/Order/app/Services/Api/OrderApiService.php` - Added pipeline to order flow

## Files Modified
1. `Modules/Order/resources/views/orders/show.blade.php` (3 locations)
2. `Modules/Vendor/app/Models/Vendor.php` (2 methods)

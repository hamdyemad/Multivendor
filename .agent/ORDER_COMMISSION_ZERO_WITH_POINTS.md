# Order Commission: Set to 0 When Points Are Used

## Summary
When a customer creates an order and uses points (any amount), the commission stored in `order_products` table should be set to 0 for all products in that order.

## Issue Found
The commission was correctly stored as 0 in the database, but the order display views were using fallback logic that retrieved commission from the department when `commission = 0`. This caused the display to show commission even when it should be 0.

## Business Logic
- **Without Points**: Commission is calculated normally (e.g., 15% of product price)
- **With Points**: Commission = 0 (regardless of how many points are used)

This means if a customer uses even 1 point, the entire order has 0 commission.

## Implementation

### 1. New Pipeline Created
**File**: `Modules/Order/app/Pipelines/AdjustCommissionForPoints.php`

This pipeline:
- Runs after `CalculatePointsUsagePipeline` (which calculates points usage)
- Checks if `points_cost > 0` (meaning points were used)
- If yes, sets `commission = 0` for all products in `products_data`
- If no, leaves commission unchanged

```php
public function handle($payload, Closure $next)
{
    $context = $payload['context'];
    
    // Check if points were used
    $pointsCost = $context['points_cost'] ?? 0;
    
    if ($pointsCost <= 0) {
        // No points used, no adjustment needed
        return $next($payload);
    }
    
    // Set commission to 0 for all products when points are used
    $productsData = $context['products_data'] ?? [];
    foreach ($productsData as &$product) {
        $product['commission'] = 0;
    }
    
    $context['products_data'] = $productsData;
    $payload['context'] = $context;
    
    return $next($payload);
}
```

### 2. Pipeline Registration
**File**: `Modules/Order/app/Services/Api/OrderApiService.php`

Added the new pipeline to the order creation flow:
```php
->through([
    FetchCartItems::class,
    ValidatePromoCode::class,
    ValidateProducts::class,
    FetchUserData::class,
    CalculateApiProductPrices::class,
    CalculateShipping::class,
    CalculateExtras::class,
    ValidateDiscountAgainstRemaining::class,
    CalculatePointsUsagePipeline::class,      // Calculates points usage
    AdjustCommissionForPoints::class,          // NEW: Sets commission to 0 if points used
    CalculateFinalTotal::class,
    CreateOrder::class,
    SyncOrderProducts::class,
    UpdateProductSales::class,
])
```

### 3. Fixed Display Logic
**File**: `Modules/Order/resources/views/orders/show.blade.php`

**Problem**: The view had fallback logic that retrieved commission from department when `commission = 0`:
```php
// OLD CODE (WRONG)
$commPercent = $prod->commission > 0
    ? $prod->commission
    : $prod->vendorProduct?->product?->department?->commission ?? 0;
```

**Solution**: Use commission directly from order_products without fallback:
```php
// NEW CODE (CORRECT)
$commPercent = $prod->commission ?? 0;
```

Fixed in 3 locations in the file:
- Line ~785: Main order summary calculation
- Line ~1040: Vendor user view calculation
- Line ~1285: Multi-vendor breakdown calculation

## Pipeline Order (Important)
1. `CalculateProductPrices` - Calculates commission normally (e.g., 15%)
2. `CalculatePointsUsagePipeline` - Calculates points usage and sets `points_cost`
3. **`AdjustCommissionForPoints`** - Sets commission to 0 if points were used
4. `CreateOrder` - Creates order with adjusted commission
5. `SyncOrderProducts` - Stores products with commission = 0

## Database Impact
**Table**: `order_products`
**Field**: `commission` (decimal)

- **Before**: Stores commission percentage (e.g., 15.00)
- **After (with points)**: Stores 0.00

## Example Scenarios

### Scenario 1: Order without points
- Product price: 100 EGP
- Commission rate: 15%
- **Stored commission**: 15.00
- **Displayed commission**: 15.00% (15 EGP)
- Platform earns: 15 EGP

### Scenario 2: Order with points (partial payment)
- Product price: 100 EGP
- Customer pays: 70 EGP cash + 30 EGP points
- Commission rate: 15%
- **Stored commission**: 0.00
- **Displayed commission**: 0.00% (0 EGP)
- Platform earns: 0 EGP

### Scenario 3: Order with points (full payment)
- Product price: 100 EGP
- Customer pays: 100 EGP points
- Commission rate: 15%
- **Stored commission**: 0.00
- **Displayed commission**: 0.00% (0 EGP)
- Platform earns: 0 EGP

## Testing Recommendations

1. **Test Case 1**: Create order without using points
   - Expected: Commission stored normally (e.g., 15%)
   - Expected: Display shows commission (e.g., 15%)

2. **Test Case 2**: Create order using some points
   - Expected: Commission = 0 for all products in database
   - Expected: Display shows "Bnaia Commission: 0.00%"

3. **Test Case 3**: Create order paying fully with points
   - Expected: Commission = 0 for all products in database
   - Expected: Display shows "Bnaia Commission: 0.00%"

4. **Test Case 4**: Check vendor earnings calculation
   - Expected: Vendor gets full product price when commission = 0

## Files Modified
1. `Modules/Order/app/Pipelines/AdjustCommissionForPoints.php` (Created)
2. `Modules/Order/app/Services/Api/OrderApiService.php` (Updated - added pipeline)
3. `Modules/Order/resources/views/orders/show.blade.php` (Fixed - removed fallback logic in 3 places)

## Root Cause
The issue was NOT in the database storage (commission was correctly stored as 0), but in the display logic that was falling back to department commission when it found 0 in the order_products table. This made it appear as if commission was being charged even though it wasn't stored in the database.

## Notes
- This only affects API orders (customer orders via mobile/web app)
- Admin-created orders through web panel are not affected (they don't use the API service)
- The commission field stores the percentage, not the amount
- When commission = 0, the platform doesn't earn anything from that order
- Vendor receives the full product price (minus any discounts/points)
- The display now correctly shows 0% commission when points are used

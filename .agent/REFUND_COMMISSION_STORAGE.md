# Refund Commission Storage in refund_request_items Table

## Summary
Added `commission` column to `refund_request_items` table to store the commission percentage at the time of refund creation, just like we do in `order_products` table. This ensures the commission is captured as a snapshot and doesn't change if department commission rates are updated later.

## Changes Made

### 1. Database Migration
**File**: `Modules/Refund/database/migrations/2026_01_23_000002_add_commission_to_refund_request_items.php`

Added `commission` column:
```php
$table->decimal('commission', 5, 2)->default(0)->after('refund_amount')
    ->comment('Commission percentage from order_products at time of refund');
```

### 2. Model Update
**File**: `Modules/Refund/app/Models/RefundRequestItem.php`

Added to fillable and casts:
```php
protected $fillable = [
    // ... existing fields
    'commission',
];

protected $casts = [
    // ... existing casts
    'commission' => 'decimal:2',
];
```

### 3. Repository Update
**File**: `Modules/Refund/app/Repositories/RefundRequestRepository.php`

Updated `createRefundWithVendorSplit()` to store commission:
```php
// Get commission from order product (already stored there)
$commission = $orderProduct->commission ?? 0;

\Modules\Refund\app\Models\RefundRequestItem::create([
    // ... existing fields
    'commission' => $commission, // Store commission percentage from order_products
]);
```

### 4. Observer Update
**File**: `Modules/Refund/app/Observers/RefundRequestObserver.php`

Updated `calculateCommissionReversal()` to use stored commission:
```php
// Get commission percentage from refund_request_items (already stored there from order_products)
$commissionPercent = $item->commission ?? 0;
```

## Data Flow

### Order Creation
1. Customer creates order with points
2. `AdjustCommissionForPoints` pipeline sets commission = 0 in products_data
3. Commission = 0 is stored in `order_products` table

### Refund Creation
1. Customer creates refund request
2. System reads commission from `order_products` table
3. Commission is copied to `refund_request_items` table
4. Commission is now stored as a snapshot

### Refund Completion
1. Admin completes refund
2. Observer reads commission from `refund_request_items` table
3. Calculates commission reversal using stored commission
4. Creates accounting entry with commission = 0

## Benefits

1. **Consistency**: Same pattern as `order_products` - commission is stored, not calculated
2. **Accuracy**: Commission reflects the rate at the time of refund, not current department rate
3. **No Fallback Logic**: No need to check department commission if refund item commission is 0
4. **Historical Record**: Can see what commission was applied to each refund item
5. **Performance**: No need to join with order_products or departments to get commission

## Example Scenarios

### Scenario 1: Order with Points (Commission = 0%)
- Order created with points → `order_products.commission = 0`
- Refund created → `refund_request_items.commission = 0`
- Refund completed → Accounting entry commission = 0
- **Result**: No commission charged ✅

### Scenario 2: Normal Order (Commission = 15%)
- Order created normally → `order_products.commission = 15`
- Refund created → `refund_request_items.commission = 15`
- Refund completed → Accounting entry commission = 15% of refund amount
- **Result**: Commission reversed correctly ✅

### Scenario 3: Department Rate Changes
- Order created with 15% commission → `order_products.commission = 15`
- Department rate changed to 20%
- Refund created → `refund_request_items.commission = 15` (from order_products, not department)
- **Result**: Uses original commission rate ✅

## Migration Status
```
2026_01_23_000002_add_commission_to_refund_request_items ............... DONE
```

## Files Modified
1. `Modules/Refund/database/migrations/2026_01_23_000002_add_commission_to_refund_request_items.php` (Created)
2. `Modules/Refund/app/Models/RefundRequestItem.php` (Updated)
3. `Modules/Refund/app/Repositories/RefundRequestRepository.php` (Updated)
4. `Modules/Refund/app/Observers/RefundRequestObserver.php` (Updated)

## Testing
- ✅ Existing refund items updated with commission from order_products
- ✅ New refunds will store commission automatically
- ✅ Commission = 0 for orders with points
- ✅ Commission = department rate for normal orders
- ✅ No fallback logic anywhere in the system

## Notes
- The commission is stored as a percentage (e.g., 15.00 for 15%)
- The commission amount is calculated when needed: `(refund_amount × commission) / 100`
- This matches the pattern used in `order_products` table
- All existing refund items have been updated with the correct commission


---

## UPDATE: Fixed Display to Use Stored Commission

### Problem
After storing commission in `refund_request_items` table, the display views were still calculating commission from `order_products` table or falling back to department commission. This caused incorrect values to be shown (e.g., 49.50 EGP instead of 0.00 EGP for orders paid with points).

### Solution
Updated all views to use the stored commission value from `refund_request_items` table instead of calculating it.

### Files Modified

#### 1. Modules/Order/resources/views/components/refunded-products.blade.php
**Changes:**
- Line ~41: Changed vendor totals commission calculation to use `$item->commission ?? 0`
- Line ~260: Changed item display commission calculation to use `$refundItem->commission ?? 0`
- Removed fallback logic to `order_products.commission` and department commission
- Removed unnecessary `orderProduct` relationship loading

**Before:**
```php
$commPercent = $orderProduct->commission > 0 
    ? $orderProduct->commission 
    : ($orderProduct->vendorProduct?->product?->department?->commission ?? 0);
```

**After:**
```php
// Use stored commission percentage from refund_request_items table
$commissionPercent = $refundItem->commission ?? 0;
```

#### 2. Modules/Order/resources/views/components/vendor-remaining-with-products.blade.php
**Changes:**
- Line ~78: Changed to use stored commission from refund items

**Before:**
```php
// Use the same commission percentage as the original product
if ($itemRefundAmount > 0 && $productCommissionPercent > 0) {
    $productRefundedCommission += ($itemRefundAmount * $productCommissionPercent) / 100;
}
```

**After:**
```php
// Use stored commission percentage from refund_request_items table
$itemCommissionPercent = $refundItem->commission ?? 0;
if ($itemRefundAmount > 0 && $itemCommissionPercent > 0) {
    $productRefundedCommission += ($itemRefundAmount * $itemCommissionPercent) / 100;
}
```

#### 3. Modules/Order/resources/views/orders/show.blade.php
**Changes:**
- Line ~822: Simplified refund commission calculation to use stored commission
- Changed `with('items.orderProduct')` to `with('items')` - no longer need orderProduct relationship

**Before:**
```php
$refundedItems = $order->refunds()->where('status', 'refunded')->with('items.orderProduct')->get();
foreach ($refundedItems as $refund) {
    foreach ($refund->items as $item) {
        $orderProduct = $item->orderProduct;
        if ($orderProduct) {
            $commPercent = $orderProduct->commission > 0 
                ? $orderProduct->commission 
                : ($orderProduct->vendorProduct?->product?->department?->commission ?? 0);
            $itemRefundAmount = $item->total_price + $item->shipping_amount;
            $refundedCommission += ($itemRefundAmount * $commPercent) / 100;
        }
    }
}
```

**After:**
```php
$refundedItems = $order->refunds()->where('status', 'refunded')->with('items')->get();
foreach ($refundedItems as $refund) {
    foreach ($refund->items as $item) {
        $itemRefundAmount = $item->total_price + $item->shipping_amount;
        // Use stored commission percentage from refund_request_items table
        $commPercent = $item->commission ?? 0;
        $refundedCommission += ($itemRefundAmount * $commPercent) / 100;
    }
}
```

### Benefits of Display Fix

1. **Accurate Display**: Shows the actual commission stored at refund creation time
2. **Performance**: Reduced database queries by not loading `orderProduct` relationships
3. **Consistency**: All parts of the system now use the stored commission value
4. **Correct Zero Commission**: Orders paid with points now correctly show 0% commission in refund displays

### Testing
Test with order #249 (paid with points):
- Order products have commission = 0%
- Refund items have commission = 0%
- Display now shows "REFUND COMMISSION: 0.00 EGP" ✅ (was showing 49.50 EGP ❌)

### Complete Implementation Status
- ✅ Database migration created
- ✅ Model updated with fillable and casts
- ✅ Repository stores commission on refund creation
- ✅ Observer uses stored commission for calculations
- ✅ Display views use stored commission (NEW)
- ✅ Existing data backfilled with script
- ✅ No more fallback logic anywhere

## Final Notes
The commission storage and display is now complete. All parts of the system (creation, calculation, and display) use the stored commission value from `refund_request_items` table. This ensures consistency and accuracy throughout the refund lifecycle.

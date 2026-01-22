# Refund Commission - Read Directly from Order Products

## Overview
Removed the `commission` column from `refund_request_items` table. Commission is now always read directly from `order_products` table via the relationship. This eliminates data duplication and ensures commission is always accurate.

## Rationale
- Commission is already stored as a snapshot in `order_products` table at order creation time
- No need to duplicate this data in `refund_request_items` table
- Reading from `order_products` ensures consistency and reduces storage
- The `orderProduct` relationship is already loaded in most queries

## Changes Made

### 1. Database Migration
**File**: `Modules/Refund/database/migrations/2026_01_23_000003_remove_commission_from_refund_request_items.php`

Dropped the `commission` column from `refund_request_items` table:
```php
Schema::table('refund_request_items', function (Blueprint $table) {
    $table->dropColumn('commission');
});
```

**Migration Status**: ✅ DONE

### 2. Model Update
**File**: `Modules/Refund/app/Models/RefundRequestItem.php`

Removed `commission` from:
- `$fillable` array
- `$casts` array

### 3. Repository Update
**File**: `Modules/Refund/app/Repositories/RefundRequestRepository.php`

Removed commission storage when creating refund items:
```php
// REMOVED: 'commission' => $commission,
```

### 4. Observer Update
**File**: `Modules/Refund/app/Observers/RefundRequestObserver.php`

Updated `calculateCommissionReversal()` to read from order_products:
```php
// Get commission percentage directly from order_products table
$commissionPercent = $orderProduct->commission ?? 0;
```

### 5. View Updates

#### Modules/Order/resources/views/orders/show.blade.php
```php
$refundedItems = $order->refunds()->where('status', 'refunded')->with('items.orderProduct')->get();
foreach ($refundedItems as $refund) {
    foreach ($refund->items as $item) {
        $orderProduct = $item->orderProduct;
        if ($orderProduct) {
            $commPercent = $orderProduct->commission ?? 0;
            // ...
        }
    }
}
```

#### Modules/Order/resources/views/components/refunded-products.blade.php
```php
// In vendor totals calculation
$orderProduct = $item->orderProduct;
if ($orderProduct) {
    $commPercent = $orderProduct->commission ?? 0;
    // ...
}

// In item display
$commissionPercent = $orderProduct->commission ?? 0;
```

#### Modules/Order/resources/views/components/vendor-remaining-with-products.blade.php
```php
$orderProduct = $refundItem->orderProduct;
$itemCommissionPercent = $orderProduct ? ($orderProduct->commission ?? 0) : 0;
```

## Data Flow

### Order Creation
1. Customer creates order (with or without points)
2. Commission is calculated and stored in `order_products` table
3. If points used → commission = 0%
4. If normal order → commission = department rate (e.g., 15%)

### Refund Creation
1. Customer creates refund request
2. Refund items are created with reference to `order_product_id`
3. **No commission is stored** in `refund_request_items`

### Refund Display
1. Views load refund items with `orderProduct` relationship
2. Commission is read from `$item->orderProduct->commission`
3. Display shows correct commission (0% for orders with points, department rate for normal orders)

### Refund Completion
1. Observer reads commission from `$item->orderProduct->commission`
2. Creates accounting entry with correct commission
3. Dashboard shows commission from `accounting_entries` table

## Benefits

1. **No Data Duplication**: Commission is stored once in `order_products`, not duplicated in `refund_request_items`
2. **Single Source of Truth**: `order_products.commission` is the only place commission is stored
3. **Consistency**: All parts of the system read from the same source
4. **Reduced Storage**: One less column in `refund_request_items` table
5. **Simpler Logic**: No need to copy commission when creating refunds

## Example Scenarios

### Scenario 1: Order with Points (Commission = 0%)
- Order created with points → `order_products.commission = 0`
- Refund created → No commission stored in `refund_request_items`
- Refund display → Reads `$item->orderProduct->commission = 0`
- Accounting entry → `commission_amount = 0`
- Dashboard → Shows "Bnaia Commission: 0.00 EGP" ✅

### Scenario 2: Normal Order (Commission = 15%)
- Order created normally → `order_products.commission = 15`
- Refund created → No commission stored in `refund_request_items`
- Refund display → Reads `$item->orderProduct->commission = 15`
- Accounting entry → `commission_amount = 15% of refund amount`
- Dashboard → Shows correct commission ✅

## Files Modified
1. `Modules/Refund/database/migrations/2026_01_23_000003_remove_commission_from_refund_request_items.php` (Created)
2. `Modules/Refund/app/Models/RefundRequestItem.php` (Updated)
3. `Modules/Refund/app/Repositories/RefundRequestRepository.php` (Updated)
4. `Modules/Refund/app/Observers/RefundRequestObserver.php` (Updated)
5. `Modules/Order/resources/views/orders/show.blade.php` (Updated)
6. `Modules/Order/resources/views/components/refunded-products.blade.php` (Updated)
7. `Modules/Order/resources/views/components/vendor-remaining-with-products.blade.php` (Updated)

## Testing
To verify the fix:
1. Create a refund for order #249 (paid with points)
2. Complete the refund
3. Check dashboard → "Bnaia Commission From Transactions" should show 0.00 EGP (not -49.50 EGP)
4. Check accounting entries → `commission_amount` should be 0

## Related Documentation
- See `.agent/ORDER_COMMISSION_ZERO_WITH_POINTS.md` for commission = 0 implementation
- See `.agent/COMMISSION_FALLBACK_LOGIC_REMOVED.md` for fallback logic removal
- See `.agent/REFUND_COMMISSION_STORAGE.md` for previous approach (now superseded)

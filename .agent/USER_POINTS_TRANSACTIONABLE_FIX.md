# User Points Transaction: Transactionable Fields Fix

## Issue
When adjusting customer points manually through the admin panel, the system was throwing an error:
```
SQLSTATE[HY000]: General error: 1364 Field 'transactionable_id' doesn't have a default value
```

## Root Cause
The `user_points_transactions` table has polymorphic relationship fields (`transactionable_id` and `transactionable_type`) that were defined as NOT NULL. When manually adjusting points (not related to an order or refund), these fields should be nullable.

## Solution Implemented

### 1. Database Migration
**File**: `Modules/SystemSetting/database/migrations/2026_01_23_000001_make_transactionable_nullable_in_user_points_transactions.php`

Made the polymorphic fields nullable:
```php
Schema::table('user_points_transactions', function (Blueprint $table) {
    $table->unsignedBigInteger('transactionable_id')->nullable()->change();
    $table->string('transactionable_type')->nullable()->change();
});
```

### 2. Controller Update
**File**: `Modules/SystemSetting/app/Http/Controllers/UserPointsController.php`

Updated `adjustPoints()` method to explicitly set these fields to null:
```php
$transaction = \Modules\SystemSetting\app\Models\UserPointsTransaction::create([
    'user_id' => $userId,
    'points' => $validated['points'],
    'type' => 'adjusted',
    'transactionable_id' => null,
    'transactionable_type' => null,
]);
```

## Transaction Types and Transactionable Usage

### With Transactionable (Related to specific entity)
1. **Earned** - Related to Order (VendorOrderStage)
   - `transactionable_type`: `Modules\Order\app\Models\VendorOrderStage`
   - `transactionable_id`: VendorOrderStage ID

2. **Redeemed** - Related to Order
   - `transactionable_type`: `Modules\Order\app\Models\Order`
   - `transactionable_id`: Order ID

3. **Adjusted** (Refund) - Related to RefundRequest
   - `transactionable_type`: `Modules\Refund\app\Models\RefundRequest`
   - `transactionable_id`: RefundRequest ID

### Without Transactionable (Manual/System actions)
1. **Adjusted** (Manual) - Admin manually adjusts points
   - `transactionable_type`: NULL
   - `transactionable_id`: NULL

2. **Expired** - System expires old points
   - `transactionable_type`: NULL
   - `transactionable_id`: NULL

## Testing
The fix allows:
- ✅ Manual point adjustments through admin panel
- ✅ Automatic point earning from orders
- ✅ Point deductions from refunds
- ✅ Point redemption in orders
- ✅ Point expiration

## Migration Status
```
2026_01_23_000001_make_transactionable_nullable_in_user_points_transactions ... Ran
```

## Files Modified
1. `Modules/SystemSetting/database/migrations/2026_01_23_000001_make_transactionable_nullable_in_user_points_transactions.php` (Created)
2. `Modules/SystemSetting/app/Http/Controllers/UserPointsController.php` (Updated)

## Notes
- The polymorphic relationship is optional - it's used when the transaction is related to a specific entity (order, refund, etc.)
- For manual adjustments and system actions (like expiration), these fields should be NULL
- The UserPointsService methods already handle setting these fields correctly for different transaction types

# User Points Service Customer Model Fix

## Issue
When marking a refund as "refunded", the points deduction was failing with multiple errors:

1. **First Error**: `No query results for model [App\\Models\\User] 656`
2. **Second Error** (after fix): `Column not found: 1054 Unknown column 'points' in 'field list'`

## Root Causes

### Issue 1: Wrong Model
The `UserPointsService` was hardcoded to use the `User` model, but customer ID 656 is in the `Customer` model.

### Issue 2: Non-existent Column
The service was trying to update a `points` column in the `customers` table:
```php
$customer->increment('points', $points);  // ❌ Column doesn't exist!
$customer->decrement('points', $points);  // ❌ Column doesn't exist!
```

However, the `customers` table doesn't have a `points` column. Points are stored as **transactions** in the `user_points_transactions` table, not as a balance in the customer record.

## Solution

### Architecture Understanding
The points system uses a **transaction-based approach**:
- Points are NOT stored as a balance in the customer table
- Points are stored as individual transactions in `user_points_transactions`
- Total points = `SUM(points)` from all transactions
  - Positive values = earned points
  - Negative values = deducted/redeemed points

### Changes Made

1. **Changed Model Reference**
   ```php
   // Before
   use App\Models\User;
   
   // After
   use Modules\Customer\app\Models\Customer;
   ```

2. **Removed Column Updates**
   ```php
   // Before (WRONG - tries to update non-existent column)
   $customer = Customer::findOrFail($userId);
   $customer->increment('points', $points);
   
   // After (CORRECT - only creates transaction)
   $customer = Customer::findOrFail($userId);  // Just verify customer exists
   // No increment/decrement - points calculated from transactions
   ```

3. **Updated getUserPoints() Method**
   ```php
   // Before (WRONG - tries to read non-existent column)
   public function getUserPoints(int $userId): float
   {
       $customer = Customer::find($userId);
       return $customer ? $customer->points : 0;
   }
   
   // After (CORRECT - calculates from transactions)
   public function getUserPoints(int $userId): float
   {
       return UserPointsTransaction::where('user_id', $userId)->sum('points');
   }
   ```

4. **Enhanced Logging**
   Added `total_points` to log messages to show calculated balance after each operation.

## How It Works Now

### Adding Points (Order Delivered)
1. Verify customer exists
2. Create transaction with positive points: `+4600`
3. Log the operation with new total
4. Total points = SUM of all transactions

### Deducting Points (Refund Completed)
1. Verify customer exists
2. Create transaction with negative points: `-2300`
3. Log the operation with new total
4. Total points = SUM of all transactions

### Example
```
Customer 656 transactions:
- Transaction 1: +4600 (earned from order)
- Transaction 2: -2300 (deducted from refund)
- Total: 4600 + (-2300) = 2300 points
```

## Test Case

**Refund #64:**
- Customer ID: 656
- Refunded Amount: 230 EGP
- Points Per Currency: 10
- Points to Deduct: 2300

**Expected Flow:**
1. ✅ Find customer 656 (exists)
2. ✅ Create transaction: `user_id=656, points=-2300, type=adjusted`
3. ✅ Calculate total: Previous earned (4600) - Deducted (2300) = 2300 remaining
4. ✅ Log success with total points

## Files Modified
- `Modules/SystemSetting/app/Services/UserPointsService.php`

## Impact
- ✅ Points deduction works correctly (no column errors)
- ✅ Points earning works correctly
- ✅ Points redemption works correctly
- ✅ Total points calculated accurately from transactions
- ✅ No need for `points` column in customers table
- ✅ Full transaction history maintained

## Status: ✅ FIXED

The service now correctly uses a transaction-based approach, calculating points from the sum of all transactions rather than storing a balance in the customer table.

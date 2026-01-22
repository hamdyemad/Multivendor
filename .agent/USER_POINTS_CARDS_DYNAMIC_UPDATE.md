# User Points Cards Dynamic Update

## Overview
Updated the user points transaction view cards to use dynamic calculations from the Customer model instead of the old UserPoints model.

## Changes Made

### Controller Update
**File**: `Modules/SystemSetting/app/Http/Controllers/UserPointsController.php`

#### Before (Using UserPoints Model)
```php
public function transactionsView($lang, $countryCode, $userId)
{
    $customer = Customer::findOrFail($userId);
    $userPoint = UserPoints::where('user_id', $customer->id)->first();
    $adjustedPoints = UserPointsTransaction::where('user_id', $customer->id)
        ->where('type', 'adjusted')
        ->sum('points');
    
    $data = [
        'total_points' => $userPoint ? $userPoint->total_points : 0,
        'earned_points' => $userPoint ? $userPoint->earned_points : 0,
        'redeemed_points' => $userPoint ? $userPoint->redeemed_points : 0,
        'expired_points' => $userPoint ? $userPoint->expired_points : 0,
        'available_points' => $userPoint ? $userPoint->available_points : 0,
        'adjusted_points' => $adjustedPoints,
    ];
}
```

#### After (Using Customer Model Dynamic Calculations)
```php
public function transactionsView($lang, $countryCode, $userId)
{
    $customer = Customer::findOrFail($userId);
    
    // Use dynamic calculations from Customer model
    $data = [
        'total_points' => $customer->total_points,
        'earned_points' => $customer->earned_points,
        'redeemed_points' => $customer->redeemed_points,
        'expired_points' => $customer->expired_points,
        'available_points' => $customer->available_points,
        'adjusted_points' => $customer->adjusted_points,
    ];
}
```

## Benefits

### 1. Real-Time Accuracy
- Cards now show live data calculated from transactions
- No dependency on UserPoints model which may be out of sync
- Always reflects the current state

### 2. Simplified Code
- Removed dependency on UserPoints model
- No need for separate query for adjusted points
- Single source of truth (Customer model)

### 3. Consistency
- All points data comes from the same calculation methods
- Same logic used everywhere in the application
- Easier to maintain and debug

### 4. Performance
- Customer model methods use efficient queries
- Can be cached if needed
- No redundant data storage

## Cards Display

The view displays 4 cards:

### Card 1: Available Points (Purple)
- **Icon**: Wallet
- **Value**: `$customer->available_points`
- **Formula**: `earned + adjusted - redeemed - expired`
- **Example**: 2300.00

### Card 2: Earned Points (Green)
- **Icon**: Arrow Up
- **Value**: `$customer->earned_points`
- **Formula**: `SUM(points) WHERE type = 'earned'`
- **Example**: 4600.00

### Card 3: Adjusted Points (Blue)
- **Icon**: Edit
- **Value**: `$customer->adjusted_points`
- **Formula**: `SUM(points) WHERE type = 'adjusted'`
- **Example**: -2300.00

### Card 4: Redeemed Points (Pink)
- **Icon**: Arrow Down
- **Value**: `$customer->redeemed_points`
- **Formula**: `ABS(SUM(points)) WHERE type = 'redeemed'`
- **Example**: 0.00

## Example Data (Customer 656)

### Transactions
```
ID  | Type     | Points
----|----------|--------
694 | earned   | +4600
695 | adjusted | -2300
```

### Card Values
```
Available Points: 2300.00  (4600 + (-2300) - 0 - 0)
Earned Points:    4600.00  (SUM of earned)
Adjusted Points:  -2300.00 (SUM of adjusted)
Redeemed Points:  0.00     (SUM of redeemed)
```

## Files Modified
1. `Modules/SystemSetting/app/Http/Controllers/UserPointsController.php`
2. `Modules/Customer/app/Models/Customer.php` (previously updated with dynamic methods)

## Status: ✅ COMPLETE

The points cards now display real-time, accurate data calculated dynamically from transactions using the Customer model's accessor methods.

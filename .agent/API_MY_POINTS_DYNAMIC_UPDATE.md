# API My Points Dynamic Update

## Overview
Updated the `/points/my-points` API endpoint to use dynamic calculations from the Customer model instead of the old UserPoints model.

## Endpoint
**Route**: `GET /api/points/my-points`
**Authentication**: Required (sanctum)
**Controller**: `CustomerPointsApiController::myPoints()`

## Changes Made

### Before (Using UserPoints Model)
```php
public function myPoints(Request $request)
{
    $user = $request->user();
    $userPoints = UserPoints::where('user_id', $user->id)->first();
    
    $data = [
        'total_points' => $userPoints ? round($userPoints->total_points, 2) : 0,
        'earned_points' => $userPoints ? round($userPoints->earned_points, 2) : 0,
        'redeemed_points' => $userPoints ? round($userPoints->redeemed_points, 2) : 0,
        'expired_points' => $userPoints ? round($userPoints->expired_points, 2) : 0,
        'available_points' => $userPoints ? round($userPoints->available_points, 2) : 0,
    ];
}
```

### After (Using Customer Model Dynamic Calculations)
```php
public function myPoints(Request $request)
{
    $customer = $request->user();
    
    // Use dynamic calculations from Customer model
    $data = [
        'total_points' => round($customer->total_points, 2),
        'earned_points' => round($customer->earned_points, 2),
        'redeemed_points' => round($customer->redeemed_points, 2),
        'expired_points' => round($customer->expired_points, 2),
        'adjusted_points' => round($customer->adjusted_points, 2),  // NEW
        'available_points' => round($customer->available_points, 2),
    ];
}
```

## Key Changes

### 1. Removed UserPoints Dependency
- No longer queries `UserPoints` model
- Uses Customer model accessors directly
- Simpler, cleaner code

### 2. Added Adjusted Points
- Now includes `adjusted_points` in the response
- Shows points deducted from refunds
- More complete picture of points history

### 3. Real-Time Calculations
- All values calculated from transactions on-demand
- Always accurate and up-to-date
- No sync issues

## API Response

### Example Response
```json
{
    "message": "Points retrieved successfully",
    "success": true,
    "data": {
        "total_points": 2300.00,
        "points_value": 230.00,
        "earned_points": 4600.00,
        "redeemed_points": 0.00,
        "expired_points": 0.00,
        "adjusted_points": -2300.00,
        "available_points": 2300.00,
        "expiring_soon": []
    }
}
```

### Field Descriptions

| Field | Description | Calculation |
|-------|-------------|-------------|
| `total_points` | Total points balance | SUM of all transactions |
| `points_value` | Monetary value of points | (total_points / points_per_currency) × currency_per_point |
| `earned_points` | Points earned from orders | SUM WHERE type = 'earned' |
| `redeemed_points` | Points used in purchases | ABS(SUM WHERE type = 'redeemed') |
| `expired_points` | Points that expired | ABS(SUM WHERE type = 'expired') |
| `adjusted_points` | Points adjusted (refunds) | SUM WHERE type = 'adjusted' (usually negative) |
| `available_points` | Points available to use | earned + adjusted - redeemed - expired |
| `expiring_soon` | Transactions expiring in 30 days | Array of transactions |

## Example Scenario

### Customer 656 Data
**Transactions:**
- Earned: +4600 (from order delivery)
- Adjusted: -2300 (from refund)

**API Response:**
```json
{
    "total_points": 2300.00,
    "earned_points": 4600.00,
    "adjusted_points": -2300.00,
    "redeemed_points": 0.00,
    "expired_points": 0.00,
    "available_points": 2300.00
}
```

**Calculation:**
```
Available = Earned + Adjusted - Redeemed - Expired
         = 4600 + (-2300) - 0 - 0
         = 2300
```

## Benefits

### 1. Consistency
- Same calculation logic as admin panel
- Same calculation logic as web interface
- Single source of truth

### 2. Accuracy
- Real-time calculations from transactions
- No stale data
- No sync issues

### 3. Transparency
- Shows adjusted points separately
- Customer can see refund impact
- More detailed breakdown

### 4. Maintainability
- One place to update calculations
- Easier to debug
- Less code duplication

## Files Modified
- `Modules/Customer/app/Http/Controllers/Api/CustomerPointsApiController.php`

## Related Updates
This update is part of a series of changes to use dynamic points calculations:
1. ✅ Customer model - Added dynamic calculation methods
2. ✅ Admin panel cards - Updated to use Customer model
3. ✅ API endpoint - Updated to use Customer model (this change)

## Status: ✅ COMPLETE

The `/points/my-points` API endpoint now returns real-time, accurate points data calculated dynamically from transactions using the Customer model's accessor methods.

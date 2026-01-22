# Customer Dynamic Points Calculation

## Overview
Added dynamic points calculation methods to the `Customer` model that calculate points directly from transactions in real-time, rather than storing a static balance.

## Architecture

### Transaction-Based Points System
- Points are NOT stored as a column in the `customers` table
- Points are stored as individual transactions in `user_points_transactions` table
- Total points = `SUM(points)` from all transactions
- Each transaction has a type: `earned`, `redeemed`, `adjusted`, `expired`

### Benefits
- ✅ Real-time accuracy - always reflects current state
- ✅ Full audit trail - every point change is tracked
- ✅ No sync issues - no need to keep balance in sync
- ✅ Historical data - can see all point movements
- ✅ Flexible - easy to add new transaction types

## Methods Added to Customer Model

### 1. Relationship
```php
public function pointsTransactions()
{
    return $this->hasMany(UserPointsTransaction::class, 'user_id');
}
```

### 2. Total Points (Accessor)
```php
public function getTotalPointsAttribute(): float
{
    return $this->pointsTransactions()->sum('points');
}
```
**Usage**: `$customer->total_points`
**Returns**: Sum of all points (positive + negative)

### 3. Earned Points (Accessor)
```php
public function getEarnedPointsAttribute(): float
{
    return $this->pointsTransactions()->where('type', 'earned')->sum('points');
}
```
**Usage**: `$customer->earned_points`
**Returns**: Total points earned from orders

### 4. Redeemed Points (Accessor)
```php
public function getRedeemedPointsAttribute(): float
{
    return abs($this->pointsTransactions()->where('type', 'redeemed')->sum('points'));
}
```
**Usage**: `$customer->redeemed_points`
**Returns**: Total points used in purchases (absolute value)

### 5. Adjusted Points (Accessor)
```php
public function getAdjustedPointsAttribute(): float
{
    return $this->pointsTransactions()->where('type', 'adjusted')->sum('points');
}
```
**Usage**: `$customer->adjusted_points`
**Returns**: Total points adjusted (usually negative from refunds)

### 6. Expired Points (Accessor)
```php
public function getExpiredPointsAttribute(): float
{
    return abs($this->pointsTransactions()->where('type', 'expired')->sum('points'));
}
```
**Usage**: `$customer->expired_points`
**Returns**: Total points that have expired (absolute value)

### 7. Available Points (Accessor)
```php
public function getAvailablePointsAttribute(): float
{
    $earned = $this->pointsTransactions()->where('type', 'earned')->sum('points');
    $redeemed = abs($this->pointsTransactions()->where('type', 'redeemed')->sum('points'));
    $expired = abs($this->pointsTransactions()->where('type', 'expired')->sum('points'));
    $adjusted = $this->pointsTransactions()->where('type', 'adjusted')->sum('points'); // Usually negative
    
    // Available = earned + adjusted - redeemed - expired
    return $earned + $adjusted - $redeemed - $expired;
}
```
**Usage**: `$customer->available_points`
**Returns**: Points available to use
**Formula**: `earned + adjusted - redeemed - expired`
**Note**: Adjusted points (from refunds) reduce available points since they're usually negative

### 8. Points Balance (Accessor)
```php
public function getPointsBalanceAttribute(): array
{
    return [
        'total' => $this->total_points,
        'earned' => $this->earned_points,
        'redeemed' => $this->redeemed_points,
        'adjusted' => $this->adjusted_points,
        'expired' => $this->expired_points,
        'available' => $this->available_points,
    ];
}
```
**Usage**: `$customer->points_balance`
**Returns**: Complete breakdown of all point types

## Usage Examples

### Example 1: Get Customer's Total Points
```php
$customer = Customer::find(656);
echo $customer->total_points;  // 2300.00
```

### Example 2: Get Points Breakdown
```php
$customer = Customer::find(656);
$balance = $customer->points_balance;

/*
[
    'total' => 2300.00,
    'earned' => 4600.00,
    'redeemed' => 0.00,
    'adjusted' => -2300.00,  // From refund
    'expired' => 0.00,
    'available' => 2300.00,
]
*/
```

### Example 3: Check if Customer Has Enough Points
```php
$customer = Customer::find(656);
$requiredPoints = 1000;

if ($customer->available_points >= $requiredPoints) {
    // Customer can use points
}
```

### Example 4: Display in API Response
```php
// Customer model has appends = ['full_name', 'total_points', 'available_points']
return response()->json([
    'customer' => $customer,  // Automatically includes total_points and available_points
]);

/*
{
    "customer": {
        "id": 656,
        "first_name": "John",
        "last_name": "Doe",
        "full_name": "John Doe",
        "total_points": 2300.00,
        "available_points": 2300.00
    }
}
*/
```

### Example 5: Get Points History
```php
$customer = Customer::find(656);
$transactions = $customer->pointsTransactions()
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($transactions as $transaction) {
    echo "{$transaction->points} points - {$transaction->type} - {$transaction->description}\n";
}

/*
-2300.00 points - adjusted - Points deducted for refund: REF-20260122-0001
+4600.00 points - earned - Earned 4600 points from order #ORD-000001
*/
```

### Example 6: Eager Load Points Data
```php
// Load customers with their points transactions
$customers = Customer::with('pointsTransactions')->get();

foreach ($customers as $customer) {
    echo "{$customer->full_name}: {$customer->total_points} points\n";
}
```

## Real-World Scenario

### Customer 656 Transaction History
```
Transaction ID | Type     | Points   | Description
-------------- | -------- | -------- | -----------
694            | earned   | +4600.00 | Earned from order delivery
695            | adjusted | -2300.00 | Deducted from refund
```

### Calculations
```php
$customer = Customer::find(656);

$customer->total_points;      // 2300.00 (4600 - 2300)
$customer->earned_points;     // 4600.00
$customer->adjusted_points;   // -2300.00
$customer->redeemed_points;   // 0.00
$customer->expired_points;    // 0.00
$customer->available_points;  // 2300.00 (4600 + (-2300) - 0 - 0)
```

### Formula Breakdown
```
Available Points = Earned + Adjusted - Redeemed - Expired
                 = 4600 + (-2300) - 0 - 0
                 = 2300
```

## Performance Considerations

### Caching (Optional)
For high-traffic applications, you can cache the points calculation:

```php
public function getTotalPointsAttribute(): float
{
    return Cache::remember(
        "customer.{$this->id}.total_points",
        now()->addMinutes(5),
        fn() => $this->pointsTransactions()->sum('points')
    );
}
```

### Eager Loading
When loading multiple customers, eager load transactions:

```php
$customers = Customer::with('pointsTransactions')->get();
```

## Migration Not Required
No database migration is needed because:
- No new columns added to `customers` table
- Points calculated dynamically from existing `user_points_transactions` table
- All methods are accessors (virtual attributes)

## Files Modified
- `Modules/Customer/app/Models/Customer.php`

## Status: ✅ COMPLETE

The Customer model now has dynamic points calculation methods that can be used anywhere in the application to get real-time, accurate points data.

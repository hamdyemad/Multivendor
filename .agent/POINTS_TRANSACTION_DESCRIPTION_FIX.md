# Points Transaction Description Translation Fix

## Issue
When marking a refund as "refunded", the points deduction was failing with error:
```
Column not found: 1054 Unknown column 'description' in 'field list'
SQL: insert into `user_points_transactions` (..., `description`, ...) values (...)
```

## Root Cause
The `UserPointsTransaction` model uses the `Translation` trait to store descriptions in the `translations` table, not as a direct column in the `user_points_transactions` table.

The service was trying to insert `description` directly:
```php
UserPointsTransaction::create([
    'user_id' => $userId,
    'points' => $points,
    'description' => $description,  // ❌ Not a direct column!
    // ...
]);
```

## How Translation Trait Works

### Table Structure
- `user_points_transactions` table: Stores transaction data (points, type, etc.)
- `translations` table: Stores translatable fields (description in multiple languages)

### Model Setup
```php
class UserPointsTransaction extends Model
{
    use Translation;  // Enables translation functionality
    
    public function getDescriptionAttribute() {
        return $this->getTranslation('description', app()->getLocale());
    }
}
```

### Correct Usage
```php
// 1. Create the record WITHOUT description
$transaction = UserPointsTransaction::create([
    'user_id' => $userId,
    'points' => $points,
    // No description here
]);

// 2. Set translations separately
$transaction->setTranslation('description', 'en', 'English description');
$transaction->setTranslation('description', 'ar', 'Arabic description');
$transaction->save();
```

## Solution
Updated all methods in `UserPointsService` to use the Translation trait correctly:

### Before (BROKEN)
```php
$transaction = UserPointsTransaction::create([
    'user_id' => $userId,
    'points' => $points,
    'description' => $description,  // ❌ Tries to insert into non-existent column
    // ...
]);
```

### After (FIXED)
```php
// Create without description
$transaction = UserPointsTransaction::create([
    'user_id' => $userId,
    'points' => $points,
    // ...
]);

// Set description using Translation trait
$transaction->setTranslation('description', 'en', $description);
$transaction->setTranslation('description', 'ar', $description);
$transaction->save();
```

## Methods Updated
1. `addPoints()` - For earned points
2. `deductPoints()` - For adjusted points (refunds)
3. `redeemPoints()` - For redeemed points
4. `expirePoints()` - For expired points

## Example Flow

### Refund Points Deduction
```php
// Called by RefundRequestObserver
$this->userPointsService->deductPoints(
    userId: 656,
    points: 2300,
    transactionableType: RefundRequest::class,
    transactionableId: 64,
    description: "Points deducted for refund: REF-20260122-0001",
    pointsPerCurrency: 10.0
);

// Inside the service:
// 1. Create transaction
$transaction = UserPointsTransaction::create([
    'user_id' => 656,
    'points' => -2300,
    'type' => 'adjusted',
    'transactionable_type' => 'Modules\Refund\app\Models\RefundRequest',
    'transactionable_id' => 64,
    'points_per_currency' => 10.0,
]);

// 2. Set translations
$transaction->setTranslation('description', 'en', 'Points deducted for refund: REF-20260122-0001');
$transaction->setTranslation('description', 'ar', 'Points deducted for refund: REF-20260122-0001');
$transaction->save();

// 3. Description stored in translations table:
// - translatable_type: 'Modules\SystemSetting\app\Models\UserPointsTransaction'
// - translatable_id: [transaction_id]
// - key: 'description'
// - locale: 'en' / 'ar'
// - value: 'Points deducted for refund: REF-20260122-0001'
```

## Benefits
- ✅ Descriptions stored correctly in translations table
- ✅ Multi-language support (English and Arabic)
- ✅ No database column errors
- ✅ Consistent with other translatable models in the system
- ✅ Can retrieve description in any language: `$transaction->description`

## Files Modified
- `Modules/SystemSetting/app/Services/UserPointsService.php`

## Status: ✅ FIXED

The service now correctly uses the Translation trait to store descriptions in the translations table, allowing multi-language support and avoiding column errors.

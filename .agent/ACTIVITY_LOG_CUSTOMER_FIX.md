# Activity Log Customer Foreign Key Fix

## Issue
When a customer creates a refund (or performs any action that triggers activity logging), the system throws an error:
```
[2026-01-22 11:07:14] local.ERROR: GlobalModelObserver logActivity error: 
SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: 
a foreign key constraint fails (`eramo`.`activity_logs`, 
CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL)
```

## Root Cause
The `GlobalModelObserver` was storing the authenticated user's ID in `activity_logs.user_id` without checking the user type:

```php
$user = request()->user() ?? auth()->user() ?? auth('web')->user();
$userId = $user?->id;  // This could be a customer ID!
```

The problem:
- **Customers** are stored in the `customers` table (Customer model)
- **Admins/Vendors** are stored in the `users` table (User model)
- The `activity_logs.user_id` column has a foreign key constraint to the `users` table
- When a customer (ID 656) performs an action, the observer tries to store customer ID 656 in `user_id`
- ❌ Foreign key constraint fails because customer ID 656 doesn't exist in the `users` table

## Solution
Updated `GlobalModelObserver::logActivity()` to check the user type before storing the ID:

```php
// Get user from request (works for both web and API)
$user = request()->user() ?? auth()->user() ?? auth('web')->user();

// Only set user_id if the authenticated user is from the users table (admin/vendor)
// Not from customers table (customers don't have entries in users table)
$userId = null;
if ($user && $user instanceof \App\Models\User) {
    $userId = $user->id;
}
```

### Logic
- If authenticated user is an instance of `\App\Models\User` → Store their ID
- If authenticated user is an instance of `\Modules\Customer\app\Models\Customer` → Store NULL
- If no authenticated user → Store NULL

## Impact

### Before (BROKEN)
- Customer creates refund → Observer tries to log with `user_id = 656` (customer ID)
- ❌ Foreign key constraint violation
- Activity log fails to be created
- Error logged

### After (FIXED)
- Customer creates refund → Observer checks user type
- User is Customer → Sets `user_id = NULL`
- ✅ Activity log created successfully
- No error

### For Admin/Vendor Actions
- Admin/Vendor performs action → Observer checks user type
- User is from `users` table → Sets `user_id = admin/vendor ID`
- ✅ Activity log created with proper user reference
- Can track which admin/vendor performed the action

### For Customer Actions
- Customer performs action → Observer checks user type
- User is from `customers` table → Sets `user_id = NULL`
- ✅ Activity log created without user reference
- Still logs: IP address, user agent, country, model, action, properties

## Alternative Considered
We could have:
1. Added a `customer_id` column to `activity_logs` table
2. Stored customer ID there when action is by customer

However, this would require:
- New migration
- Schema changes
- Updates to activity log display logic
- More complex queries

The current solution is simpler and sufficient since:
- Activity logs are primarily for admin/vendor actions
- Customer actions are less critical to track by user
- We still log IP, user agent, and other metadata

## Files Modified
- `app/Observers/GlobalModelObserver.php`

## Status: ✅ FIXED

Activity logging now works correctly for both admin/vendor users and customers, without foreign key constraint violations.

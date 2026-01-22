# Refund Notification URL Fix

## Issue
When changing the status of a refund, an error was logged:
```
[2026-01-22 11:02:03] local.ERROR: Failed to generate admin URL 
{"route":"admin.refunds.show","error":"Missing required parameter for [Route: admin.refunds.show] 
[URI: {lang}/{countryCode}/admin/refunds/{id}] [Missing parameter: id]."}
```

## Root Cause
In `RefundNotificationService.php`, the `createAdminNotification()` method was passing the wrong parameter name to the route:

**Incorrect:**
```php
url: $adminNotificationService->generateAdminUrl('admin.refunds.show', ['refundRequest' => $refundRequest->id])
```

**Route Definition:**
```php
Route::get('/{id}', [RefundRequestController::class, 'show'])->name('show');
```

The route expects parameter `{id}`, but the code was passing `refundRequest`.

## Solution
Changed the parameter name from `refundRequest` to `id`:

**Corrected:**
```php
url: $adminNotificationService->generateAdminUrl('admin.refunds.show', ['id' => $refundRequest->id])
```

## Files Modified
- `Modules/Refund/app/Services/RefundNotificationService.php` (line 324)

## Impact
- ✅ Admin notifications for refund status changes now generate correct URLs
- ✅ No more errors in the log when changing refund status
- ✅ Clicking on refund notifications in the admin panel will now work correctly

## Status: ✅ FIXED

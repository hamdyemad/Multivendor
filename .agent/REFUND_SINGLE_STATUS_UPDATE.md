# Refund System - Single Status Update

## Changes Made

### ✅ Removed Vendor Status Field
Previously, the system had two separate status fields:
- `status` - Admin/overall status
- `vendor_status` - Vendor-specific status

Now simplified to **single status field** that applies to both admin and vendor.

---

## Files Modified

### 1. Database Migration
**Created:** `Modules/Refund/database/migrations/2026_01_18_000011_remove_vendor_status_from_refund_requests.php`
- Removes `vendor_status` column from `refund_requests` table
- Run migration: `php artisan migrate`

### 2. RefundRequest Model
**File:** `Modules/Refund/app/Models/RefundRequest.php`
- Removed `vendor_status` from `$fillable` array
- Removed `getVendorStatusLabelAttribute()` method
- Removed `vendor_status` filter from `scopeFilter()` method
- Kept single `getStatusLabelAttribute()` method

### 3. RefundRequestObserver
**File:** `Modules/Refund/app/Observers/RefundRequestObserver.php`
- Removed separate `vendor_status` change detection
- Now both customer and vendor are notified on single `status` change
- Simplified notification logic

### 4. RefundNotificationService
**File:** `Modules/Refund/app/Services/RefundNotificationService.php`
- `notifyVendorStatusChange()` now uses same status as customer
- Both customer and vendor receive notifications on status change
- Firebase notifications use same status value
- Notification type changed from `refund_vendor_status_changed` to `refund_status_changed`

### 5. API Resource
**File:** `Modules/Refund/app/Http/Resources/RefundRequestResource.php`
- Removed `vendor_status` field
- Removed `vendor_status_label` field
- Only returns single `status` and `status_label`

### 6. Translations
**Files:** 
- `Modules/Refund/lang/en/refund.php`
- `Modules/Refund/lang/ar/refund.php`

Kept existing notification keys (still functional):
- `status_changed_title` - Used for both customer and vendor
- `status_changed_body` - Used for both customer and vendor
- `vendor_status_changed_title` - Deprecated but kept for backward compatibility
- `vendor_status_changed_body` - Deprecated but kept for backward compatibility

---

## Notification Flow

### When Status Changes:

1. **RefundRequest model updated** → `status` field changes
2. **Observer detects change** → `RefundRequestObserver::updated()`
3. **Notifications sent**:
   - Customer receives notification (Laravel + Firebase)
   - Vendor receives notification (Laravel + Firebase)
4. **Firebase data payload**:
   ```json
   {
     "type": "refund_status_changed",
     "refund_id": "123",
     "refund_number": "REF-20260118-0001",
     "old_status": "pending",
     "new_status": "approved",
     "order_id": "456"
   }
   ```

---

## API Response Changes

### Before (with vendor_status):
```json
{
  "id": 1,
  "status": "approved",
  "status_label": "Approved",
  "vendor_status": "pending",
  "vendor_status_label": "Pending"
}
```

### After (single status):
```json
{
  "id": 1,
  "status": "approved",
  "status_label": "Approved"
}
```

---

## Benefits

1. **Simplified Logic**: One status to manage instead of two
2. **Consistent State**: No confusion between admin and vendor status
3. **Easier Filtering**: Single status field for queries
4. **Unified Notifications**: Both parties notified on same status change
5. **Cleaner API**: Simpler response structure

---

## Migration Steps

1. **Backup database** (recommended)
2. **Run migration**:
   ```bash
   php artisan migrate
   ```
3. **Clear cache**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```
4. **Test API endpoints** to verify single status field

---

## Status Values

The single `status` field supports these values:
- `pending` - Initial state when refund created
- `approved` - Refund approved by admin/vendor
- `in_progress` - Refund being processed
- `picked_up` - Product picked up from customer
- `refunded` - Money refunded to customer
- `rejected` - Refund request rejected
- `cancelled` - Refund cancelled by customer

---

## Backward Compatibility

- Old migrations with `vendor_status` are preserved for rollback
- Translation keys for vendor status kept for backward compatibility
- No breaking changes to existing refund records (migration handles cleanup)

---

## Testing Checklist

- [ ] Run migration successfully
- [ ] Create new refund request
- [ ] Update refund status
- [ ] Verify customer receives notification
- [ ] Verify vendor receives notification
- [ ] Check Firebase notifications sent
- [ ] Test API endpoints return single status
- [ ] Verify filtering by status works
- [ ] Check dashboard displays correctly

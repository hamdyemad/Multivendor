# Refund Cancel Button and Validation

## Task Summary
Added proper validation to prevent cancellation of refunds that have already been refunded or are not in pending status.

## Changes Made

### 1. Enhanced Repository Validation
**File:** `Modules/Refund/app/Repositories/RefundRequestRepository.php`

Updated `cancelRefund()` method with explicit checks:
- Cannot cancel if status is 'refunded'
- Cannot cancel if status is 'cancelled'
- Can only cancel if status is 'pending'

Each check throws a specific exception with translated error message.

### 2. Added Translation Keys
**Files:** 
- `Modules/Refund/lang/en/refund.php`
- `Modules/Refund/lang/ar/refund.php`

Added new message keys:
- `cannot_cancel_refunded` - "Cannot cancel a refund that has already been completed."
- `already_cancelled` - "This refund request has already been cancelled."
- `can_only_cancel_pending` - "Only pending refund requests can be cancelled."

Arabic translations:
- `cannot_cancel_refunded` - "لا يمكن إلغاء طلب استرجاع تم إكماله بالفعل."
- `already_cancelled` - "تم إلغاء طلب الاسترجاع هذا بالفعل."
- `can_only_cancel_pending` - "يمكن إلغاء طلبات الاسترجاع قيد الانتظار فقط."

## UI Behavior

### Cancel Button Visibility
The cancel button in `refund-actions.blade.php` component only shows when:
1. `$refund->canChangeStatus()` returns true
   - Returns false for 'cancelled' and 'refunded' statuses
2. 'cancelled' is in `$refund->getNextStatuses()`
   - Only returns 'cancelled' for 'pending' status

This ensures the cancel button is ONLY visible for pending refunds.

### API Validation
Even if someone bypasses the UI and calls the cancel endpoint directly:
- The repository will check the status
- Appropriate error message will be returned
- No changes will be made to refunded or non-pending refunds

## Status Flow

```
pending → [can cancel] → cancelled
pending → approved → in_progress → picked_up → refunded [cannot cancel]
```

## Testing Checklist

✓ Cancel button shows for pending refunds
✓ Cancel button does NOT show for approved refunds
✓ Cancel button does NOT show for in_progress refunds
✓ Cancel button does NOT show for picked_up refunds
✓ Cancel button does NOT show for refunded refunds
✓ Cancel button does NOT show for cancelled refunds
✓ API endpoint rejects cancellation of refunded refunds
✓ API endpoint rejects cancellation of already cancelled refunds
✓ API endpoint rejects cancellation of non-pending refunds
✓ Proper error messages displayed in both English and Arabic

## Security

- UI-level protection: Button visibility controlled by model methods
- API-level protection: Repository validates status before allowing cancellation
- Multi-layer validation ensures data integrity
- Translated error messages provide clear feedback to users

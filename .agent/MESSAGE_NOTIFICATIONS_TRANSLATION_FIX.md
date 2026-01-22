# Message Notifications Translation Fix

## Status: ✅ COMPLETE

## Issue
Message notifications were showing raw field names ("Name", "Email", "Message id") instead of translated content.

## Root Cause
The `MessageObserver` was storing raw field names in the notification data instead of using translation key prefixes like other observers.

## Solution

### 1. Updated MessageObserver
**File**: `Modules/SystemSetting/app/Observers/MessageObserver.php`

**Changes:**
- Changed `title` from `$message->name` to `'menu.new_message'` (translation key)
- Changed `description` from `trans('menu.new_message')` to `'menu.sent_new_message'` (translation key)
- Updated data keys to use translation prefixes:
  - `'message_id'` → `'common.message_id'`
  - `'name'` → `'common.name'`
  - `'email'` → `'common.email'`

**Before:**
```php
$this->notificationService->create(
    type: 'new_message',
    title: $message->name,
    description: trans('menu.new_message'),
    // ...
    data: [
        'message_id' => $message->id,
        'name' => $message->name,
        'email' => $message->email,
    ],
);
```

**After:**
```php
$this->notificationService->create(
    type: 'new_message',
    title: 'menu.new_message',
    description: 'menu.sent_new_message',
    // ...
    data: [
        'common.message_id' => $message->id,
        'common.name' => $message->name,
        'common.email' => $message->email,
    ],
);
```

### 2. Added Missing Translation Keys

#### Menu Translations
**Files**: `lang/ar/menu.php`, `lang/en/menu.php`

Added:
- `'new_message'` - Title for new message notification
- `'sent_new_message'` - Description for new message notification

**Arabic:**
```php
'new_message' => 'رسالة جديدة',
'sent_new_message' => 'أرسل رسالة جديدة',
```

**English:**
```php
'new_message' => 'New Message',
'sent_new_message' => 'Sent a new message',
```

#### Common Translations
**Files**: `lang/ar/common.php`, `lang/en/common.php`

Added:
- `'message_id'` - For message ID field

**Arabic:**
```php
'message_id' => 'رقم الرسالة',
```

**English:**
```php
'message_id' => 'Message ID',
```

**Note:** `'name'` and `'email'` keys already existed in common translations.

## Pattern Consistency

Now all notification observers follow the same pattern:

1. **Order Notifications** (`OrderObserver`)
   - Title: `'menu.order'`
   - Description: `'order.new_order_received'`
   - Data: `'order.order_number'`, `'common.name'`, `'order.total'`

2. **Vendor Request Notifications** (`VendorRequestObserver`)
   - Title: `'menu.become a vendor requests.new_request'`
   - Description: `'menu.become a vendor requests.wants_to_become'`
   - Data: `'menu.become a vendor requests.vendor_request_id'`, `'common.company_name'`, `'common.email'`

3. **Withdraw Notifications** (`WithdrawObserver`)
   - Title: `'menu.withdraw_request'` or status-specific
   - Description: `'menu.withdraw module.vendor_sent_request'` or status-specific
   - Data: `'menu.withdraw module.withdraw_id'`, `'menu.withdraw module.request_value'`, `'menu.withdraw module.status'`

4. **Message Notifications** (`MessageObserver`) ✅ NOW FIXED
   - Title: `'menu.new_message'`
   - Description: `'menu.sent_new_message'`
   - Data: `'common.message_id'`, `'common.name'`, `'common.email'`

## Testing

To test the fix:
1. Create a new message through the contact form
2. Check the admin notifications bell
3. The message notification should now show:
   - Title: "New Message" (EN) / "رسالة جديدة" (AR)
   - Description: "Sent a new message" (EN) / "أرسل رسالة جديدة" (AR)
   - Details: Properly translated field names

## Note

Existing message notifications in the database will still show the old format. Only new messages created after this fix will use the translation keys properly.

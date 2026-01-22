# Withdraw Notifications - Complete

## Overview
Updated withdraw notification creation to use translation keys and added proper notifications for withdraw request creation and status changes.

## Changes Made

### File: `Modules/Withdraw/app/Observers/WithdrawObserver.php`

#### Improvements:

1. **Translation Keys Instead of Direct Text**
   - Changed from `trans('menu.withdraw module.xxx')` to storing translation keys
   - Follows the same pattern as VendorRequestObserver and OrderObserver
   - Allows proper multilingual support

2. **New Withdraw Request Notification** (Admin)
   - Triggered when: Vendor creates a new withdraw request
   - Type: `withdraw_request`
   - Recipient: Admin only (`vendorId: null`)
   - Icon: `uil-wallet`
   - Color: `warning` (orange)

3. **Withdraw Status Change Notification** (Vendor)
   - Triggered when: Admin accepts or rejects withdraw request
   - Type: `withdraw_status`
   - Recipient: Specific vendor (`vendorId: $withdraw->reciever_id`)
   - Icon: `uil-wallet`
   - Color: `success` (green) for accepted, `danger` (red) for rejected

### Translation Files Updated

#### Arabic (`lang/ar/menu.php`):
```php
'withdraw_request' => 'طلب سحب',
'withdraw_id' => 'رقم الطلب',
'status' => 'الحالة',
'request_accepted' => 'تم قبول طلب السحب',
'request_rejected' => 'تم رفض طلب السحب',
```

#### English (`lang/en/menu.php`):
```php
'withdraw_request' => 'Withdraw Request',
'withdraw_id' => 'Request ID',
'status' => 'Status',
'request_accepted' => 'Withdraw request accepted',
'request_rejected' => 'Withdraw request rejected',
```

## Notification Details

### 1. New Withdraw Request (Admin)
**When**: Vendor submits new withdraw request

**Notification**:
- **Title**: "طلب سحب" / "Withdraw Request"
- **Description**: "[Vendor Name] أرسل طلب سحب جديد" / "[Vendor Name] sent a new withdraw request"
- **URL**: `/admin/transactionsRequests?status=new`
- **Icon**: Wallet icon
- **Color**: Orange (warning)

**Data Stored**:
```php
[
    'menu.withdraw module.withdraw_id' => 123,
    'vendor.name' => 'Vendor Name',
    'common.amount' => 5000.00,
    'common.currency' => 'EGP',
]
```

### 2. Withdraw Status Change (Vendor)
**When**: Admin accepts or rejects withdraw request

**Notification (Accepted)**:
- **Title**: "بنايه أرسلت لك المبلغ" / "Bnaia sent money to you"
- **Description**: "تم قبول طلب السحب" / "Withdraw request accepted"
- **URL**: `/admin/transactionsRequests?status=accepted`
- **Icon**: Wallet icon
- **Color**: Green (success)

**Notification (Rejected)**:
- **Title**: "بنايه رفضت طلبك" / "Bnaia rejected your request"
- **Description**: "تم رفض طلب السحب" / "Withdraw request rejected"
- **URL**: `/admin/transactionsRequests?status=rejected`
- **Icon**: Wallet icon
- **Color**: Red (danger)

**Data Stored**:
```php
[
    'menu.withdraw module.withdraw_id' => 123,
    'menu.withdraw module.status' => 'accepted', // or 'rejected'
    'common.amount' => 5000.00,
    'common.currency' => 'EGP',
]
```

## How It Works

### Withdraw Request Flow:
1. **Vendor creates withdraw request** → Status = 'new'
2. **WithdrawObserver** `created()` method triggered
3. **Admin notification created** with type `withdraw_request`
4. **Admin sees notification** in withdraw requests bell (card icon)

### Status Change Flow:
1. **Admin accepts/rejects request** → Status changes
2. **WithdrawObserver** `updated()` method triggered
3. **Checks if status changed** to 'accepted' or 'rejected'
4. **Vendor notification created** with type `withdraw_status`
5. **Vendor sees notification** in withdraw requests bell (card icon)

## Observer Registration
The WithdrawObserver is already registered in the service provider.

## Status
**COMPLETE** - Withdraw notifications now use translation keys and properly notify both admin and vendors about withdraw requests and status changes.

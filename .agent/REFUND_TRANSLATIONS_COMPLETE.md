# Refund Module - Complete Translations

## Overview
Added comprehensive translations for all validation messages in the Refund module, supporting both English and Arabic languages.

---

## Translation Files Updated

### 1. English Translations
**File:** `Modules/Refund/lang/en/refund.php`

**Added Section:** `validation`

**Total Validation Keys:** 25

### 2. Arabic Translations
**File:** `Modules/Refund/lang/ar/refund.php`

**Added Section:** `validation`

**Total Validation Keys:** 25

---

## Validation Translation Keys

### Order Validation
```php
'order_required' => 'The order field is required.',
'order_invalid' => 'The selected order is invalid.',
'order_not_yours' => 'The selected order does not belong to you.',
```

**Arabic:**
```php
'order_required' => 'حقل الطلب مطلوب.',
'order_invalid' => 'الطلب المحدد غير صالح.',
'order_not_yours' => 'الطلب المحدد لا ينتمي إليك.',
```

---

### Reason Validation
```php
'reason_required' => 'The reason field is required.',
'reason_max' => 'The reason must not exceed :max characters.',
```

**Arabic:**
```php
'reason_required' => 'حقل السبب مطلوب.',
'reason_max' => 'يجب ألا يتجاوز السبب :max حرفاً.',
```

---

### Items Validation
```php
'items_required' => 'At least one item is required.',
'items_min' => 'At least one item is required.',
```

**Arabic:**
```php
'items_required' => 'مطلوب عنصر واحد على الأقل.',
'items_min' => 'مطلوب عنصر واحد على الأقل.',
```

---

### Order Product Validation
```php
'order_product_required' => 'The order product is required.',
'order_product_invalid' => 'The selected order product is invalid.',
'order_product_not_in_order' => 'The selected order product does not belong to this order.',
'order_product_already_refunded' => 'This product has already been refunded.',
'order_product_pending_refund' => 'This product already has a pending refund request.',
```

**Arabic:**
```php
'order_product_required' => 'منتج الطلب مطلوب.',
'order_product_invalid' => 'منتج الطلب المحدد غير صالح.',
'order_product_not_in_order' => 'منتج الطلب المحدد لا ينتمي إلى هذا الطلب.',
'order_product_already_refunded' => 'تم استرجاع هذا المنتج بالفعل.',
'order_product_pending_refund' => 'هذا المنتج لديه بالفعل طلب استرجاع قيد الانتظار.',
```

---

### Quantity Validation
```php
'quantity_required' => 'The quantity is required.',
'quantity_integer' => 'The quantity must be an integer.',
'quantity_min' => 'The quantity must be at least :min.',
'quantity_exceeds_ordered' => 'The refund quantity cannot exceed the ordered quantity (:quantity).',
```

**Arabic:**
```php
'quantity_required' => 'الكمية مطلوبة.',
'quantity_integer' => 'يجب أن تكون الكمية رقماً صحيحاً.',
'quantity_min' => 'يجب أن تكون الكمية :min على الأقل.',
'quantity_exceeds_ordered' => 'لا يمكن أن تتجاوز كمية الاسترجاع الكمية المطلوبة (:quantity).',
```

---

### Status Validation
```php
'status_required' => 'The status field is required.',
'status_invalid' => 'The selected status is invalid.',
```

**Arabic:**
```php
'status_required' => 'حقل الحالة مطلوب.',
'status_invalid' => 'الحالة المحددة غير صالحة.',
```

---

### Notes Validation
```php
'notes_max' => 'The notes must not exceed :max characters.',
'customer_notes_max' => 'The customer notes must not exceed :max characters.',
'admin_notes_max' => 'The admin notes must not exceed :max characters.',
'vendor_notes_max' => 'The vendor notes must not exceed :max characters.',
'rejection_reason_required' => 'The rejection reason is required.',
'rejection_reason_max' => 'The rejection reason must not exceed :max characters.',
```

**Arabic:**
```php
'notes_max' => 'يجب ألا تتجاوز الملاحظات :max حرفاً.',
'customer_notes_max' => 'يجب ألا تتجاوز ملاحظات العميل :max حرفاً.',
'admin_notes_max' => 'يجب ألا تتجاوز ملاحظات الإدارة :max حرفاً.',
'vendor_notes_max' => 'يجب ألا تتجاوز ملاحظات المورد :max حرفاً.',
'rejection_reason_required' => 'سبب الرفض مطلوب.',
'rejection_reason_max' => 'يجب ألا يتجاوز سبب الرفض :max حرفاً.',
```

---

### Settings Validation
```php
'refund_processing_days_required' => 'The refund processing days field is required.',
'refund_processing_days_integer' => 'The refund processing days must be an integer.',
'refund_processing_days_min' => 'The refund processing days must be at least :min.',
'refund_processing_days_max' => 'The refund processing days must not exceed :max.',
'customer_pays_return_shipping_boolean' => 'The customer pays return shipping field must be true or false.',
```

**Arabic:**
```php
'refund_processing_days_required' => 'حقل أيام معالجة الاسترجاع مطلوب.',
'refund_processing_days_integer' => 'يجب أن تكون أيام معالجة الاسترجاع رقماً صحيحاً.',
'refund_processing_days_min' => 'يجب أن تكون أيام معالجة الاسترجاع :min على الأقل.',
'refund_processing_days_max' => 'يجب ألا تتجاوز أيام معالجة الاسترجاع :max.',
'customer_pays_return_shipping_boolean' => 'يجب أن يكون حقل العميل يدفع شحن الإرجاع صحيحاً أو خطأ.',
```

---

## Updated Validation Request Files

### API Requests

#### 1. StoreRefundRequestRequest
**File:** `Modules/Refund/app/Http/Requests/Api/StoreRefundRequestRequest.php`

**Updated:**
- All `messages()` method strings → Translation keys
- Custom validation closure messages → Translation keys

**Example:**
```php
// Before
'order_id.required' => 'The order field is required.',

// After
'order_id.required' => trans('refund::refund.validation.order_required'),
```

**Custom Closures:**
```php
// Before
$fail('The selected order does not belong to you.');

// After
$fail(trans('refund::refund.validation.order_not_yours'));
```

---

#### 2. UpdateRefundStatusRequest
**File:** `Modules/Refund/app/Http/Requests/Api/UpdateRefundStatusRequest.php`

**Updated:**
- All `messages()` method strings → Translation keys

---

### Dashboard Requests (Already Translated)

#### 1. UpdateRefundSettingRequest
**File:** `Modules/Refund/app/Http/Requests/UpdateRefundSettingRequest.php`

**Status:** ✅ Already using translations
- Uses `trans('validation.*')` for standard messages
- Uses `trans('refund::refund.fields.*')` for attributes

---

#### 2. RejectRefundRequest
**File:** `Modules/Refund/app/Http/Requests/RejectRefundRequest.php`

**Status:** ✅ Already using translations

---

#### 3. ChangeRefundStatusRequest
**File:** `Modules/Refund/app/Http/Requests/ChangeRefundStatusRequest.php`

**Status:** ✅ Already using translations

---

#### 4. UpdateRefundNotesRequest
**File:** `Modules/Refund/app/Http/Requests/UpdateRefundNotesRequest.php`

**Status:** ✅ Already using translations

---

## Usage Examples

### API Error Response (English)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "order_id": ["The order field is required."],
    "items.0.order_product_id": ["This product has already been refunded."],
    "items.0.quantity": ["The refund quantity cannot exceed the ordered quantity (5)."]
  }
}
```

### API Error Response (Arabic)
```json
{
  "success": false,
  "message": "فشل التحقق",
  "errors": {
    "order_id": ["حقل الطلب مطلوب."],
    "items.0.order_product_id": ["تم استرجاع هذا المنتج بالفعل."],
    "items.0.quantity": ["لا يمكن أن تتجاوز كمية الاسترجاع الكمية المطلوبة (5)."]
  }
}
```

---

## Translation Parameters

### Dynamic Values
Some translations use parameters for dynamic values:

```php
// English
'reason_max' => 'The reason must not exceed :max characters.'

// Usage
trans('refund::refund.validation.reason_max', ['max' => 500])

// Output
"The reason must not exceed 500 characters."
```

```php
// Arabic
'reason_max' => 'يجب ألا يتجاوز السبب :max حرفاً.'

// Usage
trans('refund::refund.validation.reason_max', ['max' => 500])

// Output
"يجب ألا يتجاوز السبب 500 حرفاً."
```

---

## Complete Translation Structure

```php
'refund::refund' => [
    'titles' => [...],
    'fields' => [...],
    'statuses' => [...],
    'actions' => [...],
    'messages' => [...],
    'errors' => [...],
    'help' => [...],
    'notifications' => [...],
    'validation' => [
        // 25 validation keys
        'order_required',
        'order_invalid',
        'order_not_yours',
        'reason_required',
        'reason_max',
        'customer_notes_max',
        'items_required',
        'items_min',
        'order_product_required',
        'order_product_invalid',
        'order_product_not_in_order',
        'order_product_already_refunded',
        'order_product_pending_refund',
        'quantity_required',
        'quantity_integer',
        'quantity_min',
        'quantity_exceeds_ordered',
        'item_reason_max',
        'status_required',
        'status_invalid',
        'notes_max',
        'rejection_reason_required',
        'rejection_reason_max',
        'admin_notes_max',
        'vendor_notes_max',
        'refund_processing_days_required',
        'refund_processing_days_integer',
        'refund_processing_days_min',
        'refund_processing_days_max',
        'customer_pays_return_shipping_boolean',
    ],
]
```

---

## Testing Translations

### Test English
```bash
# Set locale to English
app()->setLocale('en');

# Test validation
$request = new StoreRefundRequestRequest();
$validator = Validator::make([], $request->rules());
$errors = $validator->errors();

// Should return English messages
```

### Test Arabic
```bash
# Set locale to Arabic
app()->setLocale('ar');

# Test validation
$request = new StoreRefundRequestRequest();
$validator = Validator::make([], $request->rules());
$errors = $validator->errors();

// Should return Arabic messages
```

---

## Benefits

### 1. Multilingual Support
- Full English and Arabic support
- Easy to add more languages
- Consistent translations

### 2. Maintainability
- Centralized translation management
- Easy to update messages
- No hardcoded strings

### 3. User Experience
- Users see messages in their language
- Clear, localized error messages
- Professional appearance

### 4. Consistency
- Same translation keys across module
- Consistent message format
- Reusable translations

---

## Summary

### Files Updated:
- ✅ `Modules/Refund/lang/en/refund.php` - Added 25 validation keys
- ✅ `Modules/Refund/lang/ar/refund.php` - Added 25 validation keys
- ✅ `StoreRefundRequestRequest.php` - All messages translated
- ✅ `UpdateRefundStatusRequest.php` - All messages translated

### Already Translated:
- ✅ `UpdateRefundSettingRequest.php`
- ✅ `RejectRefundRequest.php`
- ✅ `ChangeRefundStatusRequest.php`
- ✅ `UpdateRefundNotesRequest.php`

### Total Translation Keys Added: 25
### Languages Supported: English, Arabic
### Validation Requests Updated: 6

All validation messages in the Refund module are now fully translated and support both English and Arabic languages!

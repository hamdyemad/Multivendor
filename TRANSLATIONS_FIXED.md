# Translations Fixed - Request Quotations

## ✅ Translations Added

### English (`lang/en/common.php`)
```php
'select_all' => 'Select All',
'deselect_all' => 'Deselect All',
'sending' => 'Sending',
'address' => 'Address',
'notes' => 'Notes',
```

### Arabic (`lang/ar/common.php`)
```php
'select_all' => 'تحديد الكل',
'deselect_all' => 'إلغاء تحديد الكل',
'sending' => 'جاري الإرسال',
'address' => 'العنوان',
'notes' => 'ملاحظات',
```

### Request Quotation Translations (`Modules/Order/lang/*/request-quotation.php`)

#### English
```php
'customer_name' => 'Customer Name',
'available_vendors' => 'Available Vendors',
'please_select_vendors' => 'Please select at least one vendor',
'send_to_vendors' => 'Send to Vendors',
```

#### Arabic
```php
'customer_name' => 'اسم العميل',
'available_vendors' => 'التجار المتاحون',
'please_select_vendors' => 'الرجاء اختيار تاجر واحد على الأقل',
'send_to_vendors' => 'إرسال للتجار',
```

## 📝 Already Existing Translations

These were already in the system:
- `common.back` → "Back" / "رجوع"
- `common.email` → "Email" / "البريد الإلكتروني"
- `common.phone` → "Phone" / "الهاتف"
- `common.success` → "Success" / "نجاح"
- `common.error` → "Error" / "خطأ"
- `common.warning` → "Warning" / "تحذير"
- `common.saving` → "Saving" / "جاري الحفظ"

## 🎯 Usage in Views

All translations are now properly used in the select-vendors view:

```blade
{{ __('common.select_all') }}
{{ __('common.deselect_all') }}
{{ __('common.back') }}
{{ __('common.sending') }}
{{ __('common.email') }}
{{ __('common.phone') }}
{{ __('common.address') }}
{{ __('common.notes') }}
{{ __('common.success') }}
{{ __('common.error') }}
{{ __('common.warning') }}
{{ __('order::request-quotation.customer_name') }}
{{ __('order::request-quotation.available_vendors') }}
{{ __('order::request-quotation.please_select_vendors') }}
{{ __('order::request-quotation.send_to_vendors') }}
```

## ✅ Result

All translations are now complete and working correctly in both English and Arabic!

Clear cache to see the changes:
```bash
php artisan cache:clear
php artisan config:clear
```

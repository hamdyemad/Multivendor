# Country Code in URL Implementation

## Overview
تم تعديل البنية لإضافة كود الدولة في الـ URL بصيغة: `http://127.0.0.1:8000/en/eg/admin/dashboard`

حيث:
- `en` = اللغة (Language)
- `eg` = كود الدولة (Country Code)  
- `admin/dashboard` = المسار (Route)

**Status**: ✅ COMPLETE - الـ URL الآن بالصيغة الصحيحة

---

## Files Modified

### 1. **RouteServiceProvider.php**
**الملف**: `app/Providers/RouteServiceProvider.php`

**التغييرات**:
- تم تعديل admin routes prefix ليشمل `{countryCode}` parameter
- تم تفعيل `setUrlDefaults()` method لتعيين country code افتراضي
- الـ URL format الجديد: `/{locale}/{countryCode}/admin/...`

```php
// Admin routes with authentication and country code
Route::middleware(['web', 'auth', ...])
    ->prefix(LaravelLocalization::setLocale() . '/{countryCode}')
    ->where('countryCode', '[a-z]{2}')
    ->group(function() {
        require base_path('routes/admin.php');
    });
```

### 2. **SetCountryCodeFromUrl.php** (جديد)
**الملف**: `app/Http/Middleware/SetCountryCodeFromUrl.php`

**الوظيفة**:
- استخراج country code من الـ URL
- التحقق من صحة الكود
- حفظ الكود في الـ session

### 3. **Kernel.php**
**الملف**: `app/Http/Kernel.php`

**التغييرات**:
- إضافة `SetCountryCodeFromUrl::class` إلى global middleware

### 4. **web.php** (Routes)
**الملف**: `routes/web.php`

**التغييرات**:
- تعديل `switch-country` route لتحديث الـ URL بشكل صحيح
- الآن يستبدل segment 1 (country code) بالكود الجديد

```php
Route::get('/switch-country/{countryCode}', function($countryCode) {
    // ... logic to replace country code in URL
    // URL format: /{locale}/{countryCode}/admin/...
    if (count($segments) >= 2) {
        $segments[1] = strtolower($countryCode);
    }
    // Redirect to new URL
});
```

### 5. **_country_selector.blade.php**
**الملف**: `resources/views/partials/top_nav/_country_selector.blade.php`

**التغييرات**:
- تحديث `setCountry()` JavaScript function
- الآن تستخرج الـ URL الحالية وتستبدل country code
- تحافظ على باقي المسار (path)

```javascript
function setCountry(countryCode) {
    const currentUrl = window.location.pathname;
    const segments = currentUrl.split('/').filter(s => s);
    
    // Replace segment 1 (country code)
    if (segments.length >= 2) {
        segments[1] = countryCode.toLowerCase();
    }
    
    const newUrl = '/' + segments.join('/');
    window.location.href = newUrl;
}
```

---

## Helper Functions

### في `app/Helpers/functions.php`:

#### 1. **routeWithCountryCode()**
```php
routeWithCountryCode('admin.dashboard', [], true)
// Returns: /en/eg/admin/dashboard
```

#### 2. **getCountryCode()**
```php
getCountryCode()
// Returns: 'eg' (من session أو default)
```

#### 3. **currency()**
```php
currency()
// Returns: رمز العملة للدولة الحالية
```

#### 4. **current_country()**
```php
current_country()
// Returns: Country model للدولة الحالية
```

---

## URL Examples

### Before (القديم):
```
http://127.0.0.1:8000/en/admin/dashboard
http://127.0.0.1:8000/ar/admin/dashboard
```

### After (الجديد):
```
http://127.0.0.1:8000/en/eg/admin/dashboard
http://127.0.0.1:8000/en/sa/admin/dashboard
http://127.0.0.1:8000/ar/eg/admin/dashboard
http://127.0.0.1:8000/ar/sa/admin/dashboard
```

---

## How It Works

### 1. **عند الوصول لـ Admin Dashboard**:
```
User visits: http://127.0.0.1:8000/en/eg/admin/dashboard
↓
SetCountryCodeFromUrl middleware extracts 'eg' from segment 2
↓
Stores 'EG' in session
↓
setUrlDefaults() sets URL defaults with countryCode = 'eg'
↓
Page loads with country code in URL
```

### 2. **عند تغيير الدولة من السيليكت**:
```
User clicks on country selector (e.g., SA)
↓
setCountry('SA') JavaScript function called
↓
Extracts current URL: /en/eg/admin/dashboard
↓
Replaces segment 1: /en/sa/admin/dashboard
↓
Redirects to new URL
↓
Middleware updates session with new country code
```

### 3. **عند الضغط على رابط**:
```
{{ route('admin.dashboard') }}
↓
URL::defaults() includes countryCode = 'eg'
↓
Returns: /en/eg/admin/dashboard
```

---

## Database Queries

### الدول المتاحة:
```php
// Get all active countries
$countries = Country::where('active', 1)->get();

// Get specific country
$country = Country::where('code', 'EG')->first();
```

---

## Session Storage

### Country Code في Session:
```php
// Set
session(['country_code' => 'EG']);

// Get
$code = session('country_code'); // 'EG'

// Get with default
$code = session('country_code', 'EG'); // 'EG'
```

---

## Testing

### اختبر الـ URLs:

1. **English + Egypt**:
   ```
   http://127.0.0.1:8000/en/eg/admin/dashboard
   ```

2. **English + Saudi Arabia**:
   ```
   http://127.0.0.1:8000/en/sa/admin/dashboard
   ```

3. **Arabic + Egypt**:
   ```
   http://127.0.0.1:8000/ar/eg/admin/dashboard
   ```

4. **Change Country**:
   - اضغط على country selector
   - اختر دولة جديدة
   - تحقق من تحديث الـ URL

---

## Troubleshooting

### المشكلة: URL لا يتحدث عند تغيير الدولة

**الحل**:
1. تأكد من تفعيل JavaScript
2. تحقق من browser console للأخطاء
3. تأكد من وجود دول نشطة في قاعدة البيانات

### المشكلة: Country code لا يُحفظ في Session

**الحل**:
1. تأكد من تفعيل middleware `SetCountryCodeFromUrl`
2. تحقق من أن الـ URL يحتوي على country code صحيح
3. تأكد من أن الدولة موجودة في قاعدة البيانات

---

## Notes

- الـ country code يجب أن يكون 2 حروف صغيرة في الـ URL
- الـ country code يُحفظ في session بأحرف كبيرة (EG, SA, etc.)
- الـ default country code هو 'EG'
- جميع الـ admin routes تتطلب country code في الـ URL

---

## Status: ✅ COMPLETE

تم تنفيذ النظام بنجاح. الـ URLs الآن بصيغة: `/en/eg/admin/dashboard`

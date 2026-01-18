# استخدام الكومبوننتس في صفحة Refund Requests

## ✅ تم الاستخدام بنجاح!

الكومبوننتس العامة (Global Components) مستخدمة الآن في صفحة `refund-requests/index.blade.php`

## 🎯 الكومبوننتس المستخدمة

### 1. x-breadcrumb
```blade
<x-breadcrumb :items="[
    [
        'title' => trans('dashboard.title'),
        'url' => route('admin.dashboard'),
        'icon' => 'uil uil-estate',
    ],
    ['title' => trans('menu.refunds.title')],
]" />
```
**الوظيفة:** عرض مسار التنقل (Dashboard > Refunds)

---

### 2. x-datatable-wrapper
```blade
<x-datatable-wrapper
    :title="trans('menu.refunds.all')"
    icon="uil uil-redo"
    :showExport="false"
    tableId="refundsDataTable">
    
    <x-slot name="filters">...</x-slot>
    
    <thead>...</thead>
    <tbody></tbody>
</x-datatable-wrapper>
```
**الوظيفة:** 
- يلف الجدول بالكامل
- يعرض العنوان والأيقونة
- يوفر مكان للفلاتر
- يدير شكل الجدول

---

### 3. x-datatable-filters-advanced
```blade
<x-datatable-filters-advanced
    :searchPlaceholder="trans('refund::refund.fields.refund_number')"
    :filters="[
        [
            'name' => 'status_filter',
            'id' => 'status_filter',
            'label' => trans('refund::refund.fields.status'),
            'icon' => 'uil uil-check-circle',
            'options' => [...],
            'selected' => request('status'),
            'placeholder' => __('common.all'),
        ],
    ]"
    :showDateFilters="true"
/>
```
**الوظيفة:**
- حقل البحث
- فلتر الحالة (Status)
- فلتر التاريخ من/إلى
- أزرار البحث والإعادة

---

### 4. x-loading-overlay
```blade
<x-loading-overlay />
```
**الوظيفة:** شاشة التحميل

---

## 📊 هيكل الصفحة

```
index.blade.php
├── Breadcrumb (مسار التنقل)
├── DataTable Wrapper (غلاف الجدول)
│   ├── Filters (الفلاتر)
│   │   ├── Search (البحث)
│   │   ├── Status Filter (فلتر الحالة)
│   │   └── Date Filters (فلاتر التاريخ)
│   └── Table (الجدول)
│       ├── Headers (العناوين)
│       │   ├── #
│       │   ├── رقم الاسترجاع
│       │   ├── رقم الطلب
│       │   ├── العميل
│       │   ├── المورد (للأدمن فقط)
│       │   ├── المبلغ
│       │   ├── الحالة
│       │   ├── التاريخ
│       │   └── الإجراءات
│       └── Body (محتوى الجدول - يتم ملؤه بالـ DataTable)
└── Scripts (الجافاسكريبت)
    ├── تهيئة Custom Select
    ├── تهيئة DataTable
    ├── معالجات البحث
    ├── معالجات الفلاتر
    └── معالج إعادة التعيين
```

## 🎨 المميزات

### 1. البحث المباشر (Live Search)
- البحث يعمل تلقائياً بعد 600ms من التوقف عن الكتابة
- يبحث في رقم الاسترجاع، اسم العميل، واسم المورد

### 2. الفلاتر
- **فلتر الحالة:** يعرض جميع حالات الاسترجاع
  - قيد الانتظار (Pending)
  - مقبول (Approved)
  - قيد التنفيذ (In Progress)
  - تم الاستلام (Picked Up)
  - تم الاسترجاع (Refunded)
  - مرفوض (Rejected)

- **فلتر التاريخ:** من تاريخ - إلى تاريخ

### 3. الجدول
- **Pagination:** 10, 25, 50, 100 صف
- **Sorting:** ترتيب حسب التاريخ (الأحدث أولاً)
- **RTL Support:** دعم العربية
- **Responsive:** متجاوب مع جميع الشاشات

### 4. الإجراءات
- زر عرض التفاصيل لكل طلب استرجاع

## 🔧 الإعدادات المستخدمة

### DataTable Configuration
```javascript
{
    processing: true,          // عرض رسالة "جاري المعالجة"
    serverSide: true,          // معالجة البيانات من السيرفر
    searching: false,          // تعطيل البحث الافتراضي (نستخدم بحث مخصص)
    pageLength: 10,            // 10 صفوف في الصفحة
    order: [[7, 'desc']],      // ترتيب حسب عمود التاريخ (الأحدث أولاً)
}
```

### AJAX Configuration
```javascript
ajax: {
    url: '/admin/refunds/datatable',
    data: {
        search: 'قيمة البحث',
        status_filter: 'الحالة المختارة',
        created_date_from: 'من تاريخ',
        created_date_to: 'إلى تاريخ',
        per_page: 'عدد الصفوف'
    }
}
```

## 📝 الأعمدة (Columns)

| # | العمود | النوع | قابل للترتيب | قابل للبحث |
|---|--------|------|--------------|------------|
| 1 | # | رقم | ❌ | ❌ |
| 2 | رقم الاسترجاع | نص | ❌ | ✅ |
| 3 | رقم الطلب | نص | ❌ | ❌ |
| 4 | العميل | نص | ❌ | ❌ |
| 5 | المورد | نص | ❌ | ❌ |
| 6 | المبلغ | رقم | ❌ | ❌ |
| 7 | الحالة | Badge | ❌ | ❌ |
| 8 | التاريخ | تاريخ | ❌ | ❌ |
| 9 | الإجراءات | أزرار | ❌ | ❌ |

## 🎯 كيفية العمل

### 1. عند تحميل الصفحة
```
1. تهيئة Custom Select للفلاتر
2. قراءة المعاملات من الـ URL
3. ملء حقول الفلاتر بالقيم الموجودة
4. تهيئة DataTable
5. طلب البيانات من السيرفر
6. عرض البيانات في الجدول
```

### 2. عند البحث
```
1. المستخدم يكتب في حقل البحث
2. انتظار 600ms
3. إعادة تحميل الجدول بالبحث الجديد
4. تحديث الـ URL
```

### 3. عند تغيير الفلتر
```
1. المستخدم يختار قيمة من الفلتر
2. إعادة تحميل الجدول فوراً
3. تحديث الـ URL
```

### 4. عند الضغط على "بحث"
```
1. جمع جميع قيم الفلاتر
2. تحديث الـ URL
3. إعادة تحميل الجدول
```

### 5. عند الضغط على "إعادة تعيين"
```
1. مسح جميع حقول الفلاتر
2. مسح الـ URL
3. إعادة تحميل الجدول بدون فلاتر
```

## 🚀 الخطوات التالية

يمكن الآن استخدام نفس الكومبوننتس في:
- ✅ صفحة عرض الطلبات (Orders)
- ✅ صفحة عرض المنتجات (Products)
- ✅ صفحة عرض العملاء (Customers)
- ✅ صفحة عرض الموردين (Vendors)
- ✅ أي صفحة أخرى تحتوي على جدول

## 📍 مكان الملفات

### الكومبوننتس العامة
```
resources/views/components/
├── datatable-wrapper.blade.php
├── datatable-filters-advanced.blade.php
├── datatable-actions.blade.php
└── datatable-script.blade.php
```

### صفحة الاسترجاعات
```
Modules/Refund/resources/views/refund-requests/
└── index.blade.php
```

## ✨ الخلاصة

الصفحة الآن تستخدم الكومبوننتس العامة بنجاح! 🎉

- ✅ كود أنظف وأقصر
- ✅ سهولة الصيانة
- ✅ إمكانية إعادة الاستخدام
- ✅ تناسق في التصميم
- ✅ جاهزة للاستخدام في باقي الموديولات

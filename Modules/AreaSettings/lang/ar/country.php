<?php

return [
    // Page Titles
    'countries_management' => 'إدارة الدول',
    'create_country' => 'إنشاء دولة',
    'edit_country' => 'تعديل دولة',
    'view_country' => 'عرض الدولة',
    'country_details' => 'تفاصيل الدولة',

    // Form Fields
    'name' => 'الاسم',
    'name_english' => 'الاسم',
    'name_arabic' => 'الاسم',
    'country_code' => 'كود الدولة',
    'phone_code' => 'كود الهاتف',
    'activation' => 'التفعيل',
    'status' => 'الحالة',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'cities_count' => 'عدد المدن',
    'cities' => 'مدن',

    // Placeholders
    'search_placeholder' => 'البحث بالاسم أو الكود...',
    'enter_country_name_english' => 'أدخل اسم الدولة بالإنجليزية',
    'enter_country_name_arabic' => 'أدخل اسم الدولة بالعربية',
    'enter_country_code' => 'مثال: US, EG, SA',
    'enter_phone_code' => 'مثال: +1, +20, +966',
    'search_by_name_or_code' => 'البحث بالاسم أو الكود',

    // Table Headers
    'sr_no' => '#',
    'name_en' => 'الاسم (إنجليزي)',
    'name_ar' => 'الاسم (عربي)',
    'code' => 'الكود',
    'phone' => 'كود الهاتف',
    'action' => 'الإجراءات',
    'created_at' => 'تاريخ الإنشاء',

    // Buttons
    'add_country' => 'إضافة دولة',
    'create_country' => 'إنشاء دولة',
    'update_country' => 'تحديث الدولة',
    'save_country' => 'حفظ الدولة',
    'cancel' => 'إلغاء',
    'save' => 'حفظ',
    'back_to_list' => 'العودة للقائمة',
    'reset_filters' => 'إعادة تعيين',

    // Table & Filters
    'all_status' => 'جميع الحالات',
    'all' => 'الكل',
    'no_countries_found' => 'لا توجد دول',
    'showing' => 'عرض',
    'to' => 'إلى',
    'of' => 'من',
    'entries' => 'مدخلات',

    // Messages
    'country_created_successfully' => 'تم إنشاء الدولة بنجاح',
    'country_updated_successfully' => 'تم تحديث الدولة بنجاح',
    'country_deleted_successfully' => 'تم حذف الدولة بنجاح',
    'country_created' => 'تم إنشاء الدولة بنجاح',
    'country_updated' => 'تم تحديث الدولة بنجاح',
    'country_deleted' => 'تم حذف الدولة بنجاح',
    'error_creating_country' => 'خطأ في إنشاء الدولة',
    'error_updating_country' => 'خطأ في تحديث الدولة',
    'error_deleting_country' => 'خطأ في حذف الدولة',
    'country_not_found' => 'الدولة غير موجودة',
    'no_countries_found' => 'لا توجد دول',

    // Delete Modal
    'confirm_delete' => 'تأكيد الحذف',
    'delete_confirmation' => 'هل أنت متأكد من حذف هذه الدولة؟',
    'delete_country' => 'حذف الدولة',

    // Validation & Errors
    'validation_errors' => 'أخطاء التحقق من الصحة',
    'error_occurred' => 'حدث خطأ. يرجى المحاولة مرة أخرى.',
    'processing' => 'جاري المعالجة...',
    'translations' => 'الترجمات',

    // Validation Messages
    'validation' => [
        'name_en_required' => 'الاسم الإنجليزي مطلوب',
        'name_ar_required' => 'الاسم العربي مطلوب',
        'code_required' => 'كود الدولة مطلوب',
        'code_unique' => 'كود الدولة موجود بالفعل',
        'code_max' => 'كود الدولة يجب ألا يتجاوز 3 أحرف',
        'phone_code_required' => 'كود الهاتف مطلوب',
        'phone_code_max' => 'كود الهاتف يجب ألا يتجاوز 10 أحرف',
        'phone_code_format' => 'كود الهاتف يجب أن يبدأ بعلامة الزائد (+)',
        'currency_required' => 'العملة مطلوبة',
        'currency_exists' => 'العملة المحددة غير صالحة',
    ],

    // Filters
    'search_by_name_or_code' => 'البحث بالاسم أو الكود',
    'all' => 'الكل',
    'filter' => 'تصفية',

    // Confirmation
    'confirm_delete' => 'تأكيد الحذف',
    'delete_warning' => 'هل أنت متأكد من حذف هذه الدولة؟',
    'delete_country' => 'حذف الدولة',

    // Info
    'basic_information' => 'المعلومات الأساسية',
    'countries_list' => 'قائمة الدول',
    'currency' => 'العملة',
    'select_currency' => 'اختر العملة',
    'view_cities' => 'عرض المدن',
    'add_city' => 'إضافة مدينة',
    'updated_at' => 'تاريخ التحديث',

    // Table headers
    'sr_no' => '#',
    'name_english' => 'الاسم (إنجليزي)',
    'name_arabic' => 'الاسم (عربي)',
    'action' => 'الإجراءات',
    'no_countries_found' => 'لم يتم العثور على دول',

    // Pagination
    'showing' => 'عرض',
    'to' => 'إلى',
    'of' => 'من',
    'entries' => 'إدخال',
    'previous' => 'السابق',
    'next' => 'التالي',

    // Status Change Modal
    'change_status' => 'تغيير الحالة',
    'change_status_confirmation' => 'هل أنت متأكد من تغيير حالة هذه الدولة؟',
    'current_status' => 'الحالة الحالية',
    'new_status' => 'الحالة الجديدة',
    'confirm_status_change' => 'تأكيد التغيير',
    'status_changed_successfully' => 'تم تغيير حالة الدولة بنجاح',
    'error_changing_status' => 'خطأ في تغيير حالة الدولة',
    'status_already_set' => 'الدولة مضبوطة بالفعل على هذه الحالة',
];


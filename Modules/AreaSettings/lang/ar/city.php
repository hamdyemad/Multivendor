<?php

return [
    // Page Titles
    'cities_management' => 'إدارة المدن',
    'create_city' => 'إنشاء مدينة',
    'edit_city' => 'تعديل مدينة',
    'view_city' => 'عرض المدينة',
    'city_details' => 'تفاصيل المدينة',

    // Form Fields
    'id' => 'الرقم',
    'name' => 'الاسم',
    'name_english' => 'الاسم',
    'name_arabic' => 'الاسم',
    'country' => 'الدولة',
    'activation' => 'التفعيل',
    'status' => 'الحالة',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'regions_count' => 'عدد المناطق',
    'regions' => 'مناطق',

    // Placeholders
    'enter_city_name_english' => 'أدخل اسم المدينة بالإنجليزية',
    'enter_city_name_arabic' => 'أدخل اسم المدينة بالعربية',
    'search_by_name' => 'البحث بالاسم',
    'search_placeholder' => 'البحث عن المدن بالاسم...',
    'select_country' => 'اختر الدولة',
    'all_countries' => 'جميع الدول',
    'all_status' => 'جميع الحالات',

    // Table Headers
    'name_en' => 'الاسم (إنجليزي)',
    'name_ar' => 'الاسم (عربي)',
    'created_at' => 'تاريخ الإنشاء',
    'updated_at' => 'تاريخ التحديث',

    // Buttons
    'add_city' => 'إضافة مدينة',
    'update_city' => 'تحديث المدينة',
    'cancel' => 'إلغاء',
    'save' => 'حفظ',
    'back_to_list' => 'العودة للقائمة',

    // Table
    'all' => 'الكل',
    'no_cities_found' => 'لا توجد مدن',

    // Messages
    'city_created' => 'تم إنشاء المدينة بنجاح',
    'city_updated' => 'تم تحديث المدينة بنجاح',
    'city_deleted' => 'تم حذف المدينة بنجاح',
    'error_creating_city' => 'خطأ في إنشاء المدينة',
    'error_updating_city' => 'خطأ في تحديث المدينة',
    'error_deleting_city' => 'خطأ في حذف المدينة',
    'error_occurred' => 'حدث خطأ. الرجاء المحاولة مرة أخرى.',
    'city_not_found' => 'المدينة غير موجودة',
    'processing' => 'جاري المعالجة...',

    // Delete Modal
    'confirm_delete' => 'تأكيد الحذف',
    'delete_confirmation' => 'هل أنت متأكد من حذف هذه المدينة؟',
    'delete_city' => 'حذف المدينة',

    // Validation
    'validation_errors' => 'أخطاء التحقق من الصحة',

    // Validation Messages
    'validation' => [
        'name_en_required' => 'الاسم الإنجليزي مطلوب',
        'name_en_unique' => 'اسم المدينة موجود بالفعل في الدولة المحددة',
        'name_ar_required' => 'الاسم العربي مطلوب',
        'name_ar_unique' => 'اسم المدينة موجود بالفعل في الدولة المحددة',
        'country_required' => 'الدولة مطلوبة',
        'country_exists' => 'الدولة المحددة غير موجودة',
    ],

    // Info
    'basic_information' => 'المعلومات الأساسية',
    'cities_list' => 'قائمة المدن',

    // Status Change Modal
    'change_status' => 'تغيير الحالة',
    'change_status_confirmation' => 'هل أنت متأكد من تغيير حالة هذه المدينة؟',
    'current_status' => 'الحالة الحالية',
    'new_status' => 'الحالة الجديدة',
    'confirm_status_change' => 'تأكيد التغيير',
    'status_changed_successfully' => 'تم تغيير حالة المدينة بنجاح',
    'error_changing_status' => 'خطأ في تغيير حالة المدينة',
    'status_already_set' => 'المدينة مضبوطة بالفعل على هذه الحالة',
];

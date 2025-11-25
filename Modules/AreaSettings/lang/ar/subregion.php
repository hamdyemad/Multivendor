<?php

return [
    // Page Titles
    'subregions_management' => 'إدارة المناطق الفرعية',
    'create_subregion' => 'إنشاء منطقة فرعية',
    'edit_subregion' => 'تعديل منطقة فرعية',
    'view_subregion' => 'عرض المنطقة الفرعية',
    'subregion_details' => 'تفاصيل المنطقة الفرعية',

    // Form Fields
    'id' => 'الرقم',
    'name' => 'الاسم',
    'name_english' => 'الاسم',
    'name_arabic' => 'الاسم',
    'country' => 'الدولة',
    'region' => 'المنطقة',
    'city' => 'المدينة',
    'activation' => 'التفعيل',
    'status' => 'الحالة',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'translations' => 'الترجمات',
    'hierarchy_navigation' => 'التنقل الهرمي',

    // Placeholders
    'enter_subregion_name_english' => 'أدخل اسم المنطقة الفرعية بالإنجليزية',
    'enter_subregion_name_arabic' => 'أدخل اسم المنطقة الفرعية بالعربية',
    'search_by_name' => 'البحث بالاسم',
    'search_placeholder' => 'البحث عن المناطق الفرعية بالاسم...',
    'select_region' => 'اختر المنطقة',
    'select_city' => 'اختر المدينة',
    'all_regions' => 'جميع المناطق',
    'all_status' => 'جميع الحالات',

    // Table Headers
    'name_en' => 'الاسم (إنجليزي)',
    'name_ar' => 'الاسم (عربي)',
    'created_at' => 'تاريخ الإنشاء',
    'updated_at' => 'تاريخ التحديث',
    'action' => 'الإجراءات',

    // Buttons
    'add_subregion' => 'إضافة منطقة فرعية',
    'update_subregion' => 'تحديث المنطقة الفرعية',
    'cancel' => 'إلغاء',
    'save' => 'حفظ',
    'back_to_list' => 'العودة للقائمة',

    // Table
    'all' => 'الكل',
    'no_subregions_found' => 'لا توجد مناطق فرعية',

    // Messages
    'subregion_created' => 'تم إنشاء المنطقة الفرعية بنجاح',
    'subregion_updated' => 'تم تحديث المنطقة الفرعية بنجاح',
    'subregion_deleted' => 'تم حذف المنطقة الفرعية بنجاح',
    'error_creating_subregion' => 'خطأ في إنشاء المنطقة الفرعية',
    'error_updating_subregion' => 'خطأ في تحديث المنطقة الفرعية',
    'error_deleting_subregion' => 'خطأ في حذف المنطقة الفرعية',
    'error_occurred' => 'حدث خطأ. الرجاء المحاولة مرة أخرى.',
    'processing' => 'جاري المعالجة...',
    'subregion_not_found' => 'المنطقة الفرعية غير موجودة',

    // Delete Modal
    'confirm_delete' => 'تأكيد الحذف',
    'delete_confirmation' => 'هل أنت متأكد من حذف هذه المنطقة الفرعية؟',
    'delete_subregion' => 'حذف المنطقة الفرعية',

    // Validation
    'validation_errors' => 'أخطاء التحقق من الصحة',

    // Validation Messages
    'region_required' => 'المنطقة مطلوبة',
    'region_exists' => 'المنطقة المحددة غير موجودة',
    'translations_required' => 'الترجمات مطلوبة',
    'name_required' => 'الاسم مطلوب',
    'name_string' => 'يجب أن يكون الاسم نصاً',
    'name_max' => 'يجب ألا يتجاوز الاسم 255 حرفاً',
    'validation' => [
        'name_en_required' => 'الاسم الإنجليزي مطلوب',
        'name_en_unique' => 'اسم المنطقة الفرعية موجود بالفعل في المنطقة المحددة',
        'name_ar_required' => 'الاسم العربي مطلوب',
        'name_ar_unique' => 'اسم المنطقة الفرعية موجود بالفعل في المنطقة المحددة',
        'name_unique' => 'اسم المنطقة الفرعية موجود بالفعل في المنطقة المحددة',
        'region_required' => 'المنطقة مطلوبة',
        'region_exists' => 'المنطقة المحددة غير موجودة',
    ],

    // Info
    'basic_information' => 'المعلومات الأساسية',
    'subregions_list' => 'قائمة المناطق الفرعية',

    // Status Change Modal
    'change_status' => 'تغيير الحالة',
    'change_status_confirmation' => 'هل أنت متأكد من تغيير حالة هذه المنطقة الفرعية؟',
    'current_status' => 'الحالة الحالية',
    'new_status' => 'الحالة الجديدة',
    'confirm_status_change' => 'تأكيد التغيير',
    'status_changed_successfully' => 'تم تغيير حالة المنطقة الفرعية بنجاح',
    'error_changing_status' => 'خطأ في تغيير حالة المنطقة الفرعية',
    'status_already_set' => 'المنطقة الفرعية مضبوطة بالفعل على هذه الحالة',
];

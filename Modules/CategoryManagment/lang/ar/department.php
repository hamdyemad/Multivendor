<?php

return [
    // Page Titles
    'departments_management' => 'إدارة الأقسام',
    'create_department' => 'إنشاء قسم',
    'edit_department' => 'تعديل القسم',
    'view_department' => 'عرض القسم',
    'department_details' => 'تفاصيل القسم',
    'activities' => 'الأنشطة',
    // Form Fields
    'name' => 'الاسم',
    'name_english' => 'الاسم بالانجليزية',
    'name_arabic' => 'الاسم بالعربية',
    'description' => 'الوصف',
    'description_english' => 'الوصف بالانجليزية',
    'department_code' => 'رمز القسم',
    'activation' => 'التفعيل',
    'view_status' => 'حالة العرض',
    'sort_number' => 'رقم الترتيب',
    'status' => 'الحالة',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'select_activities' => 'اختر الأنشطة',
    'commission' => 'العمولة (%)',
    'image' => 'صورة القسم',
    'icon' => 'ايقونة القسم',
    'click_to_upload_image' => 'انقر لتحميل صورة القسم',
    'click_to_upload_icon' => 'انقر لتحميل أيقونة القسم',
    'recommended_size' => 'الحجم الموصى به: 1538×402 بكسل',
    'recommended_size_for_icon' => 'الحجم الموصى به للايقونة: 112×112 بكسل',

    // Placeholders
    'enter_department_name_english' => 'أدخل اسم القسم بالإنجليزية',
    'enter_department_name_arabic' => 'أدخل اسم القسم بالعربية',
    'enter_department_code' => 'مثال: تقنية، موارد، مبيعات',
    'search_by_name_or_code' => 'البحث بالاسم أو الرمز',

    // Table Headers
    'name_en' => 'الاسم (إنجليزي)',
    'name_ar' => 'الاسم (عربي)',
    'code' => 'الرمز',
    'created_at' => 'تاريخ الإنشاء',
    'department_information' => 'معلومات القسم',

    // Buttons
    'add_department' => 'إضافة قسم',
    'update_department' => 'تحديث القسم',
    'cancel' => 'إلغاء',
    'save' => 'حفظ',
    'back_to_list' => 'العودة للقائمة',

    // Table
    'all' => 'الكل',
    'no_departments_found' => 'لم يتم العثور على أقسام',

    // Messages
    'department_created' => 'تم إنشاء القسم بنجاح',
    'department_updated' => 'تم تحديث القسم بنجاح',
    'department_deleted' => 'تم حذف القسم بنجاح',
    'error_creating_department' => 'خطأ في إنشاء القسم',
    'error_updating_department' => 'خطأ في تحديث القسم',
    'error_deleting_department' => 'خطأ في حذف القسم',
    'error_loading_departments' => 'خطأ في تحميل الأقسام',
    'error_loading_form' => 'خطأ في تحميل النموذج',
    'department_not_found' => 'القسم غير موجود',
    'search_by_name' => 'البحث بالاسم...',

    // Delete Modal
    'confirm_delete' => 'تأكيد الحذف',
    'delete_confirmation' => 'هل أنت متأكد من حذف هذا القسم؟',
    'delete_department' => 'حذف القسم',

    // Validation
    'validation_errors' => 'أخطاء التحقق',

    // Validation Messages
    'validation' => [
        'name_en_required' => 'الاسم بالإنجليزية مطلوب',
        'name_ar_required' => 'الاسم بالعربية مطلوب',
        'code_required' => 'رمز القسم مطلوب',
        'code_unique' => 'رمز القسم موجود مسبقاً',
        'code_max' => 'يجب ألا يتجاوز رمز القسم 50 حرفاً',
    ],

    // Filters
    'filter' => 'تصفية',

    // Info
    'basic_information' => 'المعلومات الأساسية',
    'departments_list' => 'قائمة الأقسام',

    // Status Change
    'change_status' => 'تغيير الحالة',
    'status_changed_successfully' => 'تم تغيير حالة القسم بنجاح',
    'error_changing_status' => 'خطأ في تغيير حالة القسم',
    'status_already_set' => 'القسم مضبوط بالفعل على هذه الحالة',

    // Validation Messages for Request
    'at_least_one_translation_required' => 'يجب توفير ترجمة واحدة على الأقل.',
    'name_required' => 'اسم القسم مطلوب.',
    'name_max_255' => 'اسم القسم قد لا يتجاوز 255 حرفاً.',

    // Reorder
    'reorder_success' => 'تم إعادة ترتيب الأقسام بنجاح',
    'error_reordering' => 'خطأ في إعادة ترتيب الأقسام',
];

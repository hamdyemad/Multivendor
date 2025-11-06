<?php

return [
    // Page Titles
    'regions_management' => 'إدارة المناطق',
    'create_region' => 'إنشاء منطقة',
    'edit_region' => 'تعديل منطقة',
    'view_region' => 'عرض المنطقة',
    'region_details' => 'تفاصيل المنطقة',

    // Form Fields
    'name' => 'الاسم',
    'name_english' => 'الاسم',
    'name_arabic' => 'الاسم',
    'country' => 'الدولة',
    'city' => 'المدينة',
    'activation' => 'التفعيل',
    'status' => 'الحالة',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'subregions_count' => 'عدد المناطق الفرعية',
    'subregions' => 'مناطق فرعية',

    // Placeholders
    'enter_region_name_english' => 'أدخل اسم المنطقة بالإنجليزية',
    'enter_region_name_arabic' => 'أدخل اسم المنطقة بالعربية',
    'search_by_name' => 'البحث بالاسم',
    'search_placeholder' => 'البحث عن المناطق بالاسم...',
    'select_city' => 'اختر المدينة',
    'all_cities' => 'جميع المدن',
    'all_status' => 'جميع الحالات',

    // Table Headers
    'name_en' => 'الاسم (إنجليزي)',
    'name_ar' => 'الاسم (عربي)',
    'created_at' => 'تاريخ الإنشاء',
    'updated_at' => 'تاريخ التحديث',

    // Buttons
    'add_region' => 'إضافة منطقة',
    'update_region' => 'تحديث المنطقة',
    'cancel' => 'إلغاء',
    'save' => 'حفظ',
    'back_to_list' => 'العودة للقائمة',

    // Table
    'all' => 'الكل',
    'no_regions_found' => 'لا توجد مناطق',

    // Messages
    'region_created' => 'تم إنشاء المنطقة بنجاح',
    'region_updated' => 'تم تحديث المنطقة بنجاح',
    'region_deleted' => 'تم حذف المنطقة بنجاح',
    'error_creating_region' => 'خطأ في إنشاء المنطقة',
    'error_updating_region' => 'خطأ في تحديث المنطقة',
    'error_deleting_region' => 'خطأ في حذف المنطقة',
    'error_occurred' => 'حدث خطأ. الرجاء المحاولة مرة أخرى.',
    'region_not_found' => 'المنطقة غير موجودة',
    'processing' => 'جاري المعالجة...',

    // Delete Modal
    'confirm_delete' => 'تأكيد الحذف',
    'delete_confirmation' => 'هل أنت متأكد من حذف هذه المنطقة؟',
    'delete_region' => 'حذف المنطقة',

    // Validation
    'validation_errors' => 'أخطاء التحقق من الصحة',
    
    // Validation Messages
    'validation' => [
        'name_en_required' => 'الاسم الإنجليزي مطلوب',
        'name_en_unique' => 'اسم المنطقة موجود بالفعل في المدينة المحددة',
        'name_ar_required' => 'الاسم العربي مطلوب',
        'name_ar_unique' => 'اسم المنطقة موجود بالفعل في المدينة المحددة',
        'name_unique' => 'اسم المنطقة موجود بالفعل في المدينة المحددة',
        'city_required' => 'المدينة مطلوبة',
        'city_exists' => 'المدينة المحددة غير موجودة',
    ],

    // Info
    'basic_information' => 'المعلومات الأساسية',
    'regions_list' => 'قائمة المناطق',
];

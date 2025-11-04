<?php

return [
    // Currency Management
    'currencies_management' => 'إدارة العملات',
    'add_currency' => 'إضافة عملة',
    'create_currency' => 'إنشاء عملة',
    'edit_currency' => 'تعديل العملة',
    'view_currency' => 'عرض العملة',
    'update_currency' => 'تحديث العملة',
    'delete_currency' => 'حذف العملة',
    'currency_details' => 'تفاصيل العملة',
    'no_currencies_found' => 'لم يتم العثور على عملات',
    
    // Fields
    'name' => 'الاسم',
    'name_english' => 'الاسم (الإنجليزية)',
    'name_arabic' => 'الاسم (العربية)',
    'currency_code' => 'رمز العملة',
    'currency_symbol' => 'رمز العملة',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'status' => 'الحالة',
    'all_status' => 'كل الحالات',
    'created_at' => 'تاريخ الإنشاء',
    'updated_at' => 'تاريخ التحديث',
    'dates' => 'التواريخ',
    
    // Placeholders
    'search_placeholder' => 'البحث بالاسم أو الرمز...',
    
    // Messages
    'created_successfully' => 'تم إنشاء العملة بنجاح',
    'updated_successfully' => 'تم تحديث العملة بنجاح',
    'deleted_successfully' => 'تم حذف العملة بنجاح',
    'error_creating' => 'خطأ في إنشاء العملة',
    'error_updating' => 'خطأ في تحديث العملة',
    'error_deleting' => 'خطأ في حذف العملة',
    'not_found' => 'العملة غير موجودة',
    
    // Actions
    'back_to_list' => 'العودة للقائمة',
    'confirm_delete' => 'تأكيد الحذف',
    'delete_confirmation' => 'هل أنت متأكد من رغبتك في حذف هذه العملة؟',
    'cancel' => 'إلغاء',
    
    // Additional
    'basic_information' => 'المعلومات الأساسية',
    'translations' => 'الترجمات',
    'validation_errors' => 'أخطاء التحقق',
    
    // Validation Messages
    'validation' => [
        'code_required' => 'رمز العملة مطلوب',
        'code_unique' => 'رمز العملة موجود بالفعل',
        'code_max' => 'رمز العملة يجب ألا يتجاوز 3 أحرف',
        'symbol_required' => 'رمز العملة مطلوب',
        'symbol_max' => 'رمز العملة يجب ألا يتجاوز 10 أحرف',
        'name_en_required' => 'الاسم (الإنجليزية) مطلوب',
        'name_ar_required' => 'الاسم (العربية) مطلوب',
    ],
];

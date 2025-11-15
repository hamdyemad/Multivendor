<?php

return [
    // Actions
    'actions' => [
        'login' => 'تسجيل الدخول',
        'logout' => 'تسجيل الخروج',
        'login_failed' => 'فشل تسجيل الدخول',
        'created' => 'إنشاء',
        'updated' => 'تحديث',
        'deleted' => 'حذف',
        'restored' => 'استعادة',
        'force_deleted' => 'حذف نهائي',
        'password_reset_requested' => 'طلب إعادة تعيين كلمة المرور',
        'password_reset_success' => 'نجح إعادة تعيين كلمة المرور',
        'password_reset_failed' => 'فشل إعادة تعيين كلمة المرور',
    ],

    // Descriptions
    'created_model' => 'تم إنشاء :model: :identifier',
    'updated_model' => 'تم تحديث :model: :identifier',
    'deleted_model' => 'تم حذف :model: :identifier',
    'restored_model' => 'تم استعادة :model: :identifier',
    'force_deleted_model' => 'تم الحذف النهائي لـ :model: :identifier',
    
    'login_success' => 'تم تسجيل الدخول بنجاح',
    'logout_success' => 'تم تسجيل الخروج',
    'login_failed_inactive' => 'فشل تسجيل الدخول - الحساب غير مفعل',
    'login_failed_blocked' => 'فشل تسجيل الدخول - الحساب محظور',
    'login_failed_credentials' => 'فشل تسجيل الدخول - بيانات غير صحيحة',
    
    'password_reset_sent' => 'تم إرسال رمز إعادة تعيين كلمة المرور إلى البريد الإلكتروني',
    'password_reset_email_failed' => 'فشل إرسال بريد إعادة تعيين كلمة المرور',
    'password_reset_invalid_code' => 'فشل إعادة تعيين كلمة المرور - رمز غير صحيح',
    'password_reset_expired_code' => 'فشل إعادة تعيين كلمة المرور - رمز منتهي الصلاحية',
    'password_reset_completed' => 'تم إعادة تعيين كلمة المرور بنجاح',

    // Model names
    'models' => [
        'Country' => 'دولة',
        'City' => 'مدينة',
        'Region' => 'منطقة',
        'SubRegion' => 'منطقة فرعية',
        'User' => 'مستخدم',
        'Currency' => 'عملة',
        'Department' => 'قسم',
        'Category' => 'فئة',
        'SubCategory' => 'فئة فرعية',
        'Product' => 'منتج',
        'Brand' => 'علامة تجارية',
        'Tax' => 'ضريبة',
        'Vendor' => 'بائع',
        'Role' => 'دور',
        'Permission' => 'صلاحية',
    ],
];
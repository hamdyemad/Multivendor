<?php

return [
    // General
    'order_management' => 'إدارة الطلبات',
    'orders_list' => 'قائمة الطلبات',
    'order_details' => 'تفاصيل الطلب',
    'create_order' => 'إنشاء طلب',
    'edit_order' => 'تعديل الطلب',
    'view_order' => 'عرض الطلب',
    'delete_order' => 'حذف الطلب',

    // Table Columns
    'order_id' => 'رقم الطلب',
    'customer_name' => 'اسم العميل',
    'customer_email' => 'بريد العميل الإلكتروني',
    'customer_phone' => 'هاتف العميل',
    'customer_address' => 'عنوان العميل',
    'total_price' => 'السعر الإجمالي',
    'items_count' => 'عدد العناصر',
    'stage' => 'المرحلة',
    'payment_type' => 'نوع الدفع',
    'order_from' => 'الطلب من',
    'created_at' => 'تاريخ الإنشاء',
    'updated_at' => 'تاريخ التحديث',

    // Filters
    'search_order_id_or_customer' => 'ابحث برقم الطلب أو اسم العميل...',
    'all_stages' => 'جميع المراحل',
    'created_from' => 'تم الإنشاء من',
    'created_until' => 'تم الإنشاء حتى',

    // Stage Management
    'change_order_stage' => 'تغيير مرحلة الطلب',
    'select_new_stage' => 'اختر المرحلة الجديدة',
    'select_stage' => 'اختر المرحلة',
    'update_stage' => 'تحديث المرحلة',
    'please_select_stage' => 'يرجى اختيار مرحلة',
    'updating_stage' => 'جاري تحديث المرحلة...',
    'stage_updated_successfully' => 'تم تحديث مرحلة الطلب بنجاح',
    'error_updating_stage' => 'خطأ في تحديث مرحلة الطلب',

    // Payment Types
    'cash_on_delivery' => 'الدفع عند الاستلام',
    'online_payment' => 'الدفع الإلكتروني',

    // Order From
    'web' => 'الويب',
    'ios' => 'تطبيق iOS',
    'android' => 'تطبيق Android',

    // Status Messages
    'order_created' => 'تم إنشاء الطلب بنجاح',
    'order_updated' => 'تم تحديث الطلب بنجاح',
    'order_deleted' => 'تم حذف الطلب بنجاح',
    'error_creating_order' => 'خطأ في إنشاء الطلب',
    'error_updating_order' => 'خطأ في تحديث الطلب',
    'error_deleting_order' => 'خطأ في حذف الطلب',

    // Validation Messages
    'order_id_required' => 'رقم الطلب مطلوب',
    'customer_name_required' => 'اسم العميل مطلوب',
    'customer_email_required' => 'بريد العميل الإلكتروني مطلوب',
    'customer_email_invalid' => 'يجب أن يكون بريد العميل الإلكتروني صحيحاً',
    'customer_phone_required' => 'هاتف العميل مطلوب',
    'total_price_required' => 'السعر الإجمالي مطلوب',
    'total_price_numeric' => 'يجب أن يكون السعر الإجمالي رقماً',
    'stage_id_required' => 'المرحلة مطلوبة',
    'stage_id_exists' => 'المرحلة المختارة غير موجودة',

    // Actions
    'add_product' => 'إضافة منتج',
    'remove_product' => 'إزالة منتج',
    'add_fee' => 'إضافة رسوم',
    'add_discount' => 'إضافة خصم',
    'create_fulfillment' => 'إنشاء تنفيذ',
    'print_invoice' => 'طباعة الفاتورة',
    'export_orders' => 'تصدير الطلبات',

    // Fulfillment
    'fulfillment' => 'التنفيذ',
    'fulfillments' => 'التنفيذات',
    'pending' => 'قيد الانتظار',
    'processing' => 'قيد المعالجة',
    'shipped' => 'تم الشحن',
    'delivered' => 'تم التسليم',
    'cancelled' => 'ملغى',

    // Order Summary
    'order_summary' => 'ملخص الطلب',
    'subtotal' => 'المجموع الفرعي',
    'shipping' => 'الشحن',
    'tax' => 'الضريبة',
    'discount' => 'الخصم',
    'total' => 'الإجمالي',

    // Promo Code
    'promo_code' => 'كود الخصم',
    'promo_code_applied' => 'تم تطبيق كود الخصم',

    // Statistics
    'total_orders' => 'إجمالي الطلبات',
    'total_product_price' => 'إجمالي سعر المنتجات',
    'income' => 'الدخل',

    // Form Fields
    'reason' => 'السبب',
    'select' => 'اختر',
    'product_name' => 'اسم المنتج',
    'price' => 'السعر',
    'please_select_product' => 'يرجى اختيار منتج',
    'customer_information' => 'معلومات العميل',
    'customer_type' => 'نوع العميل',
    'existing_customer' => 'عميل موجود',
    'external_customer' => 'عميل خارجي',
    'select_customer' => 'اختر العميل',
    'no_products_found' => 'لم يتم العثور على منتجات',
    'no_customers_found' => 'لم يتم العثور على عملاء',
    'loading_customers' => 'جاري تحميل العملاء...',
    'select_address' => 'اختر العنوان',
];

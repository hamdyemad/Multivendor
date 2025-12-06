<?php

return [
    // General
    'order' => 'طلب',
    'orders' => 'الطلبات',
    'order_id' => 'طلب #:id',
    'order_date' => 'تاريخ الطلب',
    'status' => 'الحالة',
    'total' => 'الإجمالي',
    'actions' => 'الإجراءات',
    'created_at' => 'تاريخ الإنشاء',
    'updated_at' => 'تاريخ التحديث',
    'view' => 'عرض',
    'edit' => 'تعديل',
    'delete' => 'حذف',
    'save' => 'حفظ',
    'cancel' => 'إلغاء',
    'back' => 'رجوع',
    'search' => 'بحث...',
    'no_records' => 'لا توجد سجلات',
    'confirm_delete' => 'هل أنت متأكد من حذف هذا العنصر؟',
    'delete_success' => 'تم الحذف بنجاح',
    'save_success' => 'تم الحفظ بنجاح',
    'error' => 'خطأ',
    'success' => 'نجاح',
    'warning' => 'تحذير',
    'info' => 'معلومات',

    // Order Statuses
    'status_pending' => 'قيد الانتظار',
    'status_processing' => 'قيد المعالجة',
    'status_shipped' => 'تم الشحن',
    'status_delivered' => 'تم التسليم',
    'status_cancelled' => 'ملغي',
    'status_refunded' => 'تم الاسترداد',

    // Order Creation
    'create_order' => 'إنشاء طلب',
    'edit_order' => 'تعديل الطلب',
    'order_details' => 'تفاصيل الطلب',
    'customer_information' => 'معلومات العميل',
    'billing_information' => 'معلومات الفاتورة',
    'shipping_information' => 'معلومات الشحن',
    'order_summary' => 'ملخص الطلب',
    'add_product' => 'إضافة منتج',
    'product' => 'المنتج',
    'products' => 'المنتجات',
    'quantity' => 'الكمية',
    'price' => 'السعر',
    'subtotal' => 'المجموع الفرعي',
    'shipping' => 'الشحن',
    'tax' => 'الضريبة',
    'discount' => 'الخصم',
    'grand_total' => 'الإجمالي النهائي',
    'payment_method' => 'طريقة الدفع',
    'shipping_method' => 'طريقة الشحن',
    'notes' => 'ملاحظات',
    'no_products_found' => 'لم يتم العثور على منتجات',
    'please_select_product' => 'الرجاء اختيار منتج',
    'invalid_quantity' => 'كمية غير صالحة',
    'invalid_price' => 'سعر غير صالح',
    'invalid_product_data' => 'بيانات المنتج غير صالحة',
    'product_added' => 'تمت إضافة المنتج للطلب',
    'product_removed' => 'تمت إزالة المنتج من الطلب',
    'order_created' => 'تم إنشاء الطلب بنجاح',
    'order_updated' => 'تم تحديث الطلب بنجاح',
    'order_deleted' => 'تم حذف الطلب بنجاح',
    'order_cancelled' => 'تم إلغاء الطلب بنجاح',
    'order_status_updated' => 'تم تحديث حالة الطلب بنجاح',

    // Validation Messages
    'validation' => [
        'customer_required' => 'الرجاء اختيار عميل',
        'products_required' => 'الرجاء إضافة منتج واحد على الأقل للطلب',
        'shipping_required' => 'طريقة الشحن مطلوبة',
        'payment_required' => 'طريقة الدفع مطلوبة',
        'quantity_required' => 'الكمية مطلوبة',
        'quantity_min' => 'يجب أن تكون الكمية 1 على الأقل',
        'price_required' => 'السعر مطلوب',
        'price_numeric' => 'يجب أن يكون السعر رقماً',
        'price_min' => 'يجب أن يكون السعر أكبر من صفر',
    ],

    // Order Items
    'items' => [
        'name' => 'الاسم',
        'sku' => 'الكود',
        'price' => 'السعر',
        'qty' => 'الكمية',
        'total' => 'الإجمالي',
        'remove' => 'إزالة',
    ],

    // Order Totals
    'totals' => [
        'subtotal' => 'المجموع الفرعي',
        'shipping' => 'الشحن',
        'tax' => 'الضريبة',
        'discount' => 'الخصم',
        'grand_total' => 'الإجمالي النهائي',
    ],

    // Order Status History
    'status_history' => 'سجل الحالة',
    'status_changed' => 'تم تغيير الحالة إلى :status',
    'status_changed_by' => 'بواسطة :name',
    'status_changed_at' => 'في :date',
    'no_status_history' => 'لا يوجد سجل للحالة',

    // Order Notes
    'add_note' => 'إضافة ملاحظة',
    'notes' => 'ملاحظات',
    'note_added' => 'تمت إضافة الملاحظة بنجاح',
    'note_deleted' => 'تم حذف الملاحظة بنجاح',
    'no_notes' => 'لا توجد ملاحظات',
    'note_placeholder' => 'أضف ملاحظة حول هذا الطلب...',

    // Order Emails
    'email' => [
        'subject' => 'طلبك #:order_id',
        'greeting' => 'مرحباً :name،',
        'thank_you' => 'شكراً لطلبك!',
        'order_details' => 'تفاصيل الطلب',
        'shipping_address' => 'عنوان الشحن',
        'billing_address' => 'عنوان الفاتورة',
        'track_order' => 'تتبع طلبك',
        'contact_us' => 'اتصل بنا',
        'regards' => 'مع تحياتنا،',
        'team' => 'فريق :app_name',
    ],

    // Order Fulfillment
    'fulfillment' => [
        'title' => 'تنفيذ الطلب',
        'tracking_number' => 'رقم التتبع',
        'carrier' => 'شركة الشحن',
        'date_shipped' => 'تاريخ الشحن',
        'add_tracking' => 'إضافة تتبع',
        'tracking_added' => 'تمت إضافة معلومات التتبع',
        'tracking_updated' => 'تم تحديث معلومات التتبع',
        'tracking_removed' => 'تمت إزالة معلومات التتبع',
        'no_tracking' => 'لا توجد معلومات تتبع متاحة',
    ],

    // Order Invoices
    'invoice' => 'فاتورة',
    'invoice_number' => 'فاتورة #',
    'invoice_date' => 'تاريخ الفاتورة',
    'invoice_due_date' => 'تاريخ الاستحقاق',
    'download_invoice' => 'تحميل الفاتورة',
    'print_invoice' => 'طباعة الفاتورة',
    'email_invoice' => 'إرسال الفاتورة بالبريد',
    'invoice_sent' => 'تم إرسال الفاتورة بنجاح',

    // Order Refunds
    'refund' => 'استرداد',
    'refund_amount' => 'مبلغ الاسترداد',
    'refund_reason' => 'سبب الاسترداد',
    'refund_processed' => 'تم معالجة طلب الاسترداد بنجاح',
    'refund_failed' => 'فشل معالجة طلب الاسترداد. يرجى المحاولة مرة أخرى.',
    'refund_history' => 'سجل الاسترداد',
    'no_refunds' => 'لا توجد عمليات استرداد',

    // Order Alerts
    'alert' => [
        'order_created' => 'تم إنشاء الطلب #:order_id',
        'order_updated' => 'تم تحديث الطلب #:order_id',
        'order_deleted' => 'تم حذف الطلب #:order_id',
        'order_status_changed' => 'تم تغيير حالة الطلب #:order_id إلى :status',
        'payment_received' => 'تم استلام الدفع للطلب #:order_id',
        'shipping_confirmed' => 'تم تأكيد الشحن للطلب #:order_id',
        'delivery_confirmed' => 'تم تأكيد التسليم للطلب #:order_id',
    ],

    // Order Exceptions
    'exception' => [
        'invalid_product' => 'منتج غير صالح',
        'invalid_quantity' => 'كمية غير صالحة',
        'insufficient_stock' => 'الكمية غير متوفرة للمنتج :product',
        'order_not_found' => 'الطلب غير موجود',
        'order_cannot_be_modified' => 'لا يمكن تعديل هذا الطلب',
        'order_cannot_be_cancelled' => 'لا يمكن إلغاء هذا الطلب',
        'order_cannot_be_refunded' => 'لا يمكن استرداد هذا الطلب',
        'payment_failed' => 'فشل معالجة الدفع',
        'shipping_failed' => 'فشل معالجة الشحن',
        'refund_failed' => 'فشل معالجة الاسترداد',
    ],
];

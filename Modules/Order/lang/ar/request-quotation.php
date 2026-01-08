<?php

return [
    // Page titles
    'request_quotations' => 'طلبات عروض الأسعار',
    'all_requests' => 'جميع الطلبات',
    'archived_requests' => 'الطلبات المؤرشفة',
    'request_details' => 'تفاصيل الطلب',

    // Fields
    'name' => 'الاسم',
    'email' => 'البريد الإلكتروني',
    'phone' => 'الهاتف',
    'address' => 'العنوان',
    'notes' => 'ملاحظات',
    'file' => 'الملف',
    'customer_info' => 'معلومات العميل',
    'contact_info' => 'معلومات الاتصال',
    'address_info' => 'معلومات العنوان',

    // Status
    'status_pending' => 'قيد الانتظار',
    'status_sent_offer' => 'تم إرسال العرض',
    'status_accepted_offer' => 'مقبول',
    'status_rejected_offer' => 'مرفوض',
    'status_order_created' => 'تم إنشاء الطلب',
    'status_archived' => 'مؤرشف',

    // Offer
    'send_offer' => 'إرسال عرض',
    'send_quotation_offer' => 'إرسال عرض السعر',
    'offer_details' => 'تفاصيل العرض',
    'offer_price' => 'سعر العرض',
    'offer_notes' => 'ملاحظات العرض',
    'offer_notes_placeholder' => 'أدخل أي ملاحظات حول العرض...',
    'offer_sent_at' => 'تاريخ إرسال العرض',
    'offer_responded_at' => 'تاريخ الرد',
    'offer_sent_successfully' => 'تم إرسال العرض بنجاح',
    'cannot_send_offer' => 'لا يمكن إرسال عرض لهذا الطلب',

    // Actions
    'create_order' => 'إنشاء طلب',
    'view_order' => 'عرض الطلب',
    'from_quotation' => 'من عرض سعر',
    'creating_order_from_quotation' => 'إنشاء طلب من طلب عرض السعر',
    'download_file' => 'تحميل الملف',
    'archive' => 'أرشفة',
    'order_number' => 'رقم الطلب',

    // Messages
    'created_successfully' => 'تم إرسال طلب عرض السعر بنجاح',
    'archived_successfully' => 'تم أرشفة طلب عرض السعر بنجاح',
    'deleted_successfully' => 'تم حذف طلب عرض السعر بنجاح',
    'confirm_archive' => 'هل أنت متأكد من أرشفة هذا الطلب؟',
    'search_placeholder' => 'البحث بالاسم، البريد، الهاتف...',

    // Notifications
    'notification_title' => 'عرض سعر جديد',
    'notification_body' => 'لقد تلقيت عرضاً جديداً لطلب عرض السعر الخاص بك. سعر العرض: :price',
    'notification_accepted' => 'قبل عرضك',
    'notification_rejected' => 'رفض عرضك',
    'notification_new_request' => 'تم تقديم طلب عرض سعر جديد',
    'order_created_notification_title' => 'تم إنشاء الطلب',
    'order_created_notification_body' => 'تم إنشاء طلبك رقم #:order_number من طلب عرض السعر الخاص بك',
    'quotation_offer_notification_title' => 'عرض سعر جديد',
    'quotation_offer_notification_body' => 'لقد تلقيت عرض سعر بإجمالي: :price. يرجى المراجعة والموافقة أو الرفض.',

    // API responses
    'cannot_respond_to_offer' => 'لا يمكن الرد على هذا العرض',
    'offer_accepted_successfully' => 'تم قبول العرض بنجاح',
    'offer_rejected_successfully' => 'تم رفض العرض بنجاح',
];

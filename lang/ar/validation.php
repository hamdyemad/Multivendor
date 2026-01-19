<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'success' => 'تم بنجاح',
    'error' => 'فشل',
    'accepted' => 'يجب قبول :attribute.',
    'accepted_if' => 'يجب قبول :attribute عندما يكون :other هو :value.',
    'active_url' => ':attribute ليس عنوان URL صالحاً.',
    'after' => 'يجب أن يكون :attribute تاريخاً بعد :date.',
    'after_or_equal' => 'يجب أن يكون :attribute تاريخاً بعد أو يساوي :date.',
    'alpha' => 'يجب أن يحتوي :attribute على أحرف فقط.',
    'alpha_dash' => 'يجب أن يحتوي :attribute على أحرف وأرقام وشرطات وشرطات سفلية فقط.',
    'alpha_num' => 'يجب أن يحتوي :attribute على أحرف وأرقام فقط.',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'before' => 'يجب أن يكون :attribute تاريخاً قبل :date.',
    'before_or_equal' => 'يجب أن يكون :attribute تاريخاً قبل أو يساوي :date.',
    'between' => [
        'numeric' => 'يجب أن يكون :attribute بين :min و :max.',
        'file' => 'يجب أن يكون :attribute بين :min و :max كيلوبايت.',
        'string' => 'يجب أن يكون :attribute بين :min و :max حرفاً.',
        'array' => 'يجب أن يحتوي :attribute على عناصر بين :min و :max.',
    ],
    'boolean' => 'يجب أن يكون حقل :attribute صحيحاً أو خاطئاً.',
    'confirmed' => 'تأكيد :attribute غير متطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => ':attribute ليس تاريخاً صالحاً.',
    'date_equals' => 'يجب أن يكون :attribute تاريخاً مساوياً لـ :date.',
    'date_format' => ':attribute لا يتطابق مع التنسيق :format.',
    'declined' => 'يجب رفض :attribute.',
    'declined_if' => 'يجب رفض :attribute عندما يكون :other هو :value.',
    'different' => 'يجب أن يكون :attribute و :other مختلفين.',
    'digits' => 'يجب أن يكون :attribute :digits رقماً.',
    'digits_between' => 'يجب أن يكون :attribute بين :min و :max رقماً.',
    'dimensions' => ':attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالحاً.',
    'ends_with' => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values.',
    'enum' => ':attribute المحدد غير صالح.',
    'exists' => ':attribute المحدد غير صالح.',
    'file' => 'يجب أن يكون :attribute ملفاً.',
    'filled' => 'يجب أن يحتوي حقل :attribute على قيمة.',
    'gt' => [
        'numeric' => 'يجب أن يكون :attribute أكبر من :value.',
        'file' => 'يجب أن يكون :attribute أكبر من :value كيلوبايت.',
        'string' => 'يجب أن يكون :attribute أكبر من :value حرفاً.',
        'array' => 'يجب أن يحتوي :attribute على أكثر من :value عنصراً.',
    ],
    'gte' => [
        'numeric' => 'يجب أن يكون :attribute أكبر من أو يساوي :value.',
        'file' => 'يجب أن يكون :attribute أكبر من أو يساوي :value كيلوبايت.',
        'string' => 'يجب أن يكون :attribute أكبر من أو يساوي :value حرفاً.',
        'array' => 'يجب أن يحتوي :attribute على :value عنصراً أو أكثر.',
    ],
    'image' => 'يجب أن يكون :attribute صورة.',
    'in' => ':attribute المحدد غير صالح.',
    'in_array' => 'حقل :attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون :attribute عدداً صحيحاً.',
    'ip' => 'يجب أن يكون :attribute عنوان IP صالحاً.',
    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صالحاً.',
    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صالحاً.',
    'json' => 'يجب أن يكون :attribute نص JSON صالحاً.',
    'lt' => [
        'numeric' => 'يجب أن يكون :attribute أقل من :value.',
        'file' => 'يجب أن يكون :attribute أقل من :value كيلوبايت.',
        'string' => 'يجب أن يكون :attribute أقل من :value حرفاً.',
        'array' => 'يجب أن يحتوي :attribute على أقل من :value عنصراً.',
    ],
    'lte' => [
        'numeric' => 'يجب أن يكون :attribute أقل من أو يساوي :value.',
        'file' => 'يجب أن يكون :attribute أقل من أو يساوي :value كيلوبايت.',
        'string' => 'يجب أن يكون :attribute أقل من أو يساوي :value حرفاً.',
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :value عنصراً.',
    ],
    'mac_address' => 'يجب أن يكون :attribute عنوان MAC صالحاً.',
    'max' => [
        'numeric' => 'يجب ألا يكون :attribute أكبر من :max.',
        'file' => 'يجب ألا يكون :attribute أكبر من :max كيلوبايت.',
        'string' => 'يجب ألا يكون :attribute أكبر من :max حرفاً.',
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :max عنصراً.',
    ],
    'mimes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',
    'mimetypes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',
    'min' => [
        'numeric' => 'يجب أن يكون :attribute على الأقل :min.',
        'file' => 'يجب أن يكون :attribute على الأقل :min كيلوبايت.',
        'string' => 'يجب أن يكون :attribute على الأقل :min حرفاً.',
        'array' => 'يجب أن يحتوي :attribute على الأقل على :min عنصراً.',
    ],
    'multiple_of' => 'يجب أن يكون :attribute مضاعفاً لـ :value.',
    'not_in' => ':attribute المحدد غير صالح.',
    'not_regex' => 'تنسيق :attribute غير صالح.',
    'numeric' => 'يجب أن يكون :attribute رقماً.',
    'password' => 'كلمة المرور غير صحيحة.',
    'present' => 'يجب أن يكون حقل :attribute موجوداً.',
    'prohibited' => 'حقل :attribute محظور.',
    'prohibited_if' => 'حقل :attribute محظور عندما يكون :other هو :value.',
    'prohibited_unless' => 'حقل :attribute محظور ما لم يكن :other في :values.',
    'prohibits' => 'حقل :attribute يمنع :other من التواجد.',
    'regex' => 'تنسيق :attribute غير صالح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'يجب أن يحتوي حقل :attribute على إدخالات لـ: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_unless' => 'حقل :attribute مطلوب ما لم يكن :other في :values.',
    'required_with' => 'حقل :attribute مطلوب عندما يكون :values موجوداً.',
    'required_with_all' => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without' => 'حقل :attribute مطلوب عندما لا يكون :values موجوداً.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا تكون أي من :values موجودة.',
    'same' => 'يجب أن يتطابق :attribute و :other.',
    'size' => [
        'numeric' => 'يجب أن يكون :attribute بحجم :size.',
        'file' => 'يجب أن يكون :attribute بحجم :size كيلوبايت.',
        'string' => 'يجب أن يكون :attribute بحجم :size حرفاً.',
        'array' => 'يجب أن يحتوي :attribute على :size عنصراً.',
    ],
    'starts_with' => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون :attribute نصاً.',
    'timezone' => 'يجب أن يكون :attribute منطقة زمنية صالحة.',
    'unique' => ':attribute مُستخدم بالفعل.',
    'uploaded' => 'فشل تحميل :attribute.',
    'url' => 'يجب أن يكون :attribute عنوان URL صالحاً.',
    'uuid' => 'يجب أن يكون :attribute UUID صالحاً.',


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'رسالة مخصصة',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Product Filter Validations
    |--------------------------------------------------------------------------
    */
    'min_price_positive' => 'يجب أن يكون السعر الأدنى رقماً موجباً',
    'max_price_positive' => 'يجب أن يكون السعر الأقصى رقماً موجباً',
    'min_price_max_price' => 'لا يمكن أن يكون السعر الأدنى أكبر من أو يساوي السعر الأقصى',
    'sort_by_invalid' => 'يجب أن يكون sort_by أحد الخيارات التالية: created_at, name, price, rating, views, sales',
    'sort_type_invalid' => 'يجب أن يكون sort_type إما asc أو desc',
    'country_id_not_exist' => 'الدولة غير موجودة',
    'city_id_not_exist' => 'المدينة غير موجودة',
    'region_id_not_exist' => 'المنطقة غير موجودة',
    'subregion_id_not_exist' => 'المنطقة الفرعية غير موجودة',
    'char_invalid' => 'char يجب أن يكون حرفًا صحيحًا',
    'department_id_not_exist' => 'department_id غير موجود',
    'category_id_not_exist' => 'category_id غير موجود',
    'sub_category_id_not_exist' => 'sub_category_id غير موجود',
    'brand_id_not_exist' => 'brand_id غير موجود',
    'vendor_id_not_exist' => 'vendor_id غير موجود',
    'rate_invalid' => 'rate يجب أن يكون بين 1 و 5',
    'has_discount_invalid' => 'has_discount يجب أن يكون boolean',
    'featured_invalid' => 'featured يجب أن يكون boolean',
    'created_date_from_invalid' => 'يجب أن يكون created_date_from تاريخاً صحيحاً (YYYY-MM-DD)',
    'created_date_to_invalid' => 'يجب أن يكون created_date_to تاريخاً صحيحاً (YYYY-MM-DD)',
    'min_star_range' => 'يجب أن يكون الحد الأدنى للنجوم بين 1 و 5',
    'max_star_range' => 'يجب أن يكون الحد الأقصى للنجوم بين 1 و 5',
    'min_star_max_star' => 'لا يمكن أن يكون الحد الأدنى للنجوم أكبر من الحد الأقصى',
    'status_invalid' => 'يجب أن تكون الحالة أحد الخيارات التالية: pending, approved, rejected',
    'vendor_product_id_not_exist' => 'معرف المنتج غير موجود',
    'vendor_product_id_required' => 'معرف المنتج مطلوب',
    'vendor_product_variant_id_not_exist' => 'معرف متغير المنتج غير موجود',
    'vendor_product_variant_id_required' => 'معرف متغير المنتج مطلوب',
    'variant_not_belong_to_product' => 'المتغير لا ينتمي إلى المنتج المحدد',
    'product_not_in_bundle' => 'متغير المنتج ليس جزءًا من هذه الحزمة',
    'product_not_in_occasion' => 'متغير المنتج ليس جزءًا من هذه المناسبة',
    'quantity_required' => 'الكمية مطلوبة',
    'quantity_min' => 'يجب أن تكون الكمية على الأقل 1',
    'quantity_exceeds_max_per_order' => 'لا يمكن أن تتجاوز الكمية :max لكل طلب',
    'quantity_exceeds_bundle_max_per_order' => 'الكمية يجب أن تكون بين :min و :max',
    'type_required' => 'النوع مطلوب',
    'type_invalid' => 'يجب أن يكون النوع أحد الخيارات التالية: product, bundle, occasion',
    'bundle_id_required' => 'معرف الحزمة مطلوب',
    'bundle_id_not_exist' => 'معرف الحزمة غير موجود',
    'occasion_id_required' => 'معرف المناسبة مطلوب',
    'occasion_id_not_exist' => 'معرف المناسبة غير موجود',
    'items_required' => 'العناصر مطلوبة',
    'items_min' => 'يجب أن يكون هناك عنصر واحد على الأقل',
    'cart_type_invalid' => 'يجب أن يكون نوع السلة أحد الخيارات التالية: product, bundle, occasion',

    // Checkout validation messages
    'user_address_id_required' => 'عنوان المستخدم مطلوب',
    'user_address_id_integer' => 'يجب أن يكون عنوان المستخدم رقماً',
    'user_address_id_exists' => 'عنوان المستخدم المحدد غير موجود',
    'promo_code_id_string' => 'يجب أن يكون رمز الترويج نصاً',
    'order_from_in' => 'يجب أن يكون الطلب من أحد الخيارات التالية: WEB, ANDROID, IOS',
    'payment_type_in' => 'يجب أن تكون طريقة الدفع أحد الخيارات التالية: cash_on_delivery, online, aman',
    'use_point_required' => 'حقل استخدام النقاط مطلوب',
    'use_point_boolean' => 'يجب أن تكون قيمة استخدام النقاط صحيحة أو خاطئة',
    'customer_address_id_not_exist' => 'عنوان العميل غير موجود',
    'customer_id_not_exist' => 'العميل غير موجود                    ',
    "bundle_not_active" => 'الباقة غير مفعلة أو منتهية الصلاحية',
    'occasion_not_active' => 'المناسبة غير مفعلة أو منتهية الصلاحية',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'country_id' => 'الدولة',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'logo' => 'الشعار',
        'banner' => 'البانر',
        'documents' => 'المستندات',
        'document_name' => 'اسم المستند',
        'document_file' => 'ملف المستند',
        'translations.*.name' => 'الاسم',
        'commission' => 'العمولة',
        'type' => 'النوع',
        'max_per_order' => 'الحد الاقصى للطلب الواحدة',
        'code' => 'الكود',
        'value' => 'القيمة',
        'dedicated_to' => 'النوع',
        'valid_from' => 'تاريخ البدء',
        'valid_to' => 'تاريخ الانتهاء',
        'valid_until' => 'تاريخ الانتهاء',
        'variants.*.discount_end_date' => 'تاريخ انتهاء خصم المتغير',
    ],

];

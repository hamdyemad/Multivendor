<?php

use App\Models\Attachment;
use App\Models\Language;
use App\Models\Permession;
use App\Models\Role;
use App\Models\User;
use App\Models\UserType;

/**
 * Get Status Meta
 *
 * @param string $status_key
 * @return array Status
 */
function get_status_meta($status_key = '')
{
    $metas = [
        'active'   => [
            'label' => 'Active',
            'class' => 'success',
        ],
        'inactive' => [
            'label' => 'Inactive',
            'class' => 'warning',
        ],
        'blocked'  => [
            'label' => 'Blocked',
            'class' => 'danger',
        ],
    ];

    if (empty($status_key)) {
        return $metas;
    }

    if (in_array($status_key, array_keys($metas))) {
        return $metas[$status_key];
    }

    return [];
}

/**
 * Get Status Class
 *
 * @param string $status_key
 * @return string Status Class
 */
function get_status_class($status_key = '')
{

    $status_meta = get_status_meta($status_key);

    if (empty($status_meta['class'])) {
        return '';
    }

    return $status_meta['class'];
}

/**
 * Get Status label
 *
 * @param string $status_key
 * @return string Status label
 */
function get_status_label($status_key = '')
{
    $status_meta = get_status_meta($status_key);

    if (empty($status_meta['label'])) {
        return '';
    }

    return $status_meta['label'];
}


function permessions_reset()
{
    // Get languages
    $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

    Permession::query()->forceDelete();
    $permissions = [
        // Dashboard
        ['key' => 'dashboard.view', 'translations' => [
            'name' => ['en' => 'View Dashboard', 'ar' => 'عرض لوحة التحكم'],
            'group_by' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],
        ]],

        // Catalog Management - Category Management

        // Start Activities Permessions
        ['key' => 'activities.index', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'All Activities', 'ar' => 'كل الانشطة'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'activities.view', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'View Activities', 'ar' => 'عرض الانشطة'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'activities.create', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Create Activities', 'ar' => 'إنشاء الانشطة'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'activities.edit', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Edit Activities', 'ar' => 'تعديل الانشطة'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'activities.delete', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Delete Activities', 'ar' => 'ازالة الانشطة'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        // End Activities Permessions


        // Start Departments Permessions
        ['key' => 'departments.index', 'translations' => [
            'name' => ['en' => 'All Departments', 'ar' => 'كل الأقسام'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'departments.view', 'translations' => [
            'name' => ['en' => 'View Departments', 'ar' => 'عرض الأقسام'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'departments.create', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Create Department', 'ar' => 'إنشاء قسم'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'departments.edit', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Edit Department', 'ar' => 'تعديل قسم'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'departments.delete', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Delete Department', 'ar' => 'حذف قسم'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        // End Departments Permessions

        // Start Categories Permessions
        ['key' => 'categories.index', 'translations' => [
            'name' => ['en' => 'All Main Categories', 'ar' => 'كل الأقسام الرئيسية'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'categories.view', 'translations' => [
            'name' => ['en' => 'View Main Categories', 'ar' => 'عرض الأقسام الرئيسية'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'categories.create', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Create Main Category', 'ar' => 'إنشاء قسم رئيسية'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'categories.edit', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Edit Main Category', 'ar' => 'تعديل قسم رئيسية'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'categories.delete', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Delete Main Category', 'ar' => 'حذف قسم رئيسية'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        // End Categories Permessions


        // Start Sub Categories Permessions
        ['key' => 'sub_categories.index', 'translations' => [
            'name' => ['en' => 'All Sub Categories', 'ar' => 'كل الأقسام الفرعية'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'sub_categories.view', 'translations' => [
            'name' => ['en' => 'View Sub Categories', 'ar' => 'عرض الأقسام الفرعية'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'sub_categories.create', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Create Sub Category', 'ar' => 'إنشاء قسم فرعي'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'sub_categories.edit', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Edit Sub Category', 'ar' => 'تعديل قسم فرعي'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        ['key' => 'sub_categories.delete', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Delete Sub Category', 'ar' => 'حذف قسم فرعي'],
            'group_by' => ['en' => 'Catalog Management', 'ar' => 'إدارة الكتالوج'],
        ]],
        // End Sub Categories Permessions

        // Start Products Permessions
        ['key' => 'products.index', 'translations' => [
            'name' => ['en' => 'All Products', 'ar' => 'كل المنتجات'],
            'group_by' => ['en' => 'Products', 'ar' => 'منتجات'],
        ]],
        ['key' => 'products.view', 'translations' => [
            'name' => ['en' => 'View Products', 'ar' => 'عرض المنتجات'],
            'group_by' => ['en' => 'Products', 'ar' => 'منتجات'],
        ]],
        ['key' => 'products.create', 'translations' => [
            'name' => ['en' => 'Create Product', 'ar' => 'إنشاء منتج'],
            'group_by' => ['en' => 'Products', 'ar' => 'منتجات'],
        ]],
        ['key' => 'products.edit', 'translations' => [
            'name' => ['en' => 'Edit Product', 'ar' => 'تعديل منتج'],
            'group_by' => ['en' => 'Products', 'ar' => 'منتجات'],
        ]],
        ['key' => 'products.delete', 'translations' => [
            'name' => ['en' => 'Delete Product', 'ar' => 'حذف منتج'],
            'group_by' => ['en' => 'Products', 'ar' => 'منتجات'],
        ]],
        // End Products Permessions

        // Start Products Instock Permessions
        ['key' => 'products.in_stock.view', 'translations' => [
            'name' => ['en' => 'View In Stock Products', 'ar' => 'عرض المنتجات في المخزون'],
            'group_by' => ['en' => 'Products', 'ar' => 'منتجات'],
        ]],
        ['key' => 'products.out_of_stock.view', 'translations' => [
            'name' => ['en' => 'View Out of Stock Products', 'ar' => 'عرض المنتجات غير في المخزون'],
            'group_by' => ['en' => 'Products', 'ar' => 'منتجات'],
        ]],

        // Product Reviews
        ['key' => 'product_reviews.view', 'translations' => [
            'name' => ['en' => 'View Product Reviews', 'ar' => 'عرض تقييم المنتج'],
            'group_by' => ['en' => 'Products', 'ar' => 'منتجات'],
        ]],
        ['key' => 'product_reviews.accept', 'translations' => [
            'name' => ['en' => 'Accept Product Review', 'ar' => 'قبول تقييم منتج'],
            'group_by' => ['en' => 'Products', 'ar' => 'منتجات'],
        ]],
        ['key' => 'product_reviews.reject', 'translations' => [
            'name' => ['en' => 'Reject Product Review', 'ar' => 'رفض تقييم منتج'],
            'group_by' => ['en' => 'Products', 'ar' => 'منتجات'],
        ]],
        ['key' => 'product_reviews.delete', 'translations' => [
            'name' => ['en' => 'Delete Product Review', 'ar' => 'حذف تقييم منتج'],
            'group_by' => ['en' => 'Products', 'ar' => 'منتجات'],
        ]],

        // Start Taxes
        ['key' => 'taxes.index', 'translations' => [
            'name' => ['en' => 'All Taxes', 'ar' => 'كل الضرائب'],
            'group_by' => ['en' => 'Taxes', 'ar' => 'ضرائب'],
        ]],
        ['key' => 'taxes.view', 'translations' => [
            'name' => ['en' => 'View Taxes', 'ar' => 'عرض الضرائب'],
            'group_by' => ['en' => 'Taxes', 'ar' => 'ضرائب'],
        ]],
        ['key' => 'taxes.create', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Create Tax', 'ar' => 'إنشاء ضريبة'],
            'group_by' => ['en' => 'Taxes', 'ar' => 'ضرائب'],
        ]],
        ['key' => 'taxes.edit', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Edit Tax', 'ar' => 'تعديل ضريبة'],
            'group_by' => ['en' => 'Taxes', 'ar' => 'ضرائب'],
        ]],
        ['key' => 'taxes.delete', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Delete Tax', 'ar' => 'حذف ضريبة'],
            'group_by' => ['en' => 'Taxes', 'ar' => 'ضرائب'],
        ]],
        // End Taxes

        // Start Offers
        ['key' => 'offers.index', 'translations' => [
            'name' => ['en' => 'All Offers', 'ar' => 'كل العروض'],
            'group_by' => ['en' => 'Offers', 'ar' => 'عروض'],
        ]],
        ['key' => 'offers.view', 'translations' => [
            'name' => ['en' => 'View Offers', 'ar' => 'عرض العروض'],
            'group_by' => ['en' => 'Offers', 'ar' => 'عروض'],
        ]],
        ['key' => 'offers.create', 'translations' => [
            'name' => ['en' => 'Create Offer', 'ar' => 'إنشاء عرض'],
            'group_by' => ['en' => 'Offers', 'ar' => 'عروض'],
        ]],
        ['key' => 'offers.edit', 'translations' => [
            'name' => ['en' => 'Edit Offer', 'ar' => 'تعديل عرض'],
            'group_by' => ['en' => 'Offers', 'ar' => 'عروض'],
        ]],
        ['key' => 'offers.delete', 'translations' => [
            'name' => ['en' => 'Delete Offer', 'ar' => 'حذف عرض'],
            'group_by' => ['en' => 'Offers', 'ar' => 'عروض'],
        ]],
        // End Offers

        // Promocodes
        ['key' => 'promocodes.index', 'translations' => [
            'name' => ['en' => 'All Promocodes', 'ar' => 'كل الكودات'],
            'group_by' => ['en' => 'Promocodes', 'ar' => 'كودات'],
        ]],
        ['key' => 'promocodes.view', 'translations' => [
            'name' => ['en' => 'View Promocodes', 'ar' => 'عرض الكودات'],
            'group_by' => ['en' => 'Promocodes', 'ar' => 'كودات'],
        ]],
        ['key' => 'promocodes.create', 'translations' => [
            'name' => ['en' => 'Create Promocode', 'ar' => 'إنشاء كود'],
            'group_by' => ['en' => 'Promocodes', 'ar' => 'كودات'],
        ]],
        ['key' => 'promocodes.edit', 'translations' => [
            'name' => ['en' => 'Edit Promocode', 'ar' => 'تعديل كود'],
            'group_by' => ['en' => 'Promocodes', 'ar' => 'كودات'],
        ]],
        ['key' => 'promocodes.delete', 'translations' => [
            'name' => ['en' => 'Delete Promocode', 'ar' => 'حذف كود'],
            'group_by' => ['en' => 'Promocodes', 'ar' => 'كودات'],
        ]],

        // Start Roles
        ['key' => 'roles.index', 'translations' => [
            'name' => ['en' => 'All Roles', 'ar' => 'كل الأدوار'],
            'group_by' => ['en' => 'Roles Management', 'ar' => 'إدارة الأدوار'],
        ]],
        ['key' => 'roles.view', 'translations' => [
            'name' => ['en' => 'View Roles', 'ar' => 'عرض الأدوار'],
            'group_by' => ['en' => 'Roles Management', 'ar' => 'إدارة الأدوار'],
        ]],
        ['key' => 'roles.create', 'translations' => [
            'name' => ['en' => 'Create Role', 'ar' => 'إنشاء دور'],
            'group_by' => ['en' => 'Roles Management', 'ar' => 'إدارة الأدوار'],
        ]],
        ['key' => 'roles.edit', 'translations' => [
            'name' => ['en' => 'Edit Role', 'ar' => 'تعديل دور'],
            'group_by' => ['en' => 'Roles Management', 'ar' => 'إدارة الأدوار'],
        ]],
        ['key' => 'roles.delete', 'translations' => [
            'name' => ['en' => 'Delete Role', 'ar' => 'حذف دور'],
            'group_by' => ['en' => 'Roles Management', 'ar' => 'إدارة الأدوار'],
        ]],
        // End Roles

        // Start Admins
        ['key' => 'admins.index', 'translations' => [
            'name' => ['en' => 'All Admins', 'ar' => 'كل المسؤولين'],
            'group_by' => ['en' => 'Admin Management', 'ar' => 'إدارة المسؤولين'],
        ]],
        ['key' => 'admins.view', 'translations' => [
            'name' => ['en' => 'View Admins', 'ar' => 'عرض المسؤولين'],
            'group_by' => ['en' => 'Admin Management', 'ar' => 'إدارة المسؤولين'],
        ]],
        ['key' => 'admins.create', 'translations' => [
            'name' => ['en' => 'Create Admin', 'ar' => 'إنشاء المسؤول'],
            'group_by' => ['en' => 'Admin Management', 'ar' => 'إدارة المسؤولين'],
        ]],
        ['key' => 'admins.edit', 'translations' => [
            'name' => ['en' => 'Edit Admin', 'ar' => 'تعديل المسؤول'],
            'group_by' => ['en' => 'Admin Management', 'ar' => 'إدارة المسؤولين'],
        ]],
        ['key' => 'admins.delete', 'translations' => [
            'name' => ['en' => 'Delete Admin', 'ar' => 'ازالة المسؤول'],
            'group_by' => ['en' => 'Admin Management', 'ar' => 'إدارة المسؤولين'],
        ]],
        // End Admins


        // Start Vendors
        ['key' => 'vendors.index', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'All Vendors', 'ar' => 'كل الموردين'],
            'group_by' => ['en' => 'Vendors', 'ar' => 'الموردين'],
        ]],
        ['key' => 'vendors.view', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'View Vendors', 'ar' => 'عرض الموردين'],
            'group_by' => ['en' => 'Vendors', 'ar' => 'الموردين'],
        ]],
        ['key' => 'vendors.create', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Create Vendors', 'ar' => 'انشاء الموردين'],
            'group_by' => ['en' => 'Vendors', 'ar' => 'الموردين'],
        ]],
        ['key' => 'vendors.edit', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Edit Vendors', 'ar' => 'تعديل الموردين'],
            'group_by' => ['en' => 'Vendors', 'ar' => 'الموردين'],
        ]],
        ['key' => 'vendors.delete', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Remove Vendors', 'ar' => 'ازالة الموردين'],
            'group_by' => ['en' => 'Vendors', 'ar' => 'الموردين'],
        ]],
        // End Vendors

        // Start Become a Vendor Requests
        ['key' => 'vendor_requests.new', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'View New Vendor Requests', 'ar' => 'عرض طلبات الموردين الجديدة'],
            'group_by' => ['en' => 'Vendor Requests', 'ar' => 'طلبات الموردين'],
        ]],
        ['key' => 'vendor_requests.accept', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Accept Vendor Request', 'ar' => 'قبول طلب المورد'],
            'group_by' => ['en' => 'Vendor Requests', 'ar' => 'طلبات الموردين'],
        ]],
        ['key' => 'vendor_requests.reject', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Reject Vendor Request', 'ar' => 'رفض طلب المورد'],
            'group_by' => ['en' => 'Vendor Requests', 'ar' => 'طلبات الموردين'],
        ]],
        // End Become a Vendor Requests

        // Start Orders
        ['key' => 'orders.new', 'translations' => [
            'name' => ['en' => 'View New Orders', 'ar' => 'عرض الطلبات الجديدة'],
            'group_by' => ['en' => 'Orders', 'ar' => 'الطلبات'],
        ]],
        ['key' => 'orders.inprogress', 'translations' => [
            'name' => ['en' => 'View Inprogress Orders', 'ar' => 'عرض الطلبات المعلقة'],
            'group_by' => ['en' => 'Orders', 'ar' => 'الطلبات'],
        ]],
        ['key' => 'orders.delivered', 'translations' => [
            'name' => ['en' => 'View Delivered Orders', 'ar' => 'عرض الطلبات المكتملة'],
            'group_by' => ['en' => 'Orders', 'ar' => 'الطلبات'],
        ]],
        ['key' => 'orders.canceled', 'translations' => [
            'name' => ['en' => 'View Canceled Orders', 'ar' => 'عرض الطلبات الملغاة'],
            'group_by' => ['en' => 'Orders', 'ar' => 'الطلبات'],
        ]],
        ['key' => 'orders.refunded', 'translations' => [
            'name' => ['en' => 'View Refunded Orders', 'ar' => 'عرض الطلبات المدفوعة'],
            'group_by' => ['en' => 'Orders', 'ar' => 'الطلبات'],
        ]],
        ['key' => 'orders.edit', 'translations' => [
            'name' => ['en' => 'Edit Order', 'ar' => 'تعديل طلب'],
            'group_by' => ['en' => 'Orders', 'ar' => 'الطلبات'],
        ]],
        ['key' => 'orders.delete', 'translations' => [
            'name' => ['en' => 'Delete Order', 'ar' => 'حذف طلب'],
            'group_by' => ['en' => 'Orders', 'ar' => 'الطلبات'],
        ]],
        // End Orders

        // Start Order Stages
        ['key' => 'order_stages.index', 'translations' => [
            'name' => ['en' => 'All Order Stages', 'ar' => 'كل خطوات الطلب'],
            'group_by' => ['en' => 'Order Settings', 'ar' => 'إعدادات الطلب'],
        ]],
        ['key' => 'order_stages.view', 'translations' => [
            'name' => ['en' => 'View Order Stages', 'ar' => 'عرض خطوات الطلب'],
            'group_by' => ['en' => 'Order Settings', 'ar' => 'إعدادات الطلب'],
        ]],
        ['key' => 'order_stages.create', 'translations' => [
            'name' => ['en' => 'Create Order Stage', 'ar' => 'إنشاء خطوة طلب'],
            'group_by' => ['en' => 'Order Settings', 'ar' => 'إعدادات الطلب'],
        ]],
        ['key' => 'order_stages.edit', 'translations' => [
            'name' => ['en' => 'Edit Order Stage', 'ar' => 'تعديل خطوة طلب'],
            'group_by' => ['en' => 'Order Settings', 'ar' => 'إعدادات الطلب'],
        ]],
        ['key' => 'order_stages.delete', 'translations' => [
            'name' => ['en' => 'Delete Order Stage', 'ar' => 'حذف خطوة طلب'],
            'group_by' => ['en' => 'Order Settings', 'ar' => 'إعدادات الطلب'],
        ]],
        // End Order Stages

        // Start Shipping Methods
        ['key' => 'shipping_methods.index', 'translations' => [
            'name' => ['en' => 'All Shipping Methods', 'ar' => 'كل طرق الشحن'],
            'group_by' => ['en' => 'Order Settings', 'ar' => 'إعدادات الطلب'],
        ]],
        ['key' => 'shipping_methods.view', 'translations' => [
            'name' => ['en' => 'View Shipping Methods', 'ar' => 'عرض طرق الشحن'],
            'group_by' => ['en' => 'Order Settings', 'ar' => 'إعدادات الطلب'],
        ]],
        ['key' => 'shipping_methods.create', 'translations' => [
            'name' => ['en' => 'Create Shipping Method', 'ar' => 'إنشاء طريقة شحن'],
            'group_by' => ['en' => 'Order Settings', 'ar' => 'إعدادات الطلب'],
        ]],
        ['key' => 'shipping_methods.edit', 'translations' => [
            'name' => ['en' => 'Edit Shipping Method', 'ar' => 'تعديل طريقة شحن'],
            'group_by' => ['en' => 'Order Settings', 'ar' => 'إعدادات الطلب'],
        ]],
        ['key' => 'shipping_methods.delete', 'translations' => [
            'name' => ['en' => 'Delete Shipping Method', 'ar' => 'حذف طريقة شحن'],
            'group_by' => ['en' => 'Order Settings', 'ar' => 'إعدادات الطلب'],
        ]],
        // End Shipping Methods

        // Start Points System
        ['key' => 'points.index', 'translations' => [
            'name' => ['en' => 'All Points System', 'ar' => 'كل نظام النقاط'],
            'group_by' => ['en' => 'Points System', 'ar' => 'نظام النقاط'],
        ]],
        ['key' => 'points.view', 'translations' => [
            'name' => ['en' => 'View Points System', 'ar' => 'عرض نظام النقاط'],
            'group_by' => ['en' => 'Points System', 'ar' => 'نظام النقاط'],
        ]],
        ['key' => 'points.create', 'translations' => [
            'name' => ['en' => 'Create Points System', 'ar' => 'إنشاء نظام نقاط'],
            'group_by' => ['en' => 'Points System', 'ar' => 'نظام النقاط'],
        ]],
        ['key' => 'points.edit', 'translations' => [
            'name' => ['en' => 'Edit Points System', 'ar' => 'تعديل نظام نقاط'],
            'group_by' => ['en' => 'Points System', 'ar' => 'نظام النقاط'],
        ]],
        ['key' => 'points.delete', 'translations' => [
            'name' => ['en' => 'Delete Points System', 'ar' => 'حذف نظام نقاط'],
            'group_by' => ['en' => 'Points System', 'ar' => 'نظام النقاط'],
        ]],
        // End Points System


        // Start advertisements
        ['key' => 'advertisements.index', 'translations' => [
            'name' => ['en' => 'All Advertisements', 'ar' => 'كل الإعلانات'],
            'group_by' => ['en' => 'Advertisements', 'ar' => 'الإعلانات'],
        ]],
        ['key' => 'advertisements.view', 'translations' => [
            'name' => ['en' => 'View Advertisements', 'ar' => 'عرض الإعلانات'],
            'group_by' => ['en' => 'Advertisements', 'ar' => 'الإعلانات'],
        ]],
        ['key' => 'advertisements.create', 'translations' => [
            'name' => ['en' => 'Create Advertisement', 'ar' => 'إنشاء إعلان'],
            'group_by' => ['en' => 'Advertisements', 'ar' => 'الإعلانات'],
        ]],
        ['key' => 'advertisements.edit', 'translations' => [
            'name' => ['en' => 'Edit Advertisement', 'ar' => 'تعديل إعلان'],
            'group_by' => ['en' => 'Advertisements', 'ar' => 'الإعلانات'],
        ]],
        ['key' => 'advertisements.delete', 'translations' => [
            'name' => ['en' => 'Delete Advertisement', 'ar' => 'حذف إعلان'],
            'group_by' => ['en' => 'Advertisements', 'ar' => 'الإعلانات'],
        ]],
        // End advertisements

        // Start positions
        ['key' => 'positions.index', 'translations' => [
            'name' => ['en' => 'All Advertisement Positions', 'ar' => 'كل مواقع الإعلانات'],
            'group_by' => ['en' => 'Advertisements', 'ar' => 'الإعلانات'],
        ]],
        ['key' => 'positions.view', 'translations' => [
            'name' => ['en' => 'View Advertisement Positions', 'ar' => 'عرض مواقع الإعلانات'],
            'group_by' => ['en' => 'Advertisements', 'ar' => 'الإعلانات'],
        ]],
        ['key' => 'positions.create', 'translations' => [
            'name' => ['en' => 'Create Advertisement Position', 'ar' => 'إنشاء موقع إعلان'],
            'group_by' => ['en' => 'Advertisements', 'ar' => 'الإعلانات'],
        ]],
        ['key' => 'positions.edit', 'translations' => [
            'name' => ['en' => 'Edit Advertisement Position', 'ar' => 'تعديل موقع إعلان'],
            'group_by' => ['en' => 'Advertisements', 'ar' => 'الإعلانات'],
        ]],
        ['key' => 'positions.delete', 'translations' => [
            'name' => ['en' => 'Delete Advertisement Position', 'ar' => 'حذف موقع إعلان'],
            'group_by' => ['en' => 'Advertisements', 'ar' => 'الإعلانات'],
        ]],
        // End positions


        // Notifications
        ['key' => 'notifications.view', 'translations' => [
            'name' => ['en' => 'View Notifications', 'ar' => 'عرض الإشعارات'],
            'group_by' => ['en' => 'Notifications', 'ar' => 'الإشعارات'],
        ]],
        ['key' => 'notifications.send', 'translations' => [
            'name' => ['en' => 'Send Notification', 'ar' => 'إرسال إشعار'],
            'group_by' => ['en' => 'Notifications', 'ar' => 'الإشعارات'],
        ]],
        ['key' => 'notifications.delete', 'translations' => [
            'name' => ['en' => 'Delete Notification', 'ar' => 'حذف إشعار'],
            'group_by' => ['en' => 'Notifications', 'ar' => 'الإشعارات'],
        ]],

        // Accounting Module
        ['key' => 'accounting.overview.view', 'translations' => [
            'name' => ['en' => 'View Accounting Overview', 'ar' => 'عرض ملخص المالي'],
            'group_by' => ['en' => 'Accounting', 'ar' => 'المالي'],
        ]],
        ['key' => 'accounting.balance.view', 'translations' => [
            'name' => ['en' => 'View Accounting Balance', 'ar' => 'عرض ميزانية المالي'],
            'group_by' => ['en' => 'Accounting', 'ar' => 'المالي'],
        ]],
        ['key' => 'accounting.expenses_keys.view', 'translations' => [
            'name' => ['en' => 'View Accounting Expenses Keys', 'ar' => 'عرض مفاتيح النفقات المالية'],
            'group_by' => ['en' => 'Accounting', 'ar' => 'المالي'],
        ]],
        ['key' => 'accounting.expenses_keys.create', 'translations' => [
            'name' => ['en' => 'Create Accounting Expenses Key', 'ar' => 'إنشاء مفتاح نفقات مالية'],
            'group_by' => ['en' => 'Accounting', 'ar' => 'المالي'],
        ]],
        ['key' => 'accounting.expenses_keys.edit', 'translations' => [
            'name' => ['en' => 'Edit Accounting Expenses Key', 'ar' => 'تعديل مفتاح نفقات مالية'],
            'group_by' => ['en' => 'Accounting', 'ar' => 'المالي'],
        ]],
        ['key' => 'accounting.expenses_keys.delete', 'translations' => [
            'name' => ['en' => 'Delete Accounting Expenses Key', 'ar' => 'حذف مفتاح نفقات مالية'],
            'group_by' => ['en' => 'Accounting', 'ar' => 'المالي'],
        ]],
        ['key' => 'accounting.expenses.view', 'translations' => [
            'name' => ['en' => 'View Accounting Expenses', 'ar' => 'عرض النفقات المالية'],
            'group_by' => ['en' => 'Accounting', 'ar' => 'المالي'],
        ]],
        ['key' => 'accounting.expenses.create', 'translations' => [
            'name' => ['en' => 'Create Accounting Expense', 'ar' => 'إنشاء نفقات مالية'],
            'group_by' => ['en' => 'Accounting', 'ar' => 'المالي'],
        ]],
        ['key' => 'accounting.expenses.edit', 'translations' => [
            'name' => ['en' => 'Edit Accounting Expense', 'ar' => 'تعديل نفقات مالية'],
            'group_by' => ['en' => 'Accounting', 'ar' => 'المالي'],
        ]],
        ['key' => 'accounting.expenses.delete', 'translations' => [
            'name' => ['en' => 'Delete Accounting Expense', 'ar' => 'حذف نفقات مالية'],
            'group_by' => ['en' => 'Accounting', 'ar' => 'المالي'],
        ]],

        // Withdraw Module
        ['key' => 'withdraw.send_money.view', 'translations' => [
            'name' => ['en' => 'View Send Money', 'ar' => 'عرض إرسال المال'],
            'group_by' => ['en' => 'Withdraw', 'ar' => 'سحب'],
        ]],
        ['key' => 'withdraw.send_money.create', 'translations' => [
            'name' => ['en' => 'Create Send Money', 'ar' => 'إنشاء إرسال المال'],
            'group_by' => ['en' => 'Withdraw', 'ar' => 'سحب'],
        ]],
        ['key' => 'withdraw.transactions.view', 'translations' => [
            'name' => ['en' => 'View Transactions', 'ar' => 'عرض المعاملات'],
            'group_by' => ['en' => 'Withdraw', 'ar' => 'سحب'],
        ]],
        ['key' => 'withdraw.vendor_requests.new.view', 'translations' => [
            'name' => ['en' => 'View New Vendor Requests', 'ar' => 'عرض طلبات الموردين الجديدة'],
            'group_by' => ['en' => 'Withdraw', 'ar' => 'سحب'],
        ]],
        ['key' => 'withdraw.vendor_requests.accept', 'translations' => [
            'name' => ['en' => 'Accept Vendor Request', 'ar' => 'قبول طلب المورد'],
            'group_by' => ['en' => 'Withdraw', 'ar' => 'سحب'],
        ]],
        ['key' => 'withdraw.vendor_requests.reject', 'translations' => [
            'name' => ['en' => 'Reject Vendor Request', 'ar' => 'رفض طلب المورد'],
            'group_by' => ['en' => 'Withdraw', 'ar' => 'سحب'],
        ]],
        ['key' => 'withdraw.vendor_requests.accepted.view', 'translations' => [
            'name' => ['en' => 'View Accepted Vendor Requests', 'ar' => 'عرض طلبات الموردين المقبولة'],
            'group_by' => ['en' => 'Withdraw', 'ar' => 'سحب'],
        ]],
        ['key' => 'withdraw.vendor_requests.rejected.view', 'translations' => [
            'name' => ['en' => 'View Rejected Vendor Requests', 'ar' => 'عرض طلبات الموردين الرفض'],
            'group_by' => ['en' => 'Withdraw', 'ar' => 'سحب'],
        ]],

        // Blog Management
        ['key' => 'blog.categories.view', 'translations' => [
            'name' => ['en' => 'View Blog Categories', 'ar' => 'عرض مجموعات المقالات'],
            'group_by' => ['en' => 'Blog Management', 'ar' => 'إدارة المقالات'],
        ]],
        ['key' => 'blog.categories.create', 'translations' => [
            'name' => ['en' => 'Create Blog Category', 'ar' => 'إنشاء مجموعات المقالات'],
            'group_by' => ['en' => 'Blog Management', 'ar' => 'إدارة المقالات'],
        ]],
        ['key' => 'blog.categories.edit', 'translations' => [
            'name' => ['en' => 'Edit Blog Category', 'ar' => 'تعديل مجموعات المقالات'],
            'group_by' => ['en' => 'Blog Management', 'ar' => 'إدارة المقالات'],
        ]],
        ['key' => 'blog.categories.delete', 'translations' => [
            'name' => ['en' => 'Delete Blog Category', 'ar' => 'حذف مجموعات المقالات'],
            'group_by' => ['en' => 'Blog Management', 'ar' => 'إدارة المقالات'],
        ]],
        ['key' => 'blog.posts.view', 'translations' => [
            'name' => ['en' => 'View Blog Posts', 'ar' => 'عرض المقالات'],
            'group_by' => ['en' => 'Blog Management', 'ar' => 'إدارة المقالات'],
        ]],
        ['key' => 'blog.posts.create', 'translations' => [
            'name' => ['en' => 'Create Blog Post', 'ar' => 'إنشاء مقال'],
            'group_by' => ['en' => 'Blog Management', 'ar' => 'إدارة المقالات'],
        ]],
        ['key' => 'blog.posts.edit', 'translations' => [
            'name' => ['en' => 'Edit Blog Post', 'ar' => 'تعديل مقال'],
            'group_by' => ['en' => 'Blog Management', 'ar' => 'إدارة المقالات'],
        ]],
        ['key' => 'blog.posts.delete', 'translations' => [
            'name' => ['en' => 'Delete Blog Post', 'ar' => 'حذف مقال'],
            'group_by' => ['en' => 'Blog Management', 'ar' => 'إدارة المقالات'],
        ]],

        // Reports
        ['key' => 'reports.registered_users.view', 'translations' => [
            'name' => ['en' => 'View Registered Users', 'ar' => 'عرض المستخدمين المسجلين'],
            'group_by' => ['en' => 'Reports', 'ar' => 'التقارير'],
        ]],
        ['key' => 'reports.area_users.view', 'translations' => [
            'name' => ['en' => 'View Area Users', 'ar' => 'عرض المستخدمين في المنطقة'],
            'group_by' => ['en' => 'Reports', 'ar' => 'التقارير'],
        ]],
        ['key' => 'reports.orders.view', 'translations' => [
            'name' => ['en' => 'View Orders', 'ar' => 'عرض الطلبات'],
            'group_by' => ['en' => 'Reports', 'ar' => 'التقارير'],
        ]],
        ['key' => 'reports.products.view', 'translations' => [
            'name' => ['en' => 'View Products', 'ar' => 'عرض المنتجات'],
            'group_by' => ['en' => 'Reports', 'ar' => 'التقارير'],
        ]],
        ['key' => 'reports.points.view', 'translations' => [
            'name' => ['en' => 'View Points', 'ar' => 'عرض النقاط'],
            'group_by' => ['en' => 'Reports', 'ar' => 'التقارير'],
        ]],

        // System Settings
        ['key' => 'system_log.view', 'translations' => [
            'name' => ['en' => 'View System Log', 'ar' => 'عرض سجل النظام'],
            'group_by' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],
        ]],

        // Area Settings
        ['key' => 'area.country.index', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'All Countries', 'ar' => 'كل البلاد'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.country.view', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'View Country', 'ar' => 'عرض البلد'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.country.create', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Create Country', 'ar' => 'إنشاء بلد'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.country.edit', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Edit Country', 'ar' => 'تعديل بلد'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.country.delete', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Delete Country', 'ar' => 'حذف بلد'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],


        ['key' => 'area.city.index', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'All Cities', 'ar' => 'كل المدن'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.city.view', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'View City', 'ar' => 'عرض المدينة'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.city.create', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Create City', 'ar' => 'إنشاء مدينة'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.city.edit', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Edit City', 'ar' => 'تعديل مدينة'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.city.delete', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Delete City', 'ar' => 'حذف مدينة'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],

        ['key' => 'area.region.index', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'All Regions', 'ar' => 'كل المناطق'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.region.view', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'View Region', 'ar' => 'عرض المنطقة'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.region.create', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Create Region', 'ar' => 'إنشاء منطقة'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.region.edit', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Edit Region', 'ar' => 'تعديل منطقة'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.region.delete', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Delete Region', 'ar' => 'حذف منطقة'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],

        ['key' => 'area.subregion.index', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'All Subregions', 'ar' => 'كل المناطق الفرعية'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.subregion.view', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'View Subregion', 'ar' => 'عرض المنطقة الفرعية'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.subregion.create', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Create Subregion', 'ar' => 'إنشاء منطقة فرعية'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.subregion.edit', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Edit Subregion', 'ar' => 'تعديل منطقة فرعية'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],
        ['key' => 'area.subregion.delete', 'type' => 'admin', 'translations' => [
            'name' => ['en' => 'Delete Subregion', 'ar' => 'حذف منطقة فرعية'],
            'group_by' => ['en' => 'Area Settings', 'ar' => 'إعدادات المنطقة'],
        ]],

        // System Settings Pages
        ['key' => 'settings.terms.view', 'translations' => [
            'name' => ['en' => 'View Terms', 'ar' => 'عرض الشروط'],
            'group_by' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],
        ]],
        ['key' => 'settings.terms.edit', 'translations' => [
            'name' => ['en' => 'Edit Terms', 'ar' => 'تعديل الشروط'],
            'group_by' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],
        ]],
        ['key' => 'settings.privacy.view', 'translations' => [
            'name' => ['en' => 'View Privacy', 'ar' => 'عرض الخصوصية'],
            'group_by' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],
        ]],
        ['key' => 'settings.privacy.edit', 'translations' => [
            'name' => ['en' => 'Edit Privacy', 'ar' => 'تعديل الخصوصية'],
            'group_by' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],
        ]],
        ['key' => 'settings.about.view', 'translations' => [
            'name' => ['en' => 'View About', 'ar' => 'عرض عن النظام'],
            'group_by' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],
        ]],
        ['key' => 'settings.about.edit', 'translations' => [
            'name' => ['en' => 'Edit About', 'ar' => 'تعديل عن النظام'],
            'group_by' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],
        ]],
        ['key' => 'settings.contact.view', 'translations' => [
            'name' => ['en' => 'View Contact', 'ar' => 'عرض الاتصال'],
            'group_by' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],
        ]],
        ['key' => 'settings.contact.edit', 'translations' => [
            'name' => ['en' => 'Edit Contact', 'ar' => 'تعديل الاتصال'],
            'group_by' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],
        ]],
        ['key' => 'settings.messages.view', 'translations' => [
            'name' => ['en' => 'View Messages', 'ar' => 'عرض الرسائل'],
            'group_by' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],
        ]],
        ['key' => 'settings.messages.delete', 'translations' => [
            'name' => ['en' => 'Delete Messages', 'ar' => 'حذف الرسائل'],
            'group_by' => ['en' => 'System Settings', 'ar' => 'إعدادات النظام'],
        ]],
    ];

    foreach ($permissions as $permissionData) {
        // Create or update the permission
        $permission = Permession::create(
            [
                'type' => $permissionData['type'] ?? 'other',
                'key' => $permissionData['key']
            ]
        );

        // Add translations if available and languages exist
        if ($languages->isNotEmpty() && isset($permissionData['translations'])) {
            foreach ($permissionData['translations']['name'] as $locale => $value) {
                $permission->setTranslation('name', $locale, $value);
            }
            foreach ($permissionData['translations']['group_by'] as $locale => $value) {
                $permission->setTranslation('group_by', $locale, $value);
            }
        }
    }
}

function permession_maker($key, $type = 'other', $translations = [])
{
    // Get languages
    $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

    $permession = Permession::query()->where('key', $key)->first();
    if ($permession) {
        $permession->delete();
    }
    $permissions = [
        ['key' => $key, 'type' => $type, 'translations' => $translations],
    ];

    foreach ($permissions as $permissionData) {
        // Create or update the permission
        $permission = Permession::create(
            [
                'type' => $permissionData['type'] ?? 'other',
                'key' => $permissionData['key']
            ]
        );

        // Add translations if available and languages exist
        if ($languages->isNotEmpty() && isset($permissionData['translations'])) {
            foreach ($permissionData['translations']['name'] as $locale => $value) {
                $permission->setTranslation('name', $locale, $value);
            }
            foreach ($permissionData['translations']['group_by'] as $locale => $value) {
                $permission->setTranslation('group_by', $locale, $value);
            }
        }
    }
}

function roles_reset()
{
    // Get languages
    $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

    Role::query()->forceDelete();

    // Define roles with translations
    $rolesData = [
        [
            'type' => 'super_admin',
            'translations' => [
                'name' => ['en' => 'Super Admin Eramo', 'ar' => 'سوبر ادمن ايرامو'],
            ]
        ],
        [
            'type' => 'vendor',
            'translations' => [
                'name' => ['en' => 'Vendor', 'ar' => 'تاجر'],
            ],
        ],
    ];

    foreach ($rolesData as $roleData) {
        // Create or update the role
        $role = Role::create([
            'type' => $roleData['type']
        ]);

        // Add translations if available and languages exist
        if ($languages->isNotEmpty() && isset($roleData['translations'])) {
            foreach ($roleData['translations']['name'] as $locale => $value) {
                $role->setTranslation('name', $locale, $value);
            }
        }

        // Assign permissions based on role type
        if (isset($roleData['type']) && $roleData['type'] == 'super_admin') {
            // Super admin gets all permissions
            $permissions = Permession::all();
            $role->permessions()->sync($permissions->pluck('id'));

            // Assign role to super admin user
            $super_admin = User::where('user_type_id', UserType::SUPER_ADMIN_TYPE)->first();
            if ($super_admin) {
                $super_admin->roles()->sync([$role->id]);
            }
        } else if ($roleData['type'] == 'vendor') {
            // Vendor gets other permissions
            $permissions = Permession::where('type', 'other')->get();
            $role->permessions()->sync($permissions->pluck('id'));
        }
    }
}

function preview($path)
{
    $fullPath = public_path('storage/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mime = mime_content_type($fullPath);

    // Inline display (no download)
    return response()->file($fullPath, [
        'Content-Type' => $mime,
        'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"'
    ]);
}

function truncateString($string, $length = 15, $append = '...')
{
    if (strlen($string) > $length) {
        return substr($string, 0, $length) . $append;
    }
    return $string;
}

function formatImage($imagePath): ?string
{
    if (!$imagePath) {
        return '';
    }

    if ($imagePath instanceof Attachment) {
        return url(asset('storage/' . $imagePath->path));
    }

    return url(asset('storage/' . $imagePath));
}

/**
 * Generate route URL with country code prefix
 *
 * @param string $name
 * @param array $parameters
 * @param bool $absolute
 * @return string
 */
function routeWithCountryCode($name, $parameters = [], $absolute = true): string
{
    $countryCode = strtolower(session('country_code') ?? 'us');

    // Add country code as first parameter
    $parameters = array_merge(['countryCode' => $countryCode], $parameters);

    return route($name, $parameters, $absolute);
}

/**
 * Get current country code from session or default
 *
 * @return string
 */
function getCountryCode(): string
{
    return session('country_code') ?? 'eg';
}

/**
 * Get currency symbol for the current country
 *
 * @return string
 */
function currency(): string
{
    try {
        $countryCode = session('country_code') ?? 'eg';
        $country = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->first();

        if ($country && $country->currency) {
            return $country->currency->symbol;
        }

        return 'EGP'; // Default fallback
    } catch (\Exception $e) {
        return 'EGP'; // Fallback in case of error
    }
}

function current_country()
{
    try {
        $countryCode = session('country_code');
        if($countryCode) {
            $country = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->first();
        } else {
            $country = \Modules\AreaSettings\app\Models\Country::default()->first();
        }
        if ($country) {
            return $country;
        }
        return '';
    } catch (\Exception $e) {
        return null; // Fallback in case of error
    }
}

function isAdmin() {
    $user = auth()->user();
    $user_type_id = $user->user_type_id ?? null;
    if (in_array($user_type_id, \App\Models\UserType::adminIds())) {
        return true;
    } else {
        return false;
    }
}

function isVendor() {
    $user = auth()->user();
    $user_type_id = $user->user_type_id ?? null;
    if (in_array($user_type_id, \App\Models\UserType::vendorIds())) {
        return true;
    } else {
        return false;
    }
}

<?php

return [
    'Dashboard' => [
        'name' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],
        'icon' => 'uil-dashboard',
        'type' => 'all',
        'sub_modules' => [
            'Dashboard' => [
                'name' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],
                'permissions' => [
                    'view' => ['name' => ['en' => 'View Dashboard', 'ar' => 'عرض لوحة التحكم'], 'key' => 'dashboard.view', 'type' => 'all'],
                ]
            ],
        ]
    ],

    'Roles' => [
        'name' => ['en' => 'Roles', 'ar' => 'الأدوار'],
        'icon' => 'uil-shield-check',
        'type' => 'all',
        'sub_modules' => [
            'Roles' => [
                'name' => ['en' => 'Roles', 'ar' => 'الأدوار'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'roles.index', 'type' => 'all'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'roles.create', 'type' => 'all'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'roles.edit', 'type' => 'all'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'roles.delete', 'type' => 'all'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'roles.show', 'type' => 'all'],
                ]
            ],
        ]
    ],

    'Admins' => [
        'name' => ['en' => 'Admins', 'ar' => 'المسؤولين'],
        'icon' => 'uil-users-alt',
        'type' => 'admin',
        'sub_modules' => [
            'Admins' => [
                'name' => ['en' => 'Admins', 'ar' => 'المسؤولين'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'admins.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'admins.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'admins.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'admins.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'admins.show', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Vendor Users' => [
        'name' => ['en' => 'Vendor Users', 'ar' => 'مستخدمي الموردين'],
        'icon' => 'uil-user-check',
        'type' => 'all',
        'sub_modules' => [
            'Vendor Users' => [
                'name' => ['en' => 'Vendor Users', 'ar' => 'مستخدمي الموردين'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'vendor-users.index', 'type' => 'all'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'vendor-users.create', 'type' => 'all'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'vendor-users.edit', 'type' => 'all'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'vendor-users.delete', 'type' => 'all'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'vendor-users.show', 'type' => 'all'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'vendor-users.change-status', 'type' => 'all'],
                ]
            ],
        ]
    ],

    'Products' => [
        'name' => ['en' => 'Products', 'ar' => 'المنتجات'],
        'icon' => 'uil-box',
        'type' => 'all',
        'sub_modules' => [
            'Products' => [
                'name' => ['en' => 'Products', 'ar' => 'المنتجات'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'products.index', 'type' => 'all'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'products.create', 'type' => 'all'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'products.edit', 'type' => 'all'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'products.delete', 'type' => 'all'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'products.show', 'type' => 'all'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'products.change-status', 'type' => 'all'],
                    'activation' => ['name' => ['en' => 'Change Activation', 'ar' => 'تغيير التنشيط'], 'key' => 'products.change-activation', 'type' => 'all'],
                    'stock-setup' => ['name' => ['en' => 'Stock Setup', 'ar' => 'إعداد المخزون'], 'key' => 'products.stock-setup', 'type' => 'all'],
                ]
            ],
        ]
    ],

    'Product Bank' => [
        'name' => ['en' => 'Product Bank', 'ar' => 'بنك المنتجات'],
        'icon' => 'uil-estate',
        'type' => 'all',
        'sub_modules' => [
            'Product Bank' => [
                'name' => ['en' => 'Product Bank', 'ar' => 'بنك المنتجات'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'products.bank', 'type' => 'all'],
                    'activation' => ['name' => ['en' => 'Change Activation', 'ar' => 'تغيير التنشيط'], 'key' => 'products.bank.change-activation', 'type' => 'all'],
                    'trash' => ['name' => ['en' => 'Trash Vendor Product', 'ar' => 'نقل لسلة المهملات'], 'key' => 'products.bank.vendor-product.trash', 'type' => 'all'],
                ]
            ],
        ]
    ],

    'Brands' => [
        'name' => ['en' => 'Brands', 'ar' => 'العلامات التجارية'],
        'icon' => 'uil-pricetag-alt',
        'type' => 'admin',
        'sub_modules' => [
            'Brands' => [
                'name' => ['en' => 'Brands', 'ar' => 'العلامات التجارية'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'brands.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'brands.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'brands.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'brands.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'brands.show', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Promocodes' => [
        'name' => ['en' => 'Promocodes', 'ar' => 'أكواد الخصم'],
        'icon' => 'uil-percentage',
        'type' => 'admin',
        'sub_modules' => [
            'Promocodes' => [
                'name' => ['en' => 'Promocodes', 'ar' => 'أكواد الخصم'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'promocodes.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'promocodes.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'promocodes.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'promocodes.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'promocodes.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'promocodes.change-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Taxes' => [
        'name' => ['en' => 'Taxes', 'ar' => 'الضرائب'],
        'icon' => 'uil-bill',
        'type' => 'admin',
        'sub_modules' => [
            'Taxes' => [
                'name' => ['en' => 'Taxes', 'ar' => 'الضرائب'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'taxes.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'taxes.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'taxes.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'taxes.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'taxes.show', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Variant Keys' => [
        'name' => ['en' => 'Variant Keys', 'ar' => 'مفاتيح التنوع'],
        'icon' => 'uil-key-skeleton',
        'type' => 'admin',
        'sub_modules' => [
            'Variant Keys' => [
                'name' => ['en' => 'Variant Keys', 'ar' => 'مفاتيح التنوع'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'variant-keys.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'variant-keys.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'variant-keys.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'variant-keys.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'variant-keys.show', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Variants Configurations' => [
        'name' => ['en' => 'Variants Configurations', 'ar' => 'تكوينات التنوع'],
        'icon' => 'uil-layer-group',
        'type' => 'admin',
        'sub_modules' => [
            'Variants Configurations' => [
                'name' => ['en' => 'Variants Configurations', 'ar' => 'تكوينات التنوع'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'variants-configurations.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'variants-configurations.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'variants-configurations.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'variants-configurations.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'variants-configurations.show', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Bundle Categories' => [
        'name' => ['en' => 'Bundle Categories', 'ar' => 'تصنيفات الحزم'],
        'icon' => 'uil-package',
        'type' => 'admin',
        'sub_modules' => [
            'Bundle Categories' => [
                'name' => ['en' => 'Bundle Categories', 'ar' => 'تصنيفات الحزم'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'bundle-categories.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'bundle-categories.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'bundle-categories.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'bundle-categories.delete', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Toggle Status', 'ar' => 'تبديل الحالة'], 'key' => 'bundle-categories.toggle-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Occasions' => [
        'name' => ['en' => 'Occasions', 'ar' => 'المناسبات'],
        'icon' => 'uil-calender',
        'type' => 'all',
        'sub_modules' => [
            'Occasions' => [
                'name' => ['en' => 'Occasions', 'ar' => 'المناسبات'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'occasions.index', 'type' => 'all'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'occasions.create', 'type' => 'all'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'occasions.edit', 'type' => 'all'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'occasions.delete', 'type' => 'all'],
                    'status' => ['name' => ['en' => 'Toggle Status', 'ar' => 'تبديل الحالة'], 'key' => 'occasions.toggle-status', 'type' => 'all'],
                ]
            ],
        ]
    ],

    'Bundles' => [
        'name' => ['en' => 'Bundles', 'ar' => 'الحزم'],
        'icon' => 'uil-box',
        'type' => 'all',
        'sub_modules' => [
            'Bundles' => [
                'name' => ['en' => 'Bundles', 'ar' => 'الحزم'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'bundles.index', 'type' => 'all'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'bundles.create', 'type' => 'all'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'bundles.edit', 'type' => 'all'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'bundles.delete', 'type' => 'all'],
                    'status' => ['name' => ['en' => 'Toggle Status', 'ar' => 'تبديل الحالة'], 'key' => 'bundles.toggle-status', 'type' => 'all'],
                    'approval' => ['name' => ['en' => 'Change Approval', 'ar' => 'تغيير الموافقة'], 'key' => 'bundles.change-approval', 'type' => 'all'],
                ]
            ],
        ]
    ],

    'Reviews' => [
        'name' => ['en' => 'Reviews', 'ar' => 'التقييمات'],
        'icon' => 'uil-star',
        'type' => 'admin',
        'sub_modules' => [
            'Reviews' => [
                'name' => ['en' => 'Reviews', 'ar' => 'التقييمات'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'reviews.index', 'type' => 'admin'],
                    'approve' => ['name' => ['en' => 'Approve', 'ar' => 'قبول'], 'key' => 'reviews.approve', 'type' => 'admin'],
                    'reject' => ['name' => ['en' => 'Reject', 'ar' => 'رفض'], 'key' => 'reviews.reject', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Departments' => [
        'name' => ['en' => 'Departments', 'ar' => 'الأقسام'],
        'icon' => 'uil-sitemap',
        'type' => 'admin',
        'sub_modules' => [
            'Departments' => [
                'name' => ['en' => 'Departments', 'ar' => 'الأقسام'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'departments.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'departments.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'departments.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'departments.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'departments.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'departments.change-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Categories' => [
        'name' => ['en' => 'Categories', 'ar' => 'التصنيفات الرئيسية'],
        'icon' => 'uil-apps',
        'type' => 'admin',
        'sub_modules' => [
            'Categories' => [
                'name' => ['en' => 'Categories', 'ar' => 'التصنيفات الرئيسية'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'categories.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'categories.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'categories.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'categories.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'categories.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'categories.change-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Sub Categories' => [
        'name' => ['en' => 'Sub Categories', 'ar' => 'التصنيفات الفرعية'],
        'icon' => 'uil-list-ui-alt',
        'type' => 'admin',
        'sub_modules' => [
            'Sub Categories' => [
                'name' => ['en' => 'Sub Categories', 'ar' => 'التصنيفات الفرعية'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'subcategories.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'subcategories.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'subcategories.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'subcategories.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'subcategories.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'subcategories.change-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Countries' => [
        'name' => ['en' => 'Countries', 'ar' => 'الدول'],
        'icon' => 'uil-globe',
        'type' => 'admin',
        'sub_modules' => [
            'Countries' => [
                'name' => ['en' => 'Countries', 'ar' => 'الدول'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'area.country.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'area.country.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'area.country.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'area.country.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'area.country.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'area.country.change-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Cities' => [
        'name' => ['en' => 'Cities', 'ar' => 'المدن'],
        'icon' => 'uil-map-marker',
        'type' => 'admin',
        'sub_modules' => [
            'Cities' => [
                'name' => ['en' => 'Cities', 'ar' => 'المدن'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'area.city.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'area.city.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'area.city.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'area.city.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'area.city.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'area.city.change-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Regions' => [
        'name' => ['en' => 'Regions', 'ar' => 'المناطق'],
        'icon' => 'uil-location-point',
        'type' => 'admin',
        'sub_modules' => [
            'Regions' => [
                'name' => ['en' => 'Regions', 'ar' => 'المناطق'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'area.region.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'area.region.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'area.region.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'area.region.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'area.region.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'area.region.change-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Sub Regions' => [
        'name' => ['en' => 'Sub Regions', 'ar' => 'المناطق الفرعية'],
        'icon' => 'uil-map-pin',
        'type' => 'admin',
        'sub_modules' => [
            'Sub Regions' => [
                'name' => ['en' => 'Sub Regions', 'ar' => 'المناطق الفرعية'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'area.subregion.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'area.subregion.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'area.subregion.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'area.subregion.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'area.subregion.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'area.subregion.change-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Orders' => [
        'name' => ['en' => 'Orders', 'ar' => 'الطلبات'],
        'icon' => 'uil-shopping-cart',
        'type' => 'all',
        'sub_modules' => [
            'Orders' => [
                'name' => ['en' => 'Orders', 'ar' => 'الطلبات'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'orders.index', 'type' => 'all'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'orders.create', 'type' => 'all'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'orders.edit', 'type' => 'all'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'orders.delete', 'type' => 'all'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'orders.show', 'type' => 'all'],
                    'stage' => ['name' => ['en' => 'Change Stage', 'ar' => 'تغيير المرحلة'], 'key' => 'orders.change-stage', 'type' => 'all'],
                ]
            ],
        ]
    ],

    'Order Stages' => [
        'name' => ['en' => 'Order Stages', 'ar' => 'مراحل الطلب'],
        'icon' => 'uil-step-forward',
        'type' => 'admin',
        'sub_modules' => [
            'Order Stages' => [
                'name' => ['en' => 'Order Stages', 'ar' => 'مراحل الطلب'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'order-stages.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'order-stages.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'order-stages.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'order-stages.delete', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Toggle Status', 'ar' => 'تبديل الحالة'], 'key' => 'order-stages.toggle-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Shippings' => [
        'name' => ['en' => 'Shippings', 'ar' => 'الشحن'],
        'icon' => 'uil-truck',
        'type' => 'admin',
        'sub_modules' => [
            'Shippings' => [
                'name' => ['en' => 'Shippings', 'ar' => 'الشحن'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'shippings.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'shippings.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'shippings.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'shippings.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'shippings.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'shippings.change-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Customers' => [
        'name' => ['en' => 'Customers', 'ar' => 'العملاء'],
        'icon' => 'uil-user-circle',
        'type' => 'admin',
        'sub_modules' => [
            'Customers' => [
                'name' => ['en' => 'Customers', 'ar' => 'العملاء'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'customers.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'customers.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'customers.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'customers.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'customers.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'customers.change-status', 'type' => 'admin'],
                    'verification' => ['name' => ['en' => 'Change Verification', 'ar' => 'تغيير التحقق'], 'key' => 'customers.change-verification', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Vendors' => [
        'name' => ['en' => 'Vendors', 'ar' => 'الموردين'],
        'icon' => 'uil-store',
        'type' => 'admin',
        'sub_modules' => [
            'Vendors' => [
                'name' => ['en' => 'Vendors', 'ar' => 'الموردين'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'vendors.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'vendors.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'vendors.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'vendors.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'vendors.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'vendors.change-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Vendor Requests' => [
        'name' => ['en' => 'Vendor Requests', 'ar' => 'طلبات الموردين'],
        'icon' => 'uil-clipboard-notes',
        'type' => 'admin',
        'sub_modules' => [
            'Vendor Requests' => [
                'name' => ['en' => 'Vendor Requests', 'ar' => 'طلبات الموردين'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'vendor-requests.index', 'type' => 'admin'],
                    'approve' => ['name' => ['en' => 'Approve', 'ar' => 'قبول'], 'key' => 'vendor-requests.approve', 'type' => 'admin'],
                    'reject' => ['name' => ['en' => 'Reject', 'ar' => 'رفض'], 'key' => 'vendor-requests.reject', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'vendor-requests.delete', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Withdraws' => [
        'name' => ['en' => 'Withdraws', 'ar' => 'السحوبات'],
        'icon' => 'uil-wallet',
        'type' => 'all',
        'sub_modules' => [
            'Withdraws' => [
                'name' => ['en' => 'Withdraws', 'ar' => 'السحوبات'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'withdraw.index', 'type' => 'all'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'withdraw.create', 'type' => 'all'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'withdraw.edit', 'type' => 'all'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'withdraw.delete', 'type' => 'all'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'withdraw.show', 'type' => 'all'],
                    'sendMoney' => ['name' => ['en' => 'Send Money', 'ar' => 'إرسال أموال'], 'key' => 'withdraw.sendMoney', 'type' => 'all'],
                    'transactions' => ['name' => ['en' => 'Transactions Database', 'ar' => 'قاعدة بيانات المعاملات'], 'key' => 'withdraw.allTransactionsDatabase', 'type' => 'all'],
                    'all_transactions' => ['name' => ['en' => 'All Transactions', 'ar' => 'كل المعاملات'], 'key' => 'withdraw.allTransactions', 'type' => 'all'],
                    'requests' => ['name' => ['en' => 'Transaction Requests', 'ar' => 'طلبات المعاملات'], 'key' => 'withdraw.transactionsRequests', 'type' => 'all'],
                    'change_status' => ['name' => ['en' => 'Change Status', 'ar' => 'تغيير الحالة'], 'key' => 'withdraw.changeTransactionRequestsStatus', 'type' => 'all'],
                ]
            ],
        ]
    ],

    'Currencies' => [
        'name' => ['en' => 'Currencies', 'ar' => 'العملات'],
        'icon' => 'uil-money-bill',
        'type' => 'admin',
        'sub_modules' => [
            'Currencies' => [
                'name' => ['en' => 'Currencies', 'ar' => 'العملات'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'system.currency.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'system.currency.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'system.currency.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'system.currency.delete', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Activity Logs' => [
        'name' => ['en' => 'Activity Logs', 'ar' => 'سجلات النشاط'],
        'icon' => 'uil-history',
        'type' => 'admin',
        'sub_modules' => [
            'Activity Logs' => [
                'name' => ['en' => 'Activity Logs', 'ar' => 'سجلات النشاط'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'settings.logs.view', 'type' => 'admin'],
                    'view' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'settings.logs.show', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Messages' => [
        'name' => ['en' => 'Messages', 'ar' => 'الرسائل'],
        'icon' => 'uil-envelope',
        'type' => 'admin',
        'sub_modules' => [
            'Messages' => [
                'name' => ['en' => 'Messages', 'ar' => 'الرسائل'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'messages.index', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'messages.delete', 'type' => 'admin'],
                    'mark_read' => ['name' => ['en' => 'Mark as Read', 'ar' => 'تم القراءة'], 'key' => 'messages.mark-read', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Points Settings' => [
        'name' => ['en' => 'Points Settings', 'ar' => 'إعدادات النقاط'],
        'icon' => 'uil-award',
        'type' => 'admin',
        'sub_modules' => [
            'Points Settings' => [
                'name' => ['en' => 'Points Settings', 'ar' => 'إعدادات النقاط'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'points-settings.index', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'points-settings.update', 'type' => 'admin'],
                    'toggle' => ['name' => ['en' => 'Toggle System', 'ar' => 'تبديل النظام'], 'key' => 'points-settings.points-system.toggle-enabled', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'User Points' => [
        'name' => ['en' => 'User Points', 'ar' => 'نقاط المستخدمين'],
        'icon' => 'uil-medal',
        'type' => 'admin',
        'sub_modules' => [
            'User Points' => [
                'name' => ['en' => 'User Points', 'ar' => 'نقاط المستخدمين'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'points-settings.user-points.index', 'type' => 'admin'],
                    'adjust' => ['name' => ['en' => 'Adjust Points', 'ar' => 'تعديل النقاط'], 'key' => 'points-settings.user-points.adjust', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Ads' => [
        'name' => ['en' => 'Ads', 'ar' => 'الإعلانات'],
        'icon' => 'uil-image-v',
        'type' => 'admin',
        'sub_modules' => [
            'Ads' => [
                'name' => ['en' => 'Ads', 'ar' => 'الإعلانات'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'ads.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'ads.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'ads.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'ads.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'ads.show', 'type' => 'admin'],
                    'toggle' => ['name' => ['en' => 'Toggle Status', 'ar' => 'تبديل الحالة'], 'key' => 'ads.toggle-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Sliders' => [
        'name' => ['en' => 'Sliders', 'ar' => 'السلايدر'],
        'icon' => 'uil-presentation-play',
        'type' => 'admin',
        'sub_modules' => [
            'Sliders' => [
                'name' => ['en' => 'Sliders', 'ar' => 'السلايدر'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'sliders.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'sliders.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'sliders.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'sliders.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'sliders.show', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Blogs' => [
        'name' => ['en' => 'Blogs', 'ar' => 'المدونات'],
        'icon' => 'uil-document-layout-left',
        'type' => 'admin',
        'sub_modules' => [
            'Blogs' => [
                'name' => ['en' => 'Blogs', 'ar' => 'المدونات'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'blogs.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'blogs.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'blogs.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'blogs.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'blogs.show', 'type' => 'admin'],
                    'status' => ['name' => ['en' => 'Toggle Status', 'ar' => 'تبديل الحالة'], 'key' => 'blogs.toggle-status', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'FAQs' => [
        'name' => ['en' => 'FAQs', 'ar' => 'الأسئلة الشائعة'],
        'icon' => 'uil-question-circle',
        'type' => 'admin',
        'sub_modules' => [
            'FAQs' => [
                'name' => ['en' => 'FAQs', 'ar' => 'الأسئلة الشائعة'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'faqs.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'faqs.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'faqs.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'faqs.delete', 'type' => 'admin'],
                    'show' => ['name' => ['en' => 'View', 'ar' => 'عرض'], 'key' => 'faqs.show', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Information Pages' => [
        'name' => ['en' => 'Information Pages', 'ar' => 'صفحات المعلومات'],
        'icon' => 'uil-file-info-alt',
        'type' => 'admin',
        'sub_modules' => [
            'Information Pages' => [
                'name' => ['en' => 'Information Pages', 'ar' => 'صفحات المعلومات'],
                'permissions' => [
                    'site_info' => ['name' => ['en' => 'Site Information', 'ar' => 'معلومات الموقع'], 'key' => 'site-information.index', 'type' => 'admin'],
                    'privacy_policy' => ['name' => ['en' => 'Privacy Policy', 'ar' => 'سياسة الخصوصية'], 'key' => 'privacy-policy.index', 'type' => 'admin'],
                    'return_policy' => ['name' => ['en' => 'Return Policy', 'ar' => 'سياسة الاسترجاع'], 'key' => 'return-policy.index', 'type' => 'admin'],
                    'service_terms' => ['name' => ['en' => 'Service Terms', 'ar' => 'شروط الخدمة'], 'key' => 'service-terms.index', 'type' => 'admin'],
                    'terms_conditions' => ['name' => ['en' => 'Terms & Conditions', 'ar' => 'الشروط والأحكام'], 'key' => 'terms-conditions.index', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Features' => [
        'name' => ['en' => 'Features', 'ar' => 'المميزات'],
        'icon' => 'uil-bright',
        'type' => 'admin',
        'sub_modules' => [
            'Features' => [
                'name' => ['en' => 'Features', 'ar' => 'المميزات'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'features.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create/Update', 'ar' => 'إنشاء/تحديث'], 'key' => 'features.store', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Footer Content' => [
        'name' => ['en' => 'Footer Content', 'ar' => 'محتوى التذييل'],
        'icon' => 'uil-arrow-down',
        'type' => 'admin',
        'sub_modules' => [
            'Footer Content' => [
                'name' => ['en' => 'Footer Content', 'ar' => 'محتوى التذييل'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'footer-content.index', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create/Update', 'ar' => 'إنشاء/تحديث'], 'key' => 'footer-content.store', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Accounting Overview' => [
        'name' => ['en' => 'Accounting Overview', 'ar' => 'نظرة عامة على المحاسبة'],
        'icon' => 'uil-chart-line',
        'type' => 'admin',
        'sub_modules' => [
            'Overview' => [
                'name' => ['en' => 'Overview', 'ar' => 'نظرة عامة'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'accounting.overview.view', 'type' => 'admin'],
                ]
            ],
        ]
    ],

    'Expenses' => [
        'name' => ['en' => 'Expenses', 'ar' => 'المصروفات'],
        'icon' => 'uil-money-withdrawing',
        'type' => 'admin',
        'sub_modules' => [
            'Expenses' => [
                'name' => ['en' => 'Expenses', 'ar' => 'المصروفات'],
                'permissions' => [
                    'read' => ['name' => ['en' => 'Read', 'ar' => 'قراءة'], 'key' => 'accounting.expenses.view', 'type' => 'admin'],
                    'create' => ['name' => ['en' => 'Create', 'ar' => 'إنشاء'], 'key' => 'accounting.expenses.create', 'type' => 'admin'],
                    'edit' => ['name' => ['en' => 'Edit', 'ar' => 'تعديل'], 'key' => 'accounting.expenses.edit', 'type' => 'admin'],
                    'delete' => ['name' => ['en' => 'Delete', 'ar' => 'حذف'], 'key' => 'accounting.expenses.delete', 'type' => 'admin'],
                ]
            ],
        ]
    ],

];

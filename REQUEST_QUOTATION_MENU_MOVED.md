# Request Quotations - Moved to Vendors Section

## ✅ Changes Made

### 1. Menu Translations Updated

#### English (`lang/en/menu.php`)
- ❌ **Removed** from `business activities` section
- ✅ **Added** to `vendors` section:
```php
'vendors' => [
    'title' => 'vendors',
    'all' => 'all',
    'create' => 'create',
    'request_quotations' => [
        'title' => 'Request Quotations',
        'all_requests' => 'All Requests',
        'archived_requests' => 'Archived Requests',
    ],
],
```

#### Arabic (`lang/ar/menu.php`)
- ❌ **Removed** from `business activities` section
- ✅ **Added** to `vendors` section:
```php
'vendors' => [
    'title' => 'الموردين',
    'all' => 'الكل',
    'create' => 'إضافة',
    'request_quotations' => [
        'title' => 'طلبات عروض الأسعار',
        'all_requests' => 'جميع الطلبات',
        'archived_requests' => 'الطلبات المؤرشفة',
    ],
],
```

### 2. Menu Structure

**Before:**
```
📁 Business Activities
  └── Request Quotations
```

**After:**
```
📁 Vendors
  ├── All
  ├── Create
  └── Request Quotations
      ├── All Requests
      └── Archived Requests
```

## 📝 Next Steps

### Update Sidebar Menu View

You need to update the sidebar menu blade file to reflect this change. The file is likely located at:
- `resources/views/layouts/sidebar.blade.php` or
- `resources/views/partials/sidebar.blade.php` or
- Similar location

Look for the menu rendering code and update it to show Request Quotations under Vendors section.

### Example Menu Structure:
```blade
<!-- Vendors Section -->
<li class="menu-item">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons uil uil-store"></i>
        <div>{{ __('menu.vendors.title') }}</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item">
            <a href="{{ route('admin.vendors.index') }}" class="menu-link">
                <div>{{ __('menu.vendors.all') }}</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin.vendors.create') }}" class="menu-link">
                <div>{{ __('menu.vendors.create') }}</div>
            </a>
        </li>
        
        <!-- Request Quotations Submenu -->
        <li class="menu-item">
            <a href="#" class="menu-link menu-toggle">
                <div>{{ __('menu.vendors.request_quotations.title') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin.request-quotations.index') }}" class="menu-link">
                        <div>{{ __('menu.vendors.request_quotations.all_requests') }}</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.request-quotations.archived') }}" class="menu-link">
                        <div>{{ __('menu.vendors.request_quotations.archived_requests') }}</div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</li>
```

## 🔍 Files Modified

1. `lang/en/menu.php` - English menu translations
2. `lang/ar/menu.php` - Arabic menu translations

## ✅ Result

Request Quotations is now logically grouped under Vendors section, which makes more sense since:
- Request Quotations are sent to vendors
- Vendors send offers
- It's part of vendor management workflow

The menu structure is now cleaner and more intuitive!

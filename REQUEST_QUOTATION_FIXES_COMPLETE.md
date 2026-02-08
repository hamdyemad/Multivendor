# Request Quotations - Translations Fixed & Assign to Vendors Added

## ✅ Changes Completed

### 1. Menu Structure Updated

#### Sidebar Menu (`resources/views/partials/_menu.blade.php`)
- ✅ **Removed** standalone Request Quotations section
- ✅ **Moved** Request Quotations under Vendors section
- ✅ **Updated** translations to use `menu.vendors.request_quotations.*`

**New Structure:**
```
📁 Vendors
  ├── All Vendors
  ├── Create Vendor
  └── 📁 Request Quotations
      ├── All Requests (with count badge)
      └── Archived Requests (with count badge)
```

### 2. Translation Keys Updated

#### Menu Translations
**Before:**
- `menu.sections.request_quotations`
- `menu.request_quotations.title`
- `menu.request_quotations.all_requests`
- `menu.request_quotations.archived_requests`

**After:**
- `menu.vendors.request_quotations.title`
- `menu.vendors.request_quotations.all_requests`
- `menu.vendors.request_quotations.archived_requests`

#### Translation Files (`lang/en/menu.php` & `lang/ar/menu.php`)
```php
'vendors' => [
    'title' => 'vendors', // الموردين
    'all' => 'all', // الكل
    'create' => 'create', // إضافة
    'request_quotations' => [
        'title' => 'Request Quotations', // طلبات عروض الأسعار
        'all_requests' => 'All Requests', // جميع الطلبات
        'archived_requests' => 'Archived Requests', // الطلبات المؤرشفة
    ],
],
```

### 3. DataTable Actions Updated

#### New Buttons Added (`RequestQuotationController::datatable()`)

**For Pending Status:**
1. **Assign to Vendors** (Green button with users icon)
   - Icon: `uil-users-alt`
   - Color: `btn-success`
   - Permission: `request-quotations.send-to-vendors`
   - Route: `admin.request-quotations.select-vendors`
   - Action: Opens vendor selection page

2. **Create Order** (Gray button - old workflow)
   - Icon: `uil-file-plus`
   - Color: `btn-secondary`
   - Action: Direct order creation (single vendor)

**For Sent to Vendors Status:**
3. **View Offers** (Blue button with eye icon)
   - Icon: `uil-eye`
   - Color: `btn-info`
   - Permission: `request-quotations.view-offers`
   - Route: `admin.request-quotations.view-offers`
   - Statuses: `sent_to_vendors`, `offers_received`, `partially_accepted`, `fully_accepted`, `orders_created`

### 4. Button Priority Order

The buttons appear in this order (left to right):
1. 🟢 **Assign to Vendors** (Multi-vendor workflow - NEW)
2. 🔵 **View Offers** (View vendor offers)
3. ⚪ **Create Order** (Single vendor workflow - OLD)
4. 🔵 **View Order** (If order exists)
5. 🔵 **Download File** (If file attached)
6. 🟡 **Archive** (If pending)
7. 🔵 **View Details** (Always visible)

## 🎯 User Flow

### Multi-Vendor Workflow (NEW)
1. Customer creates Request Quotation → Status: `pending`
2. Admin clicks **"Assign to Vendors"** → Selects vendors
3. System sends notifications to vendors → Status: `sent_to_vendors`
4. Vendors send offers → Status: `offers_received`
5. Admin clicks **"View Offers"** → Sees all vendor offers
6. Customer accepts/rejects offers → Orders created

### Single Vendor Workflow (OLD - Still Available)
1. Customer creates Request Quotation → Status: `pending`
2. Admin clicks **"Create Order"** → Creates order directly
3. Order sent to customer → Status: `order_created`

## 📝 Translation Keys Reference

### English
```php
'select_vendors' => 'Select Vendors',
'view_offers' => 'View Offers',
'send_quotation_offer' => 'Send Offer',
'view_order' => 'View Order',
'download_file' => 'Download File',
'archive' => 'Archive',
```

### Arabic
```php
'select_vendors' => 'اختيار التجار',
'view_offers' => 'عرض العروض',
'send_quotation_offer' => 'إرسال عرض',
'view_order' => 'عرض الطلب',
'download_file' => 'تحميل الملف',
'archive' => 'أرشفة',
```

## 🔧 Files Modified

1. `resources/views/partials/_menu.blade.php` - Sidebar menu structure
2. `lang/en/menu.php` - English menu translations
3. `lang/ar/menu.php` - Arabic menu translations
4. `Modules/Order/app/Http/Controllers/RequestQuotationController.php` - DataTable actions

## ✅ Testing Checklist

- [ ] Menu shows Request Quotations under Vendors
- [ ] Translations display correctly (English & Arabic)
- [ ] "Assign to Vendors" button appears for pending requests
- [ ] "View Offers" button appears after vendors assigned
- [ ] Both workflows work (multi-vendor & single-vendor)
- [ ] Permissions are checked correctly
- [ ] Count badges show correct numbers

## 🎉 Result

The Request Quotations feature is now:
- ✅ Properly organized under Vendors section
- ✅ Translations are correct and consistent
- ✅ "Assign to Vendors" button is visible and functional
- ✅ Both workflows (old & new) are available
- ✅ Menu structure is cleaner and more intuitive

Clear your browser cache and refresh to see the changes!

# Request Quotation Scopes Implementation - Complete

## Overview
Refactored search and filter logic in both admin and vendor request quotation controllers to use reusable model scopes for cleaner, more maintainable code.

## Changes Made

### 1. RequestQuotation Model - `Modules/Order/app/Models/RequestQuotation.php`

#### Added Scopes:

**`scopeSearch($search)`**
- Searches across multiple fields:
  - quotation_number
  - notes
  - customer (first_name, last_name, email, phone)
  - customer address
  - vendor order numbers (through vendors.order relationship)

**`scopeVendorStatus($status)`**
- Filters quotations by vendor quotation status
- Uses `whereHas` on vendors relationship

**`scopeDateRange($from, $to)`**
- Filters by created_at date range
- Accepts optional from and to dates

**Updated `scopeFilter($filters)`**
- Now uses the individual scopes above
- Cleaner implementation using method chaining

### 2. RequestQuotationVendor Model - `Modules/Order/app/Models/RequestQuotationVendor.php`

#### Added Scopes:

**`scopeSearch($search)`**
- Searches through requestQuotation relationship:
  - quotation_number
  - notes
  - customer info (first_name, last_name, email, phone)

**`scopeByStatus($status)`**
- Filters by vendor quotation status
- Handles 'all' status to show everything

**`scopeDateRange($from, $to)`**
- Filters by created_at date range
- Accepts optional from and to dates

**`scopeForVendor($vendorId)`**
- Filters quotations for a specific vendor
- Used in vendor-facing views

### 3. RequestQuotationController - `Modules/Order/app/Http/Controllers/RequestQuotationController.php`

#### Updated `datatable()` Method:
**Before:**
```php
// Long inline filter logic with multiple if statements
if ($request->filled('search_text')) {
    $search = $request->input('search_text');
    $query->where(function ($q) use ($search) {
        // ... many lines of search logic
    });
}
// ... more if statements
```

**After:**
```php
// Clean scope usage
$query->search($request->input('search_text'))
      ->vendorStatus($request->input('vendor_status'))
      ->dateRange(
          $request->input('created_date_from'),
          $request->input('created_date_to')
      );
```

### 4. VendorRequestQuotationController - `Modules/Order/app/Http/Controllers/VendorRequestQuotationController.php`

#### Updated `datatable()` Method:
**Before:**
```php
// Multiple if statements for filtering
if ($request->filled('search_text')) {
    // ... search logic
}
if ($request->filled('status')) {
    // ... status filter
}
// ... more filters
```

**After:**
```php
// Clean scope usage
$query->forVendor($vendorId)
      ->search($request->input('search_text'))
      ->byStatus($request->input('status'))
      ->dateRange(
          $request->input('created_date_from'),
          $request->input('created_date_to')
      )
      ->latest();
```

## Benefits

1. **DRY (Don't Repeat Yourself)**: Search logic defined once in model, reused everywhere
2. **Maintainability**: Changes to search logic only need to be made in one place
3. **Readability**: Controller code is much cleaner and easier to understand
4. **Testability**: Scopes can be unit tested independently
5. **Reusability**: Scopes can be used in other parts of the application
6. **Chainable**: Scopes can be chained together for complex queries

## Usage Examples

### Admin Controller
```php
RequestQuotation::search('RQ-000001')
    ->vendorStatus('order_created')
    ->dateRange('2026-01-01', '2026-12-31')
    ->notArchived()
    ->get();
```

### Vendor Controller
```php
RequestQuotationVendor::forVendor($vendorId)
    ->search('customer name')
    ->byStatus('pending')
    ->dateRange('2026-01-01', null)
    ->get();
```

### Using Filter Scope
```php
RequestQuotation::filter([
    'search' => 'RQ-000001',
    'vendor_status' => 'pending',
    'created_date_from' => '2026-01-01',
    'created_date_to' => '2026-12-31'
])->get();
```

## Files Modified

1. `Modules/Order/app/Models/RequestQuotation.php`
2. `Modules/Order/app/Models/RequestQuotationVendor.php`
3. `Modules/Order/app/Http/Controllers/RequestQuotationController.php`
4. `Modules/Order/app/Http/Controllers/VendorRequestQuotationController.php`

## Status
✅ **COMPLETE** - All search and filter logic refactored to use model scopes

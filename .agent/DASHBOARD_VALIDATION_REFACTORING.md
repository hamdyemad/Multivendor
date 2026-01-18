# Dashboard Controllers Validation Refactoring

## ✅ Changes Made

### 1. Created Dashboard-Specific Form Requests

**Location:** `Modules/Refund/app/Http/Requests/`

#### A. UpdateRefundSettingRequest
- Validates refund settings update
- Rules: `customer_pays_return_shipping` (boolean), `refund_processing_days` (1-365)
- Custom attribute names and messages
- Used in: `RefundSettingController@update`

#### B. RejectRefundRequest
- Validates refund rejection
- Rules: `rejection_reason` (required, max 1000 chars)
- Custom attribute names and messages
- Used in: `RefundRequestController@reject`

#### C. ChangeRefundStatusRequest
- Validates status changes
- Rules: `status` (in: in_progress, picked_up, refunded)
- Custom attribute names and messages
- Used in: `RefundRequestController@changeStatus`

#### D. UpdateRefundNotesRequest
- Validates notes update
- Rules: `notes` (required, max 1000 chars)
- Custom attribute names and messages
- Used in: `RefundRequestController@updateNotes`

### 2. Enhanced scopeFilters in RefundRequest Model

**Added Filters:**
- ✅ `status` - Filter by refund status
- ✅ `vendor_status` - Filter by vendor status
- ✅ `customer_id` - Filter by customer
- ✅ `vendor_id` - Filter by vendor
- ✅ `date_from` / `date_to` - Date range filtering
- ✅ `search` - Search in refund number, order number, customer name, vendor name
- ✅ `current_vendor_id` - Special filter for vendor-specific queries (datatable)

**Search Capabilities:**
- Refund number (LIKE)
- Order number (via relationship)
- Customer name (via relationship)
- Vendor name (JSON fields - en/ar)

### 3. Refactored Controllers

#### RefundSettingController
**Before:**
```php
public function update(Request $request)
{
    $request->validate([
        'customer_pays_return_shipping' => 'required|boolean',
        'refund_processing_days' => 'required|integer|min:1|max:365',
    ]);
    
    $settings->update([
        'customer_pays_return_shipping' => $request->customer_pays_return_shipping,
        'refund_processing_days' => $request->refund_processing_days,
    ]);
}
```

**After:**
```php
public function update(UpdateRefundSettingRequest $request)
{
    $settings->update($request->validated());
}
```

#### RefundRequestController
**Before:**
```php
public function datatable(Request $request)
{
    $query = RefundRequest::with([...]);
    
    // Manual filtering
    if (!isAdmin()) {
        $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
        if ($vendor) {
            $query->where('vendor_id', $vendor->id);
        }
    }
    
    if ($request->has('status_filter') && $request->status_filter != '') {
        $query->where('status', $request->status_filter);
    }
    
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            // Complex search logic...
        });
    }
    
    // More filters...
}
```

**After:**
```php
public function datatable(Request $request)
{
    // Build filters array
    $filters = [
        'status' => $request->status_filter ?? null,
        'search' => $request->search ?? null,
        'date_from' => $request->created_date_from ?? null,
        'date_to' => $request->created_date_to ?? null,
    ];
    
    // Add vendor filter if not admin
    if (!isAdmin()) {
        $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
        if ($vendor) {
            $filters['current_vendor_id'] = $vendor->id;
        }
    }

    // Build query with scopeFilters
    $query = RefundRequest::with([...])
        ->scopeFilters($filters);
}
```

## 📊 File Structure

```
Modules/Refund/app/
├── Http/
│   ├── Controllers/
│   │   ├── RefundSettingController.php          ✅ Updated
│   │   └── RefundRequestController.php          ✅ Updated
│   └── Requests/
│       ├── Api/                                  (API requests - separate)
│       │   ├── StoreRefundRequestRequest.php
│       │   └── UpdateRefundStatusRequest.php
│       ├── UpdateRefundSettingRequest.php        ✅ Created
│       ├── RejectRefundRequest.php               ✅ Created
│       ├── ChangeRefundStatusRequest.php         ✅ Created
│       └── UpdateRefundNotesRequest.php          ✅ Created
└── Models/
    └── RefundRequest.php                         ✅ Enhanced scopeFilters
```

## 🎯 Benefits

### 1. Separation of Concerns
- **Controllers**: Thin, focused on HTTP handling
- **Form Requests**: Handle validation logic
- **Model Scopes**: Handle query filtering logic

### 2. Reusability
- scopeFilters can be used in:
  - Dashboard datatable
  - API endpoints
  - Reports
  - Exports
  - Any query that needs filtering

### 3. Maintainability
- Validation rules in one place
- Filter logic in one place
- Easy to update and test
- Clear, readable code

### 4. Consistency
- Same validation messages across the app
- Same filtering behavior everywhere
- Predictable code structure

## 📝 Form Request Features

### Custom Attribute Names
```php
public function attributes(): array
{
    return [
        'customer_pays_return_shipping' => trans('refund::refund.fields.customer_pays_return_shipping'),
        'refund_processing_days' => trans('refund::refund.fields.refund_processing_days'),
    ];
}
```

### Custom Messages
```php
public function messages(): array
{
    return [
        'refund_processing_days.min' => trans('validation.min.numeric', [
            'attribute' => trans('refund::refund.fields.refund_processing_days'),
            'min' => 1
        ]),
    ];
}
```

### Authorization
```php
public function authorize(): bool
{
    return true; // Can add custom authorization logic here
}
```

## 🔍 scopeFilters Usage Examples

### Example 1: Dashboard Datatable
```php
$filters = [
    'status' => 'pending',
    'search' => 'REF-2026',
    'date_from' => '2026-01-01',
    'current_vendor_id' => 10,
];

$refunds = RefundRequest::scopeFilters($filters)->paginate(10);
```

### Example 2: API Endpoint
```php
$filters = [
    'status' => $request->status,
    'customer_id' => $request->customer_id,
    'vendor_id' => $request->vendor_id,
    'search' => $request->search,
];

$refunds = RefundRequest::scopeFilters($filters)->get();
```

### Example 3: Report Generation
```php
$filters = [
    'status' => 'refunded',
    'date_from' => '2026-01-01',
    'date_to' => '2026-01-31',
    'vendor_id' => 10,
];

$refunds = RefundRequest::scopeFilters($filters)->get();
```

## ✅ Validation Request vs API Request

| Feature | Dashboard Requests | API Requests |
|---------|-------------------|--------------|
| **Location** | `Requests/` | `Requests/Api/` |
| **Error Format** | Redirect with errors | JSON response |
| **Usage** | Web forms | API endpoints |
| **Authorization** | Can use `authorize()` | Can use `authorize()` |
| **Messages** | Translated | English |
| **Attributes** | Translated | English |

## 🎨 Code Quality Improvements

### Before:
- ❌ Validation logic in controller
- ❌ Filter logic scattered in controller
- ❌ Repetitive code
- ❌ Hard to test
- ❌ Hard to maintain

### After:
- ✅ Validation in Form Requests
- ✅ Filters in Model Scope
- ✅ DRY (Don't Repeat Yourself)
- ✅ Easy to test
- ✅ Easy to maintain
- ✅ Clean, readable controllers

## 📚 Summary

**Created:**
- 4 Dashboard-specific Form Requests
- Enhanced scopeFilters in RefundRequest model

**Updated:**
- RefundSettingController - Uses UpdateRefundSettingRequest
- RefundRequestController - Uses 3 Form Requests + scopeFilters

**Benefits:**
- Clean separation of concerns
- Reusable filter logic
- Maintainable validation
- Consistent code structure
- Easy to test and extend

The dashboard controllers are now clean, maintainable, and follow best practices! 🎉

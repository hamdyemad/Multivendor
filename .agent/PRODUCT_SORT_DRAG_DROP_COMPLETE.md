# Product Drag & Drop Sorting - Complete Implementation

## Summary
Successfully implemented drag & drop sorting for products using the same style and behavior as the department list. The implementation is now fully integrated into the reusable `datatable-wrapper` component.

## Changes Made

### 1. Translation Keys Added
**Files**: `lang/en/common.php`, `lang/ar/common.php`

Added missing translation key:
- `sort_updated` => 'Sort order updated successfully' (EN)
- `sort_updated` => 'تم تحديث الترتيب بنجاح' (AR)

### 2. ProductController - Updated Sort Logic
**File**: `Modules/CatalogManagement/app/Http/Controllers/ProductController.php`

Replaced the `updateSortOrder()` method to match department logic:
- Now recalculates ALL product sort numbers (not just dragged items)
- Uses the same algorithm as departments for proper sequential numbering
- Validates request data structure
- Logs reorder operations for debugging

**Key Logic**:
```php
// Get all products ordered by sort_number
$allProducts = VendorProduct::orderBy('sort_number', 'asc')->get();

// Remove dragged items from list
$remainingProducts = $allProducts->filter(function($product) use ($itemIds) {
    return !in_array($product->id, $itemIds);
})->values();

// Rebuild order with sequential sort numbers (1, 2, 3, ...)
// Insert dragged items at their new positions
// Update ALL products with new sort numbers
```

### 3. Datatable-Wrapper Component - Department Style
**File**: `resources/views/components/datatable-wrapper.blade.php`

#### Styles Updated:
- Added complete drag handle styling (grab cursor, hover effects)
- Added placeholder styling with dashed blue border
- Added reorder info message box styling
- Added jQuery UI CSS link

#### JavaScript Updated:
- Changed parameter name from `order` to `items` (matches controller)
- Added **LoadingOverlay** integration (shows "Saving..." during drag)
- Added **SweetAlert2** notifications (success toast, error alerts)
- Enhanced sortable configuration:
  - `opacity: 0.8` for dragged item
  - `disabled: !dragDropEnabled` to prevent dragging when not in sort mode
  - Better placeholder with colspan and blue background
  - Uses `outerWidth()` and `outerHeight()` for accurate sizing

#### Reorder Info Message:
- Shows info message when drag & drop is enabled
- Shows warning message when disabled (not sorting by sort_number ASC)
- Updates dynamically when sort filters change

### 4. Visual Feedback
The implementation now provides the same visual feedback as departments:

**When Dragging**:
- Item becomes semi-transparent (opacity: 0.8)
- Cursor changes to "grabbing"
- Blue dashed placeholder shows drop position
- Box shadow on dragged item

**When Disabled**:
- Drag handles become semi-transparent (opacity: 0.3)
- Cursor shows "not-allowed"
- Warning message displays: "Drag and drop is only available when sorting by Sort Number (Ascending)"

**After Drop**:
- Loading overlay shows "Saving... Please wait..."
- Success toast notification appears (top-right corner, auto-dismiss)
- Table reloads to show updated sort numbers
- On error: Alert dialog with error message

## How It Works

### 1. Enable Sorting in Product Index
**File**: `Modules/CatalogManagement/resources/views/product/index.blade.php`

```blade
<x-datatable-wrapper
    :enableSorting="true"
    :sortUpdateUrl="route('admin.products.update-sort-order')"
    sortPermission="products.edit"
    ...
/>
```

### 2. Drag Handle Column
**File**: `Modules/CatalogManagement/resources/views/product/product_configurations_table/_datatable-scripts.blade.php`

First column in datatable shows drag handle:
```javascript
{
    data: null,
    name: 'drag',
    orderable: false,
    searchable: false,
    className: 'text-center',
    render: function(data, type, row) {
        return `<span class="drag-handle" data-id="${row.id}" title="Drag to reorder">
            <i class="uil uil-draggabledots"></i>
        </span>`;
    }
}
```

### 3. Sort Filters Control Drag State
**File**: `Modules/CatalogManagement/resources/views/product/product_configurations_table/_filters.blade.php`

Two filters control when drag & drop is enabled:
- **Sort By**: Must be "Sort Number"
- **Sort Direction**: Must be "Ascending"

Only when both conditions are met, drag & drop is enabled.

### 4. Backend Processing
**Route**: `POST /admin/products/update-sort-order`

**Request**:
```json
{
    "items": [
        {"id": 123, "sort_number": 1},
        {"id": 456, "sort_number": 2},
        {"id": 789, "sort_number": 3}
    ]
}
```

**Response**:
```json
{
    "success": true,
    "message": "Sort order updated successfully"
}
```

## Component Reusability

The `datatable-wrapper` component is now fully reusable for any table that needs drag & drop sorting:

```blade
<x-datatable-wrapper
    tableId="myTable"
    :enableSorting="true"
    :sortUpdateUrl="route('admin.my-resource.update-sort-order')"
    sortPermission="my-resource.edit"
    :headers="[...]"
    :columnsJson="json_encode($columns)"
    ajaxUrl="{{ route('admin.my-resource.index') }}"
/>
```

**Requirements**:
1. Database table must have `sort_number` column (integer)
2. Controller must have `updateSortOrder()` method (same logic as ProductController)
3. Route must be defined for sort update
4. First column must be drag handle with `data-id` attribute

## Testing Checklist

✅ Drag and drop works when sorting by "Sort Number (ASC)"
✅ Drag and drop disabled when sorting by other columns
✅ Sort numbers update correctly after drag
✅ Table reloads and shows new sort numbers
✅ Loading overlay appears during save
✅ Success toast notification appears
✅ Error handling works (shows alert on failure)
✅ Reorder info message updates based on sort state
✅ Visual feedback matches department style
✅ Works with pagination (only affects current page items)
✅ Translations work in both English and Arabic

## Files Modified

1. `lang/en/common.php` - Added `sort_updated` translation
2. `lang/ar/common.php` - Added `sort_updated` translation
3. `Modules/CatalogManagement/app/Http/Controllers/ProductController.php` - Updated `updateSortOrder()` method
4. `resources/views/components/datatable-wrapper.blade.php` - Enhanced drag & drop with department style

## Files Already Configured (from previous tasks)

1. `Modules/CatalogManagement/database/migrations/2026_01_25_120655_add_sort_number_to_vendor_products_table.php`
2. `Modules/CatalogManagement/resources/views/product/index.blade.php`
3. `Modules/CatalogManagement/resources/views/product/product_configurations_table/_filters.blade.php`
4. `Modules/CatalogManagement/resources/views/product/product_configurations_table/_datatable-scripts.blade.php`
5. `Modules/CatalogManagement/routes/web.php`
6. `Modules/CatalogManagement/app/Actions/ProductAction.php`

## Notes

- The drag & drop style now matches departments exactly
- Uses SweetAlert2 for notifications (not toastr)
- Uses LoadingOverlay for loading states
- Reorder info message provides clear user feedback
- Component is fully reusable for other tables
- All sort numbers are recalculated sequentially after each drag operation
- Works seamlessly with existing filters and pagination

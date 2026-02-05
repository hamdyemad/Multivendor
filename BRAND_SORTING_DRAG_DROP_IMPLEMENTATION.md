# Brand Sorting and Drag-and-Drop Implementation

## Overview
Implemented sorting functionality and drag-and-drop reordering for brands, similar to the category management system.

## Changes Made

### 1. Database Migration
**File**: `database/migrations/2026_02_05_101255_add_sort_number_to_brands_table.php`
- Added `sort_number` column to `brands` table (integer, default 0)
- Added index on `sort_number` for better query performance
- Migration executed successfully

### 2. Brand Form
**File**: `Modules/CatalogManagement/resources/views/brand/form.blade.php`
- Added `sort_number` input field before the activation switcher
- Field type: number, min: 0
- Default value: 0 for new brands, existing value for edits

### 3. Brand Controller
**File**: `Modules/CatalogManagement/app/Http/Controllers/BrandController.php`
- Added `reorder()` method to handle drag-and-drop reordering
- Validates incoming data (items array with id and sort_number)
- Implements swap logic: when moving a brand to a position, swaps sort numbers with conflicting brand
- Returns JSON response for AJAX calls

### 4. Routes
**File**: `Modules/CatalogManagement/routes/web.php`
- Added POST route: `brands/reorder` → `BrandController@reorder`
- Route name: `brands.reorder`

### 5. Brand Action
**File**: `Modules/CatalogManagement/app/Actions/BrandAction.php`
- Added sort parameters support: `sort_column` and `sort_direction`
- Added `sort_number` to datatable response
- Added `index` column for row numbering
- Passes sort parameters to repository

### 6. Brand Repository
**File**: `Modules/CatalogManagement/app/Repositories/BrandRepository.php`
- Updated `getBrandsQuery()` to handle sorting
- Supports sorting by:
  - `sort_number` (ascending/descending)
  - `created_at` (ascending/descending)
- Default sort: `sort_number ASC`
- Updated `createBrand()` to save `sort_number`
- Updated `updateBrand()` to update `sort_number`

## Next Steps (Brand Index View)

The brand index view needs to be updated with:

1. **Sort Filter Controls**:
   - Sort By dropdown (Sort Number, Created At)
   - Sort Direction dropdown (Ascending, Descending)

2. **Drag Handle Column**:
   - First column with drag icon
   - Only enabled when sorting by Sort Number (Ascending)

3. **Sort Number Display**:
   - Show sort_number in brand information column

4. **jQuery UI Sortable**:
   - Initialize sortable on table tbody
   - Handle drag-and-drop events
   - Send AJAX request to reorder endpoint
   - Reload table after successful reorder

5. **Reorder Info Alert**:
   - Show info message when drag-and-drop is enabled
   - Show warning when drag-and-drop is disabled (not sorting by sort_number asc)

## Features

### Drag-and-Drop Reordering
- **Enabled**: Only when sorting by "Sort Number" in "Ascending" order
- **Visual Feedback**: 
  - Drag handle cursor changes to "grab"
  - Placeholder shows where item will be dropped
  - Opacity changes during drag
- **Auto-Save**: Changes saved automatically via AJAX
- **Swap Logic**: When dropping on a position, swaps sort numbers with existing brand

### Manual Sort Number
- Can be set manually in the brand form
- Useful for precise positioning
- Works in conjunction with drag-and-drop

### Sort Options
- **Sort Number (Ascending)**: Enables drag-and-drop, shows brands in custom order
- **Sort Number (Descending)**: Disables drag-and-drop, shows reverse order
- **Created At (Ascending/Descending)**: Disables drag-and-drop, shows chronological order

## Testing Checklist

### Form Testing
- [ ] Create new brand - verify sort_number defaults to 0
- [ ] Edit existing brand - verify sort_number shows current value
- [ ] Update sort_number manually - verify it saves correctly

### Reorder API Testing
- [ ] Test reorder endpoint directly with Postman/curl
- [ ] Verify swap logic works correctly
- [ ] Check error handling for invalid data

### Index View (After Implementation)
- [ ] Verify sort filters appear and work
- [ ] Test drag-and-drop when sorting by sort_number asc
- [ ] Verify drag-and-drop disabled for other sort options
- [ ] Test reorder saves correctly
- [ ] Verify table reloads after reorder
- [ ] Check visual feedback during drag

## Files Modified

1. `database/migrations/2026_02_05_101255_add_sort_number_to_brands_table.php` - NEW
2. `Modules/CatalogManagement/resources/views/brand/form.blade.php` - MODIFIED
3. `Modules/CatalogManagement/app/Http/Controllers/BrandController.php` - MODIFIED
4. `Modules/CatalogManagement/routes/web.php` - MODIFIED
5. `Modules/CatalogManagement/app/Actions/BrandAction.php` - MODIFIED
6. `Modules/CatalogManagement/app/Repositories/BrandRepository.php` - MODIFIED
7. `Modules/CatalogManagement/resources/views/brand/index.blade.php` - NEEDS UPDATE

## Notes

- The implementation follows the same pattern as categories for consistency
- Sort numbers can have gaps (0, 5, 10, etc.) - the system handles this
- Drag-and-drop only works within the current page (pagination aware)
- Permission check: `brands.edit` required for reordering

## API Endpoint

**POST** `/admin/brands/reorder`

**Request Body**:
```json
{
  "_token": "csrf_token",
  "items": [
    {
      "id": 1,
      "sort_number": 5
    }
  ]
}
```

**Success Response**:
```json
{
  "success": true,
  "message": "Order updated successfully"
}
```

**Error Response**:
```json
{
  "success": false,
  "message": "Failed to update order"
}
```

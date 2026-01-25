# Product Per Page Custom Select - Implementation Complete

## Overview
Added a custom select dropdown next to the search button in the filters section to control the number of products displayed per page (10, 25, 50, 100). This replaces the old entriesSelect implementation with the custom select component already built in the project.

## Changes Made

### 1. Added Per Page Selector UI
**File**: `Modules/CatalogManagement/resources/views/product/index.blade.php`

**Location**: In the filters section, next to the Search and Reset buttons

```blade
<div class="col-md-12 d-flex align-items-center gap-3">
    <div class="d-flex gap-1">
        <button type="button" id="searchBtn"
            class="btn btn-success btn-default btn-squared"
            title="{{ __('common.search') }}">
            <i class="uil uil-search me-1"></i>
            {{ __('common.search') }}
        </button>
        <button type="button" id="resetFilters"
            class="btn btn-warning btn-default btn-squared"
            title="{{ __('common.reset') }}">
            <i class="uil uil-redo me-1"></i>
            {{ __('common.reset_filters') }}
        </button>
    </div>
    
    {{-- Per Page Selector --}}
    <div class="d-flex align-items-center">
        <label class="me-2 mb-0 text-muted">{{ __('common.show') }}:</label>
        <div style="width: 100px;">
            <x-custom-select
                name="per_page_filter"
                id="per_page_filter"
                :options="[
                    ['id' => '10', 'name' => '10'],
                    ['id' => '25', 'name' => '25'],
                    ['id' => '50', 'name' => '50'],
                    ['id' => '100', 'name' => '100']
                ]"
                :selected="'10'"
                :placeholder="''"
            />
        </div>
        <span class="ms-2 mb-0 text-muted">{{ __('common.entries') }}</span>
    </div>
</div>
```

**Styling**:
- Parent div uses `gap-3` for spacing between button group and per page selector
- Buttons grouped together with `gap-1`
- Per page selector has flexbox layout with label, dropdown, and suffix text
- Custom select width: 100px
- Label and suffix in muted text color

### 2. Added Custom Select Initialization
Added `per_page_filter` to the customSelectIds array:

```javascript
const customSelectIds = [
    'vendor_filter', 'brand_filter', 'department_filter', 'category_filter',
    'product_type', 'configuration_filter', 'active', 'stock_filter', 'status', 'per_page_filter'
];
```

### 3. Added Change Event Handler
Created a dedicated change handler for the per page filter:

```javascript
// Per page filter change handler
const perPageEl = document.getElementById('per_page_filter');
if (perPageEl) {
    perPageEl.addEventListener('change', function(e) {
        const perPage = e.detail ? e.detail.value : (typeof CustomSelect !== 'undefined' ? CustomSelect.getValue('per_page_filter') : 10);
        table.page.len(parseInt(perPage)).draw();
    });
}
```

**Functionality**:
- Listens to change events on the custom select
- Gets the selected value (10, 25, 50, or 100)
- Updates DataTable page length using `table.page.len()`
- Redraws the table with new page size

### 4. Updated DataTable Ajax Data Function
Changed from using `$('#entriesSelect').val()` to custom select:

**Before**:
```javascript
d.per_page = $('#entriesSelect').val() || 10;
```

**After**:
```javascript
d.per_page = typeof CustomSelect !== 'undefined' && document.getElementById('per_page_filter') ? CustomSelect.getValue('per_page_filter') : 10;
```

### 5. Removed Old Entries Selector Code
Removed the jQuery-based entriesSelect implementation:

**Removed**:
```javascript
// Entries Selector
$('#entriesSelect').html([10, 25, 50, 100].map(n => `<option value="${n}">${n}</option>`).join(''));
$('#entriesSelect').val(10).on('change', function() {
    table.page.len($(this).val()).draw();
});
```

### 6. Updated Reset Filters Function
Added per_page_filter reset to default value (10):

```javascript
// Reset per page to default (10)
if (document.getElementById('per_page_filter') && typeof CustomSelect !== 'undefined') {
    CustomSelect.setValue('per_page_filter', '10');
}
```

## User Experience Flow

1. **Initial State**: 
   - Per page selector shows "10" by default
   - Table displays 10 products per page

2. **Changing Per Page Value**:
   - User clicks the per page dropdown
   - Selects 25, 50, or 100
   - Table immediately updates to show selected number of products
   - Pagination updates accordingly

3. **Reset Filters**:
   - User clicks "Reset Filters" button
   - Per page selector resets to "10"
   - All other filters are cleared
   - Table reloads with default settings

## Visual Layout

```
┌─────────────────────────────────────────────────────────────────────┐
│ Filters Section                                                     │
│ [Search] [Reset]    Show: [10 ▼] entries                          │
├─────────────────────────────────────────────────────────────────────┤
│ ☐ | # | Product Information | Vendor | Status | Actions            │
├─────────────────────────────────────────────────────────────────────┤
│ ☐ | 1 | Product 1 ...                                              │
│ ☐ | 2 | Product 2 ...                                              │
│ ...                                                                 │
└─────────────────────────────────────────────────────────────────────┘
```

## Benefits

1. **Convenient Location**: Per page selector is right next to search/reset buttons
2. **Consistent UI**: Uses the same custom select component as other filters
3. **Better UX**: All action buttons and controls are grouped together
4. **Space Efficient**: Doesn't take up extra vertical space
5. **Integrated**: Properly resets with other filters

## Technical Details

### Custom Select Component
- Uses the existing `x-custom-select` Blade component
- Supports keyboard navigation
- Has consistent styling with other filters
- Properly handles change events

### DataTable Integration
- Updates page length using `table.page.len()`
- Triggers table redraw with `.draw()`
- Maintains current page position when possible
- Updates pagination controls automatically

### Default Value
- Default: 10 entries per page
- Matches DataTable's default `pageLength: 10`
- Resets to 10 when filters are cleared

## Files Modified

1. `Modules/CatalogManagement/resources/views/product/index.blade.php`

## Testing Recommendations

1. Test selecting each option (10, 25, 50, 100)
2. Verify table updates immediately on selection
3. Test pagination updates correctly
4. Test reset filters resets per page to 10
5. Test with different filter combinations
6. Verify it works for both admin and vendor users
7. Test keyboard navigation in the dropdown
8. Test on mobile/tablet devices

## Notes

- The per page selector is positioned next to the Search and Reset buttons in the filters section
- It's grouped with action buttons for better organization and accessibility
- The width is fixed at 100px to prevent layout shifts
- Default value is 10, matching the DataTable configuration
- Uses `gap-3` for spacing between button group and per page selector

# Global DataTable Components

## Overview

Created reusable DataTable components in the main project's `resources/views/components` folder that can be used across all modules.

## Components Created

### 1. datatable-wrapper.blade.php
**Location:** `resources/views/components/datatable-wrapper.blade.php`

Wraps the entire DataTable with header, title, and action buttons.

**Props:**
- `title` - Page title (string)
- `icon` - Icon class (string, default: 'uil uil-list-ul')
- `createRoute` - Route for create button (string|null)
- `createText` - Create button text (string|null)
- `showExport` - Show export button (boolean, default: false)
- `exportText` - Export button text (string|null)
- `tableId` - Table ID (string, default: 'dataTable')
- `additionalButtons` - Slot for additional buttons

**Slots:**
- `filters` - Filter section
- Default slot - Table content (thead, tbody)

**Usage:**
```blade
<x-datatable-wrapper
    :title="trans('menu.refunds.all')"
    icon="uil uil-redo"
    :showExport="false"
    tableId="refundsDataTable">
    
    <x-slot name="filters">
        {{-- Filters here --}}
    </x-slot>

    <thead>
        {{-- Table headers --}}
    </thead>
    <tbody></tbody>
</x-datatable-wrapper>
```

---

### 2. datatable-filters-advanced.blade.php
**Location:** `resources/views/components/datatable-filters-advanced.blade.php`

Advanced filter component with search, custom filters, and date range.

**Props:**
- `filters` - Array of filter configurations
- `searchPlaceholder` - Search input placeholder (string|null)
- `showDateFilters` - Show date range filters (boolean, default: true)
- `showSearchButton` - Show search button (boolean, default: true)
- `showResetButton` - Show reset button (boolean, default: true)
- `showExportButton` - Show export button (boolean, default: false)
- `additionalContent` - Slot for additional content

**Filter Configuration:**
Each filter in the `filters` array should have:
```php
[
    'name' => 'filter_name',
    'id' => 'filter_id',
    'label' => 'Filter Label',
    'icon' => 'uil uil-icon',
    'options' => [
        ['id' => 'value1', 'name' => 'Label 1'],
        ['id' => 'value2', 'name' => 'Label 2'],
    ],
    'selected' => 'current_value',
    'placeholder' => 'All',
]
```

**Usage:**
```blade
<x-datatable-filters-advanced
    :searchPlaceholder="trans('refund::refund.fields.refund_number')"
    :filters="[
        [
            'name' => 'status_filter',
            'id' => 'status_filter',
            'label' => trans('refund::refund.fields.status'),
            'icon' => 'uil uil-check-circle',
            'options' => $statusOptions,
            'selected' => request('status'),
            'placeholder' => __('common.all'),
        ],
    ]"
    :showDateFilters="true"
/>
```

---

### 3. datatable-actions.blade.php
**Location:** `resources/views/components/datatable-actions.blade.php`

Reusable actions component for table rows.

**Props:**
- `actions` - Array of action configurations

**Action Types:**

**Link:**
```php
[
    'type' => 'link',
    'url' => route('admin.refunds.show', $id),
    'class' => 'btn btn-primary table_action_father',
    'title' => trans('common.view'),
    'icon' => 'uil uil-eye',
    'target' => '_blank', // optional
]
```

**Button (with modal):**
```php
[
    'type' => 'button',
    'class' => 'btn btn-warning table_action_father',
    'title' => trans('common.edit'),
    'icon' => 'uil uil-edit',
    'modal' => '#editModal',
    'data' => [
        'item-id' => $id,
        'item-name' => $name,
    ],
]
```

**Form (with confirmation):**
```php
[
    'type' => 'form',
    'url' => route('admin.refunds.destroy', $id),
    'method' => 'DELETE',
    'class' => 'btn btn-danger table_action_father',
    'title' => trans('common.delete'),
    'icon' => 'uil uil-trash-alt',
    'confirm' => trans('common.confirm_delete'),
]
```

**Usage in DataTable render:**
```javascript
render: function(data) {
    return `<x-datatable-actions :actions="[
        ['type' => 'link', 'url' => '${showUrl}', 'icon' => 'uil uil-eye', 'title' => 'View'],
        ['type' => 'button', 'icon' => 'uil uil-edit', 'modal' => '#editModal', 'data' => ['item-id' => '${data.id}']],
    ]" />`;
}
```

---

### 4. datatable-script.blade.php
**Location:** `resources/views/components/datatable-script.blade.php`

Complete DataTable JavaScript initialization component.

**Props:**
- `tableId` - Table ID (string, default: 'dataTable')
- `ajaxUrl` - AJAX endpoint URL (string)
- `columns` - DataTable columns configuration (array)
- `order` - Default sorting (array, default: [[0, 'desc']])
- `customSelectIds` - Array of custom select IDs (array)
- `additionalData` - Additional data to send with AJAX (array)
- `pageLength` - Default page length (int, default: 10)

**Usage:**
```blade
@push('scripts')
<x-datatable-script
    tableId="refundsDataTable"
    ajaxUrl="{{ route('admin.refunds.datatable') }}"
    :columns="[
        ['data' => 'index', 'name' => 'index', 'orderable' => false],
        ['data' => 'refund_number', 'name' => 'refund_number'],
        // ... more columns
    ]"
    :order="[[7, 'desc']]"
    :customSelectIds="['status_filter']"
    :pageLength="10"
/>
@endpush
```

---

## Migration from Module Components

### Before (Module-specific):
```blade
<x-refund::datatable-wrapper>
<x-refund::search-filters>
```

### After (Global):
```blade
<x-datatable-wrapper>
<x-datatable-filters-advanced>
```

## Benefits

1. **Reusability** - Use across all modules
2. **Consistency** - Same look and feel everywhere
3. **Maintainability** - Update once, applies everywhere
4. **Flexibility** - Highly configurable with props
5. **DRY Principle** - Don't Repeat Yourself

## File Structure

```
resources/views/components/
├── datatable-wrapper.blade.php           ✅ NEW - Wrapper with header
├── datatable-filters-advanced.blade.php  ✅ NEW - Advanced filters
├── datatable-actions.blade.php           ✅ NEW - Table row actions
├── datatable-script.blade.php            ✅ NEW - DataTable JS init
├── datatable-filters.blade.php           📦 Existing - Simple filters
├── datatable-table.blade.php             📦 Existing - Simple table
└── ... (other components)
```

## Usage in Refund Module

The refund module now uses these global components:

**File:** `Modules/Refund/resources/views/refund-requests/index.blade.php`

```blade
<x-datatable-wrapper
    :title="trans('menu.refunds.all')"
    icon="uil uil-redo"
    tableId="refundsDataTable">
    
    <x-slot name="filters">
        <x-datatable-filters-advanced
            :searchPlaceholder="trans('refund::refund.fields.refund_number')"
            :filters="[...]"
            :showDateFilters="true"
        />
    </x-slot>

    <thead>...</thead>
    <tbody></tbody>
</x-datatable-wrapper>
```

## Next Steps

These components can now be used in:
- Order management
- Product management
- Customer management
- Vendor management
- Any other module with DataTables

## Example: Using in Another Module

```blade
{{-- In any module's index view --}}
<x-datatable-wrapper
    :title="trans('menu.orders.all')"
    icon="uil uil-shopping-cart"
    :createRoute="route('admin.orders.create')"
    :showExport="true"
    tableId="ordersDataTable">
    
    <x-slot name="filters">
        <x-datatable-filters-advanced
            :filters="[
                [
                    'name' => 'status',
                    'id' => 'status',
                    'label' => 'Status',
                    'icon' => 'uil uil-check',
                    'options' => $statuses,
                ],
            ]"
        />
    </x-slot>

    <thead>
        <tr class="userDatatable-header">
            <th>#</th>
            <th>Order Number</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</x-datatable-wrapper>
```

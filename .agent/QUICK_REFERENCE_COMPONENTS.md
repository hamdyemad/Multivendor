# Quick Reference: Global DataTable Components

## 🎯 Quick Start

### Basic DataTable Page

```blade
@extends('layout.app')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb --}}
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => 'My Page'],
            ]" />
        </div>
    </div>

    {{-- DataTable --}}
    <x-datatable-wrapper
        title="My Items"
        icon="uil uil-list"
        tableId="myTable">
        
        <x-slot name="filters">
            <x-datatable-filters-advanced
                searchPlaceholder="Search items..."
                :showDateFilters="true"
            />
        </x-slot>

        <thead>
            <tr class="userDatatable-header">
                <th>#</th>
                <th>Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </x-datatable-wrapper>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let table = $('#myTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.items.datatable') }}',
                data: function(d) {
                    d.search = $('#search').val();
                    d.created_date_from = $('#created_date_from').val();
                    d.created_date_to = $('#created_date_to').val();
                }
            },
            columns: [
                {data: 'index', orderable: false},
                {data: 'name'},
                {data: 'status'},
                {data: null, render: function(data) {
                    return `<a href="/items/${data.id}" class="btn btn-primary">
                        <i class="uil uil-eye"></i>
                    </a>`;
                }}
            ]
        });
    });
</script>
@endpush
```

## 📦 Component Reference

### x-datatable-wrapper

```blade
<x-datatable-wrapper
    title="Page Title"
    icon="uil uil-icon"
    :createRoute="route('admin.items.create')"
    createText="Add New"
    :showExport="true"
    exportText="Export"
    tableId="myTable">
    
    <x-slot name="filters">...</x-slot>
    <x-slot name="additionalButtons">...</x-slot>
    
    <thead>...</thead>
    <tbody></tbody>
</x-datatable-wrapper>
```

### x-datatable-filters-advanced

```blade
<x-datatable-filters-advanced
    searchPlaceholder="Search..."
    :filters="[
        [
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'icon' => 'uil uil-check',
            'options' => [
                ['id' => 'active', 'name' => 'Active'],
                ['id' => 'inactive', 'name' => 'Inactive'],
            ],
            'selected' => request('status'),
            'placeholder' => 'All',
        ],
    ]"
    :showDateFilters="true"
    :showSearchButton="true"
    :showResetButton="true"
    :showExportButton="false"
/>
```

### x-datatable-actions

```blade
{{-- In DataTable render function --}}
render: function(data) {
    const actions = [
        {
            type: 'link',
            url: '/items/' + data.id,
            icon: 'uil uil-eye',
            title: 'View',
            class: 'btn btn-primary table_action_father'
        },
        {
            type: 'button',
            icon: 'uil uil-edit',
            title: 'Edit',
            modal: '#editModal',
            class: 'btn btn-warning table_action_father',
            data: {
                'item-id': data.id,
                'item-name': data.name
            }
        },
        {
            type: 'form',
            url: '/items/' + data.id,
            method: 'DELETE',
            icon: 'uil uil-trash',
            title: 'Delete',
            class: 'btn btn-danger table_action_father',
            confirm: 'Are you sure?'
        }
    ];
    
    // Return HTML string with actions
    let html = '<div class="orderDatatable_actions d-inline-flex gap-1">';
    actions.forEach(action => {
        if (action.type === 'link') {
            html += `<a href="${action.url}" class="${action.class}" title="${action.title}">
                <i class="${action.icon} table_action_icon"></i>
            </a>`;
        }
    });
    html += '</div>';
    return html;
}
```

## 🔧 Common Patterns

### With Status Filter

```blade
<x-datatable-filters-advanced
    :filters="[
        [
            'name' => 'status',
            'id' => 'status',
            'label' => trans('common.status'),
            'icon' => 'uil uil-check-circle',
            'options' => [
                ['id' => 'pending', 'name' => trans('status.pending')],
                ['id' => 'approved', 'name' => trans('status.approved')],
                ['id' => 'rejected', 'name' => trans('status.rejected')],
            ],
        ],
    ]"
/>
```

### With Multiple Filters

```blade
<x-datatable-filters-advanced
    :filters="[
        [
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'icon' => 'uil uil-check-circle',
            'options' => $statuses,
        ],
        [
            'name' => 'category',
            'id' => 'category',
            'label' => 'Category',
            'icon' => 'uil uil-folder',
            'options' => $categories,
        ],
        [
            'name' => 'vendor',
            'id' => 'vendor',
            'label' => 'Vendor',
            'icon' => 'uil uil-store',
            'options' => $vendors,
        ],
    ]"
/>
```

### With Create Button

```blade
<x-datatable-wrapper
    title="Products"
    icon="uil uil-box"
    :createRoute="route('admin.products.create')"
    createText="Add Product"
    tableId="productsTable">
    ...
</x-datatable-wrapper>
```

### With Export Button

```blade
<x-datatable-wrapper
    title="Orders"
    icon="uil uil-shopping-cart"
    :showExport="true"
    exportText="Export Orders"
    tableId="ordersTable">
    ...
</x-datatable-wrapper>
```

## 📝 DataTable JavaScript Template

```javascript
$(document).ready(function() {
    // Initialize Custom Selects
    if (document.getElementById('status') && typeof CustomSelect !== 'undefined') {
        CustomSelect.init('status');
    }

    // DataTable
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: '{{ route('admin.items.datatable') }}',
            data: function(d) {
                d.search = $('#search').val();
                d.status = CustomSelect.getValue('status');
                d.created_date_from = $('#created_date_from').val();
                d.created_date_to = $('#created_date_to').val();
            }
        },
        columns: [
            {data: 'index', orderable: false, className: 'text-center'},
            {data: 'name'},
            {data: 'status', className: 'text-center'},
            {data: null, orderable: false, className: 'text-center', render: function(data) {
                return `<a href="/items/${data.id}" class="btn btn-primary">View</a>`;
            }}
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        dom: '<"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    });

    // Search
    $('#searchBtn').on('click', () => table.ajax.reload());
    $('#search').on('keyup', function() {
        clearTimeout(window.searchTimer);
        window.searchTimer = setTimeout(() => table.ajax.reload(), 600);
    });

    // Filters
    document.getElementById('status').addEventListener('change', () => table.ajax.reload());
    $('#created_date_from, #created_date_to').on('change', () => table.ajax.reload());

    // Reset
    $('#resetFilters').on('click', function() {
        $('#search, #created_date_from, #created_date_to').val('');
        CustomSelect.clear('status');
        table.ajax.reload();
    });
});
```

## 🎨 Styling Classes

### Button Classes
- `btn btn-primary` - Blue (View)
- `btn btn-warning` - Yellow (Edit)
- `btn btn-danger` - Red (Delete)
- `btn btn-success` - Green (Approve)
- `btn btn-info` - Cyan (Info)
- `btn btn-secondary` - Gray (Other)

### Icon Classes
- `uil uil-eye` - View
- `uil uil-edit` - Edit
- `uil uil-trash` - Delete
- `uil uil-check` - Approve
- `uil uil-times` - Reject
- `uil uil-download` - Download

### Badge Classes
- `badge badge-success` - Green
- `badge badge-warning` - Yellow
- `badge badge-danger` - Red
- `badge badge-info` - Blue
- `badge badge-secondary` - Gray

## 📍 File Locations

```
resources/views/components/
├── datatable-wrapper.blade.php
├── datatable-filters-advanced.blade.php
├── datatable-actions.blade.php
└── datatable-script.blade.php
```

## 🔗 Related Components

- `x-breadcrumb` - Page breadcrumbs
- `x-custom-select` - Custom select dropdown
- `x-loading-overlay` - Loading overlay
- `x-delete-with-loading` - Delete modal with loading

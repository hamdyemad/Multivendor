# Dynamic DataTable Components - Complete Guide

## 🎯 Overview

I've created **fully flexible components** that let you pass any filters and columns you need. You can now use these components for ANY index.blade.php file!

### Components Created:
1. **`datatable-filters-dynamic.blade.php`** - Accepts dynamic filter configuration
2. **`datatable-table-dynamic.blade.php`** - Accepts dynamic column configuration

---

## 📋 Filter Types Supported

The dynamic filters component supports these filter types:
- **`search`** - Text input for search
- **`select`** - Dropdown with options
- **`date`** - Date picker
- **`custom`** - Any custom HTML

---

## 🚀 Usage Examples

### Example 1: Activities (Basic)

```blade
{{-- In your activities/index.blade.php --}}

<x-datatable-filters-dynamic 
    :filters="[
        [
            'type' => 'search',
            'id' => 'search',
            'label' => __('common.search'),
            'placeholder' => __('activity.search_by_name'),
            'col' => 'col-md-3'
        ],
        [
            'type' => 'select',
            'id' => 'active',
            'label' => __('activity.activation'),
            'icon' => 'uil uil-check-circle',
            'allOption' => __('activity.all'),
            'options' => [
                '1' => __('activity.active'),
                '0' => __('activity.inactive')
            ],
            'col' => 'col-md-3'
        ]
    ]"
    :showDateFilters="true"
    :showButtons="true"
/>

<x-datatable-table-dynamic 
    tableId="activitiesDataTable"
    :columns="[
        ['label' => __('activity.name') . ' (English)'],
        ['label' => __('activity.name') . ' (Arabic)', 'rtl' => true],
        ['label' => __('activity.activation')],
        ['label' => __('activity.created_at')]
    ]"
/>
```

### Example 2: Categories (With Department Filter)

```blade
<x-datatable-filters-dynamic 
    :filters="[
        [
            'type' => 'search',
            'id' => 'search',
            'label' => __('common.search'),
            'icon' => 'uil uil-search',
            'placeholder' => __('category.search_by_name'),
            'col' => 'col-md-3'
        ],
        [
            'type' => 'select',
            'id' => 'department_id',
            'label' => __('category.department'),
            'icon' => 'uil uil-layers',
            'allOption' => __('category.all_departments'),
            'options' => $departments->pluck('name', 'id')->toArray(),
            'col' => 'col-md-3'
        ],
        [
            'type' => 'select',
            'id' => 'active',
            'label' => __('category.activation'),
            'icon' => 'uil uil-check-circle',
            'allOption' => __('category.all'),
            'options' => [
                '1' => __('category.active'),
                '0' => __('category.inactive')
            ],
            'col' => 'col-md-3'
        ]
    ]"
/>

<x-datatable-table-dynamic 
    tableId="categoriesDataTable"
    :columns="[
        ['label' => __('category.name') . ' (EN)'],
        ['label' => __('category.name') . ' (AR)', 'rtl' => true],
        ['label' => __('category.department')],
        ['label' => __('category.activation')],
        ['label' => __('category.created_at')]
    ]"
/>
```

### Example 3: Cities (With Country Filter)

```blade
@php
$filters = [
    [
        'type' => 'search',
        'id' => 'search',
        'label' => __('common.search'),
        'icon' => 'uil uil-search',
        'placeholder' => __('areasettings::city.search_placeholder'),
        'col' => 'col-md-4'
    ],
    [
        'type' => 'select',
        'id' => 'country_id',
        'label' => __('areasettings::city.country'),
        'icon' => 'uil uil-globe',
        'allOption' => __('areasettings::city.all_countries'),
        'options' => $countries->mapWithKeys(function($country) {
            return [$country->id => $country->getTranslation('name', app()->getLocale()) ?? $country->code];
        })->toArray(),
        'col' => 'col-md-4'
    ],
    [
        'type' => 'select',
        'id' => 'active',
        'label' => __('areasettings::city.status'),
        'icon' => 'uil uil-check-circle',
        'allOption' => __('areasettings::city.all_status'),
        'options' => [
            '1' => __('areasettings::city.active'),
            '0' => __('areasettings::city.inactive')
        ],
        'col' => 'col-md-4'
    ]
];

$columns = [];
foreach ($languages as $language) {
    $columns[] = [
        'label' => __('areasettings::city.name') . ' (' . $language->name . ')',
        'rtl' => $language->rtl
    ];
}
$columns[] = ['label' => __('areasettings::city.country')];
$columns[] = ['label' => __('areasettings::city.regions')];
$columns[] = ['label' => __('areasettings::city.status')];
$columns[] = ['label' => __('areasettings::city.created_at')];
@endphp

<x-datatable-filters-dynamic :filters="$filters" />
<x-datatable-table-dynamic tableId="citiesDataTable" :columns="$columns" />
```

### Example 4: Vendors (With Multiple Filters)

```blade
<x-datatable-filters-dynamic 
    :filters="[
        [
            'type' => 'search',
            'id' => 'search',
            'label' => __('common.search'),
            'placeholder' => __('vendor::vendor.search_placeholder'),
            'col' => 'col-md-3'
        ],
        [
            'type' => 'select',
            'id' => 'active',
            'label' => __('vendor::vendor.status'),
            'allOption' => __('vendor::vendor.all'),
            'options' => [
                '1' => __('vendor::vendor.active'),
                '0' => __('vendor::vendor.inactive')
            ],
            'col' => 'col-md-3'
        ],
        [
            'type' => 'select',
            'id' => 'subscription_status',
            'label' => __('vendor::vendor.subscription'),
            'allOption' => __('vendor::vendor.all_subscriptions'),
            'options' => [
                'active' => __('vendor::vendor.subscription_active'),
                'expired' => __('vendor::vendor.subscription_expired')
            ],
            'col' => 'col-md-3'
        ]
    ]"
/>

<x-datatable-table-dynamic 
    tableId="vendorsDataTable"
    :columns="[
        ['label' => __('vendor::vendor.vendor_information')],
        ['label' => __('vendor::vendor.commission')],
        ['label' => __('vendor::vendor.subscription')],
        ['label' => __('vendor::vendor.status')]
    ]"
/>
```

### Example 5: Custom HTML Filter

```blade
<x-datatable-filters-dynamic 
    :filters="[
        [
            'type' => 'search',
            'id' => 'search',
            'label' => __('common.search'),
            'placeholder' => __('product.search_by_name'),
            'col' => 'col-md-3'
        ],
        [
            'type' => 'custom',
            'col' => 'col-md-3',
            'html' => '
                <div class="form-group">
                    <label class="il-gray fs-14 fw-500 mb-10">
                        <i class="uil uil-tag-alt me-1"></i> ' . __('product.price_range') . '
                    </label>
                    <div class="d-flex gap-2">
                        <input type="number" id="price_min" class="form-control" placeholder="Min">
                        <input type="number" id="price_max" class="form-control" placeholder="Max">
                    </div>
                </div>
            '
        ]
    ]"
/>
```

---

## 📖 Props Reference

### datatable-filters-dynamic Component

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `filters` | Array | `[]` | Array of filter configurations |
| `showDateFilters` | Boolean | `true` | Show created_date_from and created_date_to |
| `showButtons` | Boolean | `true` | Show Export Excel and Reset buttons |
| `showEntriesSelector` | Boolean | `true` | Show entries per page selector |

### Filter Configuration Array

Each filter in the `filters` array can have:

| Key | Required | Type | Description |
|-----|----------|------|-------------|
| `type` | Yes | String | Filter type: 'search', 'select', 'date', 'custom' |
| `id` | Yes | String | HTML id attribute |
| `label` | Yes | String | Label text |
| `icon` | No | String | Icon class (e.g., 'uil uil-search') |
| `placeholder` | No | String | Placeholder text (for search/date) |
| `col` | No | String | Bootstrap column class (default: 'col-md-3') |
| `allOption` | No | String | "All" option text (for select) |
| `options` | No | Array | Options array [value => label] (for select) |
| `html` | No | String | Custom HTML (for custom type) |

### datatable-table-dynamic Component

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `tableId` | String | 'dataTable' | HTML id for the table |
| `columns` | Array | `[]` | Array of column configurations |
| `showIdColumn` | Boolean | `true` | Show ID (#) column |
| `showActionsColumn` | Boolean | `true` | Show Actions column |

### Column Configuration Array

Each column in the `columns` array can have:

| Key | Required | Type | Description |
|-----|----------|------|-------------|
| `label` | Yes | String | Column header text |
| `rtl` | No | Boolean | Set to true for RTL text direction |

---

## 💡 Pro Tips

### 1. Building Filters from Collections

```php
// In your controller
$departments = Department::where('active', 1)->get();

// In your view
$filters[] = [
    'type' => 'select',
    'id' => 'department_id',
    'label' => __('category.department'),
    'allOption' => __('category.all_departments'),
    'options' => $departments->pluck('name', 'id')->toArray()
];
```

### 2. Building Multi-Language Columns

```php
@php
$columns = [];
foreach ($languages as $language) {
    $columns[] = [
        'label' => __('common.name') . ' (' . $language->name . ')',
        'rtl' => $language->rtl
    ];
}
$columns[] = ['label' => __('common.status')];
$columns[] = ['label' => __('common.created_at')];
@endphp

<x-datatable-table-dynamic :columns="$columns" />
```

### 3. Hide Date Filters or Buttons

```blade
{{-- Without date filters --}}
<x-datatable-filters-dynamic 
    :filters="$filters"
    :showDateFilters="false"
/>

{{-- Without buttons --}}
<x-datatable-filters-dynamic 
    :filters="$filters"
    :showButtons="false"
/>

{{-- Without entries selector --}}
<x-datatable-filters-dynamic 
    :filters="$filters"
    :showEntriesSelector="false"
/>
```

### 4. JavaScript Integration

The components use standard IDs, so your JavaScript works the same:

```javascript
// Access any filter by its ID
$('#search').on('keyup', function() { ... });
$('#country_id').on('change', function() { ... });
$('#department_id').on('change', function() { ... });

// Reset button clears all fields automatically
$('#resetFilters').on('click', function() {
    // Clear your custom filters here
    $('#search').val('');
    $('#country_id').val('');
    $('#department_id').val('');
    // etc...
});
```

---

## 🎨 Complete Working Example

Here's a complete example for a Categories page:

```blade
@extends('layout.app')
@section('title', __('category.categories_management'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[...]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('category.categories_management') }}</h4>
                        <a href="{{ route('admin.categories.create') }}"
                            class="btn btn-primary btn-squared shadow-sm px-4">
                            <i class="uil uil-plus"></i> {{ __('category.add_category') }}
                        </a>
                    </div>

                    @php
                    $filters = [
                        [
                            'type' => 'search',
                            'id' => 'search',
                            'label' => __('common.search'),
                            'icon' => 'uil uil-search',
                            'placeholder' => __('category.search_by_name'),
                            'col' => 'col-md-3'
                        ],
                        [
                            'type' => 'select',
                            'id' => 'department_id',
                            'label' => __('category.department'),
                            'icon' => 'uil uil-layers',
                            'allOption' => __('category.all_departments'),
                            'options' => $departments->pluck('name', 'id')->toArray(),
                            'col' => 'col-md-3'
                        ],
                        [
                            'type' => 'select',
                            'id' => 'active',
                            'label' => __('category.activation'),
                            'icon' => 'uil uil-check-circle',
                            'allOption' => __('category.all'),
                            'options' => [
                                '1' => __('category.active'),
                                '0' => __('category.inactive')
                            ],
                            'col' => 'col-md-3'
                        ]
                    ];

                    $columns = [];
                    foreach ($languages as $language) {
                        $columns[] = [
                            'label' => __('category.name') . ' (' . $language->name . ')',
                            'rtl' => $language->rtl
                        ];
                    }
                    $columns[] = ['label' => __('category.department')];
                    $columns[] = ['label' => __('category.activation')];
                    $columns[] = ['label' => __('category.created_at')];
                    @endphp

                    <x-datatable-filters-dynamic :filters="$filters" />
                    <x-datatable-table-dynamic tableId="categoriesDataTable" :columns="$columns" />
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Your existing DataTable JavaScript code stays here --}}
@endpush
```

---

## ✅ Migration Steps

1. **Define your filters array** with all the filters you need
2. **Define your columns array** with all the columns you need
3. **Replace old HTML** with the component calls
4. **Keep your JavaScript** exactly as is (just make sure filter IDs match)
5. **Test** search, filters, and export functionality

---

## 🚨 Important Notes

- Filter `id` must match the ID you use in your JavaScript
- The component automatically includes Export Excel and Reset buttons
- Date filters (created_date_from, created_date_to) are included by default
- You can disable date filters with `:showDateFilters="false"`
- Entries selector is included by default
- Actions column is included by default

---

## 📦 Files Created

1. `/resources/views/components/datatable-filters-dynamic.blade.php` - Dynamic filters component
2. `/resources/views/components/datatable-table-dynamic.blade.php` - Dynamic table component
3. `/resources/views/components/DYNAMIC-COMPONENTS-USAGE.md` - This guide

The original simple components are still available for basic use cases!

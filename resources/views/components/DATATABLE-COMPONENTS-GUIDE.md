# DataTable Components Guide

## 📁 Created Components

I've created two reusable Blade components for DataTables in:
`resources/views/components/`

### 1️⃣ **datatable-filters.blade.php**
Handles all the filter UI including:
- Live search info alert
- Search input
- Active/Inactive filter
- Date range filters (from/to)
- Export Excel button
- Reset Filters button
- Entries per page selector

### 2️⃣ **datatable-table.blade.php**
Handles the table structure with:
- Customizable table ID
- Multi-language column support
- Additional custom columns
- Automatic ID and Actions columns

---

## 🚀 Quick Start Usage

### Step 1: Basic Implementation

Replace your existing filters and table HTML with the components:

```blade
{{-- OLD CODE (Remove this) --}}
<div class="alert alert-info glowing-alert">...</div>
<div class="mb-25">
    <div class="card border-0 shadow-sm">
        <!-- All filter fields -->
    </div>
</div>
<div class="table-responsive">
    <table id="activitiesDataTable">...</table>
</div>

{{-- NEW CODE (Use this) --}}
<x-datatable-filters 
    :searchPlaceholder="__('activity.search_by_name')"
    :showActiveFilter="true"
    :activeOptions="[
        'label' => __('activity.activation'),
        'all' => __('activity.all'),
        'active' => __('activity.active'),
        'inactive' => __('activity.inactive')
    ]"
/>

<x-datatable-table 
    tableId="activitiesDataTable"
    :languages="$languages"
    :columns="[
        'nameLabel' => __('activity.name'),
        'additional' => [
            __('activity.activation'),
            __('activity.created_at')
        ]
    ]"
/>
```

---

## 📋 Component Props Reference

### **datatable-filters Component**

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `searchPlaceholder` | String | No | 'Search...' | Placeholder text for search input |
| `showActiveFilter` | Boolean | No | `true` | Show/hide the active status dropdown |
| `activeOptions` | Array | No | `[]` | Custom labels for active filter |
| `additionalFilters` | Slot | No | `null` | Slot for extra filter fields |

**activeOptions Array Structure:**
```php
[
    'label' => 'Status',        // Label for the filter
    'all' => 'All',            // Text for "all" option
    'active' => 'Active',      // Text for "active" option
    'inactive' => 'Inactive'   // Text for "inactive" option
]
```

### **datatable-table Component**

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `tableId` | String | No | 'dataTable' | HTML ID for the table element |
| `languages` | Collection | No | `null` | Languages collection for multi-lang columns |
| `columns` | Array | No | `[]` | Column configuration |

**columns Array Structure:**
```php
[
    'nameLabel' => 'Name',     // Label for the name column
    'additional' => [          // Array of additional column headers
        'Status',
        'Created At'
    ]
]
```

---

## 🎨 Advanced Examples

### Example 1: Categories with Department Filter

```blade
<x-datatable-filters 
    :searchPlaceholder="__('category.search_by_name')"
    :showActiveFilter="true"
    :activeOptions="[
        'label' => __('category.activation'),
        'all' => __('category.all'),
        'active' => __('category.active'),
        'inactive' => __('category.inactive')
    ]"
>
    {{-- Additional Department Filter --}}
    <x-slot name="additionalFilters">
        <div class="col-md-3">
            <div class="form-group">
                <label for="department_id" class="il-gray fs-14 fw-500 mb-10">
                    {{ __('category.department') }}
                </label>
                <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                        id="department_id">
                    <option value="">{{ __('category.all_departments') }}</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-slot>
</x-datatable-filters>

<x-datatable-table 
    tableId="categoriesDataTable"
    :languages="$languages"
    :columns="[
        'nameLabel' => __('category.name'),
        'additional' => [
            __('category.department'),
            __('category.activation'),
            __('category.created_at')
        ]
    ]"
/>
```

### Example 2: Simple Table Without Active Filter

```blade
<x-datatable-filters 
    :searchPlaceholder="__('brand.search_by_name')"
    :showActiveFilter="false"
/>

<x-datatable-table 
    tableId="brandsDataTable"
    :languages="$languages"
    :columns="[
        'nameLabel' => __('brand.name'),
        'additional' => [
            __('brand.created_at')
        ]
    ]"
/>
```

### Example 3: Cities with Country Filter

```blade
<x-datatable-filters 
    :searchPlaceholder="__('areasettings::city.search_placeholder')"
    :showActiveFilter="true"
    :activeOptions="[
        'label' => __('areasettings::city.status'),
        'all' => __('areasettings::city.all_status'),
        'active' => __('areasettings::city.active'),
        'inactive' => __('areasettings::city.inactive')
    ]"
>
    <x-slot name="additionalFilters">
        <div class="col-md-4">
            <div class="form-group">
                <label for="country_id" class="il-gray fs-14 fw-500 mb-10">
                    <i class="uil uil-globe me-1"></i>
                    {{ __('areasettings::city.country') }}
                </label>
                <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                        id="country_id">
                    <option value="">{{ __('areasettings::city.all_countries') }}</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">
                            {{ $country->getTranslation('name', app()->getLocale()) ?? $country->code }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-slot>
</x-datatable-filters>

<x-datatable-table 
    tableId="citiesDataTable"
    :languages="$languages"
    :columns="[
        'nameLabel' => __('areasettings::city.name'),
        'additional' => [
            __('areasettings::city.country'),
            __('areasettings::city.regions'),
            __('areasettings::city.status'),
            __('areasettings::city.created_at')
        ]
    ]"
/>
```

---

## ⚙️ JavaScript Integration

The components use standard IDs, so your existing JavaScript code will work without changes:

```javascript
// Search input
$('#search').on('keyup', function() { ... });

// Active filter
$('#active').on('change', function() { ... });

// Date filters
$('#created_date_from').on('change', function() { ... });
$('#created_date_to').on('change', function() { ... });

// Buttons
$('#exportExcel').on('click', function() { ... });
$('#resetFilters').on('click', function() { ... });

// Entries selector
$('#entriesSelect').on('change', function() { ... });
```

---

## 📝 Migration Checklist

When converting an existing index.blade.php to use these components:

- [ ] Copy your existing JavaScript code (keep it as is)
- [ ] Replace filter section with `<x-datatable-filters />`
- [ ] Replace table HTML with `<x-datatable-table />`
- [ ] Pass necessary props (searchPlaceholder, activeOptions, columns)
- [ ] Add additional filters using the slot if needed
- [ ] Test search, filters, and export functionality
- [ ] Verify the table displays correctly

---

## 🎯 Benefits

✅ **Code Reusability** - Use the same components across all modules
✅ **Consistency** - Same UI/UX everywhere
✅ **Easy Maintenance** - Update one component, all pages benefit
✅ **Less Code** - Reduce repetitive HTML by 70%
✅ **Flexibility** - Customize with props and slots
✅ **Clean Code** - More readable and maintainable

---

## 📦 Files Created

1. `/resources/views/components/datatable-filters.blade.php` - Filters component
2. `/resources/views/components/datatable-table.blade.php` - Table component
3. `/resources/views/components/README-DATATABLE-USAGE.md` - Quick reference
4. `/resources/views/components/DATATABLE-COMPONENTS-GUIDE.md` - This detailed guide
5. `/Modules/CategoryManagment/resources/views/activity/index-with-components-EXAMPLE.blade.php` - Complete example

---

## 🔧 Need Help?

Refer to the example file:
`Modules/CategoryManagment/resources/views/activity/index-with-components-EXAMPLE.blade.php`

This shows the complete implementation with all JavaScript code intact.

# DataTable Components Usage Guide

## Components Created

### 1. `datatable-filters.blade.php`
Renders the search filters, date filters, and action buttons.

### 2. `datatable-table.blade.php`
Renders the DataTable structure with customizable columns.

## Usage Example

### Basic Usage in Activities Index

```blade
{{-- In your index.blade.php --}}

<div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
    <div class="d-flex justify-content-between align-items-center mb-25">
        <h4 class="mb-0 fw-500">{{ __('activity.activities_management') }}</h4>
        @can('activities.create')
            <div class="d-flex gap-2">
                <a href="{{ route('admin.category-management.activities.create') }}"
                    class="btn btn-primary btn-default btn-squared text-capitalize">
                    <i class="uil uil-plus"></i> {{ __('activity.add_activity') }}
                </a>
            </div>
        @endcan
    </div>

    {{-- Filters Component --}}
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

    {{-- Table Component --}}
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
</div>
```

### Advanced Usage with Additional Filters

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
    {{-- Additional Filters Slot --}}
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
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-slot>
</x-datatable-filters>
```

### Props Available

#### datatable-filters Component

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `searchPlaceholder` | string | 'Search...' | Placeholder text for search input |
| `showActiveFilter` | boolean | true | Show/hide active status filter |
| `activeOptions` | array | [] | Labels for active filter (label, all, active, inactive) |
| `additionalFilters` | slot | null | Slot for additional custom filters |

#### datatable-table Component

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `tableId` | string | 'dataTable' | The ID of the table element |
| `languages` | collection | null | Languages collection for multi-language columns |
| `columns` | array | [] | Column configuration (nameLabel, additional) |

## Notes

- The filters component automatically includes search, active filter, date filters, and action buttons
- The table component automatically includes ID column and Actions column
- You can customize the columns by passing the `columns` array
- Additional filters can be added using the `additionalFilters` slot

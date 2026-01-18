# Global Reusable Components Guide

This document provides a comprehensive guide to all global reusable components available in the project.

## 📋 DataTable Components

### 1. `<x-datatable-wrapper>`
**Location:** `resources/views/components/datatable-wrapper.blade.php`

Complete DataTable wrapper with built-in AJAX script, filters, and header management.

**Props:**
- `title` - Table title
- `icon` - Icon class (default: 'uil uil-list-ul')
- `createRoute` - Route for create button (optional)
- `createText` - Create button text (optional)
- `showExport` - Show export button (default: false)
- `tableId` - Table DOM ID (default: 'dataTable')
- `ajaxUrl` - AJAX endpoint for data loading
- `headers` - Array of table headers
- `columnsJson` - JSON string of DataTable columns configuration
- `customSelectIds` - Array of custom select IDs for filters
- `order` - Default sorting (default: [[0, 'desc']])
- `pageLength` - Items per page (default: 10)

**Slots:**
- `filters` - Filter components slot

**Example:**
```blade
<x-datatable-wrapper
    :title="trans('menu.refunds.all')"
    icon="uil uil-redo"
    tableId="refundsDataTable"
    ajaxUrl="{{ route('admin.refunds.datatable') }}"
    :headers="$headers"
    :columnsJson="$columnsJson"
    :customSelectIds="['status_filter']"
    :order="[[7, 'desc']]">
    
    <x-slot name="filters">
        <x-datatable-filters-advanced ... />
    </x-slot>
</x-datatable-wrapper>
```

### 2. `<x-datatable-filters-advanced>`
**Location:** `resources/views/components/datatable-filters-advanced.blade.php`

Advanced search and filter component with custom selects and date ranges.

**Props:**
- `searchPlaceholder` - Search input placeholder
- `filters` - Array of filter configurations
- `showDateFilters` - Show date range filters (default: false)

**Filter Configuration:**
```php
[
    'name' => 'status_filter',
    'id' => 'status_filter',
    'label' => 'Status',
    'icon' => 'uil uil-check-circle',
    'options' => [
        ['id' => 'pending', 'name' => 'Pending'],
        ['id' => 'approved', 'name' => 'Approved'],
    ],
    'selected' => request('status'),
    'placeholder' => 'All',
]
```

### 3. `<x-datatable-actions>`
**Location:** `resources/views/components/datatable-actions.blade.php`

Reusable action buttons for table rows (view, edit, delete).

**Props:**
- `viewUrl` - View action URL (optional)
- `editUrl` - Edit action URL (optional)
- `deleteUrl` - Delete action URL (optional)
- `deleteId` - ID for delete confirmation (optional)

## 📝 Form Components

### 4. `<x-form-input-field>`
**Location:** `resources/views/components/form-input-field.blade.php`

Reusable input field with label, validation, help text, and icons.

**Props:**
- `type` - Input type (default: 'text')
- `name` - Input name (required)
- `id` - Input ID (optional, defaults to name)
- `label` - Field label
- `value` - Input value
- `placeholder` - Placeholder text
- `required` - Is required (default: false)
- `min` - Min value for number inputs
- `max` - Max value for number inputs
- `step` - Step value for number inputs
- `helpText` - Help text below input
- `icon` - Icon class for label
- `disabled` - Disabled state
- `readonly` - Readonly state
- `class` - Additional CSS classes

**Example:**
```blade
<x-form-input-field
    type="number"
    name="refund_processing_days"
    :label="trans('refund::refund.fields.refund_processing_days')"
    :value="$settings->refund_processing_days ?? 7"
    placeholder="7"
    :min="1"
    :max="365"
    :required="true"
    :helpText="trans('refund::refund.help.refund_processing_days')"
/>
```

### 5. `<x-form-switcher>`
**Location:** `resources/views/components/form-switcher.blade.php`

Reusable switcher/toggle with different colors.

**Props:**
- `name` - Input name (required)
- `id` - Input ID (optional, defaults to name)
- `label` - Field label
- `checked` - Checked state (default: false)
- `value` - Checkbox value (default: '1')
- `helpText` - Help text below switcher
- `switchColor` - Color theme: 'primary', 'success', 'danger', 'warning', 'info'
- `disabled` - Disabled state

**Example:**
```blade
<x-form-switcher
    name="customer_pays_return_shipping"
    :label="trans('refund::refund.fields.customer_pays_return_shipping')"
    :checked="$settings->customer_pays_return_shipping ?? 0"
    switchColor="primary"
    :helpText="trans('refund::refund.help.customer_pays_return_shipping')"
/>
```

### 6. `<x-form-ajax-handler>`
**Location:** `resources/views/components/form-ajax-handler.blade.php`

Complete AJAX form submission handler with loading overlay, progress bar, and validation errors display.

**Props:**
- `formId` - Form DOM ID (default: 'ajaxForm')
- `successMessage` - Success message to display
- `redirectUrl` - URL to redirect after success (optional)
- `showSuccessAlert` - Show success alert (default: true)
- `reloadOnSuccess` - Reload page on success (default: false)

**Features:**
- Automatic loading overlay with progress bar
- Validation error display
- Success animation
- Automatic form re-enable after completion

**Example:**
```blade
@push('scripts')
<x-form-ajax-handler
    formId="refundSettingsForm"
    :successMessage="trans('refund::refund.messages.settings_updated')"
    :showSuccessAlert="true"
/>
@endpush
```

### 7. `<x-form-card-handler>`
**Location:** `resources/views/components/form-card-handler.blade.php`

Complete form wrapper with card, header, alerts, AJAX submission, and action buttons. This is the most comprehensive form component that combines everything.

**Props:**
- `formId` - Form DOM ID (default: 'ajaxForm')
- `formAction` - Form action URL (required)
- `formMethod` - HTTP method: POST, PUT, PATCH, DELETE (default: 'POST')
- `title` - Card header title
- `icon` - Icon class for header (default: 'uil uil-setting')
- `backUrl` - Back button URL (optional)
- `backText` - Back button text (optional, default: trans('common.back'))
- `submitText` - Submit button text (optional, default: trans('common.save_changes'))
- `successMessage` - Success message to display
- `redirectUrl` - URL to redirect after success (optional)
- `showSuccessAlert` - Show success alert (default: true)
- `reloadOnSuccess` - Reload page on success (default: false)

**Features:**
- Complete card structure with header
- Alert container for dynamic messages
- Server-side validation error display
- AJAX form submission with loading overlay
- Progress bar animation
- Success animation
- Back and submit buttons
- Automatic CSRF and method spoofing
- **Built-in loading overlay** (no need to add separately)

**Example:**
```blade
<x-form-card-handler
    formId="refundSettingsForm"
    :formAction="route('admin.refunds.settings.update')"
    formMethod="PUT"
    :title="trans('refund::refund.titles.refund_settings')"
    icon="uil uil-setting"
    :backUrl="route('admin.refunds.index')"
    :successMessage="trans('refund::refund.messages.settings_updated')"
    :showSuccessAlert="true">
    
    {{-- Your form fields here --}}
    <div class="col-md-6">
        <x-form-input-field ... />
    </div>
    
    <div class="col-md-6">
        <x-form-switcher ... />
    </div>
</x-form-card-handler>

{{-- Loading overlay is automatically included! --}}
```

## 🔄 Loading Components

### 8. `<x-loading-overlay>`
**Location:** `resources/views/components/loading-overlay.blade.php`

Global loading overlay with progress bar and success animation.

**Props:**
- `loadingText` - Loading text (default: trans('loading.processing'))
- `loadingSubtext` - Loading subtext (default: trans('loading.please_wait'))

**JavaScript API:**
```javascript
// Show loading
LoadingOverlay.show();

// Hide loading
LoadingOverlay.hide();

// Animate progress bar
LoadingOverlay.animateProgressBar(60, 300); // 60%, 300ms duration

// Show success animation
LoadingOverlay.showSuccess('Success!', 'Redirecting...');

// Progress sequence
LoadingOverlay.progressSequence([30, 60, 90, 100], [300, 200, 200, 200]);
```

**Example:**
```blade
@push('after-body')
<x-loading-overlay
    :loadingText="trans('common.processing')"
    :loadingSubtext="trans('common.please_wait')"
/>
@endpush
```

## 🎯 Usage Best Practices

### Simple Form Implementation (Recommended)

The easiest way to create a form is using the `<x-form-card-handler>` component:

```blade
@extends('layout.app')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="..." />

    {{-- Complete Form with Card --}}
    <x-form-card-handler
        formId="myForm"
        :formAction="route('admin.settings.update')"
        formMethod="PUT"
        :title="trans('settings.title')"
        :backUrl="route('admin.dashboard')"
        :successMessage="trans('messages.updated')">
        
        <div class="col-md-6">
            <x-form-input-field
                name="field_name"
                :label="trans('field.label')"
                :value="$model->field_name"
                :required="true"
            />
        </div>
        
        <div class="col-md-6">
            <x-form-switcher
                name="is_active"
                :label="trans('field.active')"
                :checked="$model->is_active"
            />
        </div>
    </x-form-card-handler>
</div>
@endsection

{{-- That's it! Loading overlay is automatically included --}}
```

That's it! No need for separate form tags, AJAX handlers, or action buttons.

### DataTable Implementation

1. **Prepare data in PHP:**
```php
@php
// Build headers
$headers = [
    ['label' => '#', 'class' => 'text-center'],
    ['label' => trans('field.name')],
];

// Build columns
$columns = [
    ['data' => 'index', 'orderable' => false, 'className' => 'text-center'],
    ['data' => 'name', 'orderable' => false],
];

// Convert to JSON and add render functions
$columnsJson = json_encode($columns);
@endphp
```

2. **Use the wrapper component:**
```blade
<x-datatable-wrapper
    title="My Table"
    ajaxUrl="{{ route('admin.data.datatable') }}"
    :headers="$headers"
    :columnsJson="$columnsJson">
    
    <x-slot name="filters">
        <x-datatable-filters-advanced ... />
    </x-slot>
</x-datatable-wrapper>
```

### Advanced Form Implementation (Manual Control)

If you need more control over the form structure, you can use individual components:

1. **Create form with components:**
```blade
<form id="myForm" method="POST" action="{{ route('admin.update') }}">
    @csrf
    @method('PUT')
    
    <x-form-input-field
        name="field_name"
        :label="trans('field.label')"
        :value="$model->field_name"
        :required="true"
    />
    
    <x-form-switcher
        name="is_active"
        :label="trans('field.active')"
        :checked="$model->is_active"
    />
    
    <button type="submit">Save</button>
</form>
```

2. **Add AJAX handler:**
```blade
@push('scripts')
<x-form-ajax-handler
    formId="myForm"
    :successMessage="trans('messages.updated')"
/>
@endpush

@push('after-body')
<x-loading-overlay />
@endpush
```

## 📦 Component Dependencies

- **jQuery** - Required for DataTables
- **DataTables** - Required for table functionality
- **Bootstrap 5** - Required for styling
- **CustomSelect** - Required for custom select dropdowns (project-specific)

## 🌍 Translation Keys Used

### Common Translations (lang/en/common.php, lang/ar/common.php)
- `common.show`, `common.showing`, `common.to`, `common.of`
- `common.entries`, `common.loading`, `common.processing`
- `common.no_records_found`, `common.search`
- `common.filtered_from`, `common.total_entries`
- `common.sort_ascending`, `common.sort_descending`
- `common.success`, `common.error`, `common.please_wait`
- `common.processing`, `common.please_check_form`
- `common.validation_errors`, `common.error_occurred`

## ✅ Complete Implementation Example

See `Modules/Refund/resources/views/refund-requests/index.blade.php` for a complete DataTable implementation.

See `Modules/Refund/resources/views/settings/index.blade.php` for a complete Form implementation.

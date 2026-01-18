# Form Card Handler Component - Quick Reference

## 🎯 Purpose

The `<x-form-card-handler>` component is an all-in-one form solution that combines:
- Card structure with header
- Alert container
- Form with CSRF and method spoofing
- AJAX submission with loading overlay
- Validation error display
- Back and submit buttons

## 📦 Component Location

`resources/views/components/form-card-handler.blade.php`

## 🚀 Quick Start

### Minimal Example

```blade
<x-form-card-handler
    formId="myForm"
    :formAction="route('admin.update')"
    :title="trans('page.title')">
    
    <div class="col-md-12">
        <x-form-input-field
            name="name"
            :label="trans('field.name')"
            :value="$model->name"
        />
    </div>
</x-form-card-handler>

@push('after-body')
    <x-loading-overlay />
@endpush
```

### Complete Example

```blade
<x-form-card-handler
    formId="settingsForm"
    :formAction="route('admin.settings.update')"
    formMethod="PUT"
    :title="trans('settings.title')"
    icon="uil uil-cog"
    :backUrl="route('admin.dashboard')"
    :backText="trans('common.back')"
    :submitText="trans('common.save')"
    :successMessage="trans('messages.updated')"
    :showSuccessAlert="true">
    
    <div class="col-md-6">
        <x-form-input-field
            type="text"
            name="site_name"
            :label="trans('settings.site_name')"
            :value="$settings->site_name"
            :required="true"
        />
    </div>
    
    <div class="col-md-6">
        <x-form-input-field
            type="email"
            name="admin_email"
            :label="trans('settings.admin_email')"
            :value="$settings->admin_email"
            :required="true"
        />
    </div>
    
    <div class="col-md-6">
        <x-form-switcher
            name="maintenance_mode"
            :label="trans('settings.maintenance_mode')"
            :checked="$settings->maintenance_mode"
            switchColor="danger"
        />
    </div>
</x-form-card-handler>

@push('after-body')
    <x-loading-overlay />
@endpush
```

## 📋 Props Reference

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `formId` | string | 'ajaxForm' | Form DOM ID |
| `formAction` | string | **required** | Form action URL |
| `formMethod` | string | 'POST' | HTTP method (POST, PUT, PATCH, DELETE) |
| `title` | string | '' | Card header title |
| `icon` | string | 'uil uil-setting' | Icon class for header |
| `backUrl` | string | null | Back button URL (optional) |
| `backText` | string | trans('common.back') | Back button text |
| `submitText` | string | trans('common.save_changes') | Submit button text |
| `successMessage` | string | null | Success message to display |
| `redirectUrl` | string | null | URL to redirect after success |
| `showSuccessAlert` | boolean | true | Show success alert |
| `reloadOnSuccess` | boolean | false | Reload page on success |

## 🎨 Features

### 1. Automatic Card Structure
- Card with shadow
- Header with icon and title
- Body with proper padding

### 2. Alert System
- Alert container for dynamic messages
- Server-side validation errors display
- Session success messages display
- AJAX success/error alerts

### 3. Form Handling
- Automatic CSRF token
- Method spoofing for PUT/PATCH/DELETE
- Form fields in responsive grid (row)

### 4. AJAX Submission
- Loading overlay with progress bar
- Progress sequence: 30% → 60% → 90% → 100%
- Success animation with checkmark
- Validation error display (inline and alert)
- Automatic form re-enable

### 5. Action Buttons
- Optional back button
- Submit button with loading state
- Proper spacing and alignment

## 🔄 Form Submission Flow

1. User clicks submit button
2. Button shows loading spinner
3. Loading overlay appears
4. Progress bar animates to 30%
5. AJAX request sent
6. Progress bar animates to 60%
7. Response received
8. Progress bar animates to 90%
9. Processing complete
10. Progress bar animates to 100%
11. Success animation shows
12. Alert displayed (if enabled)
13. Redirect/reload (if configured)
14. Form re-enabled (if no redirect)

## 💡 Usage Tips

### 1. Loading Overlay is Automatic

The component automatically includes the loading overlay, so you don't need to add it separately:

```blade
{{-- ✅ Correct - No need for loading overlay --}}
<x-form-card-handler ...>
    <div class="col-md-6">
        <x-form-input-field ... />
    </div>
</x-form-card-handler>

{{-- ❌ Not needed anymore --}}
@push('after-body')
    <x-loading-overlay />
@endpush
```

### 2. Use Column Classes for Fields

The component wraps content in a `<div class="row">`, so use column classes:

```blade
<x-form-card-handler ...>
    <div class="col-md-6">
        <x-form-input-field ... />
    </div>
    
    <div class="col-md-6">
        <x-form-switcher ... />
    </div>
    
    <div class="col-md-12">
        <x-form-input-field ... />
    </div>
</x-form-card-handler>
```

### 3. Method Spoofing

For PUT/PATCH/DELETE requests, just set `formMethod`:

```blade
<x-form-card-handler
    formMethod="PUT"
    ...>
```

The component automatically adds `@method('PUT')`.

### 4. Conditional Back Button

Back button only shows if `backUrl` is provided:

```blade
{{-- With back button --}}
<x-form-card-handler :backUrl="route('admin.index')" ...>

{{-- Without back button --}}
<x-form-card-handler ...>
```

### 5. Custom Success Behavior

```blade
{{-- Show alert and stay on page --}}
<x-form-card-handler
    :successMessage="trans('messages.saved')"
    :showSuccessAlert="true">

{{-- Redirect after success --}}
<x-form-card-handler
    :successMessage="trans('messages.saved')"
    :redirectUrl="route('admin.index')">

{{-- Reload page after success --}}
<x-form-card-handler
    :successMessage="trans('messages.saved')"
    :reloadOnSuccess="true">
```

## 🎯 Real-World Example

See `Modules/Refund/resources/views/settings/index.blade.php` for a complete implementation.

### Before (Manual):
- 120+ lines of code
- Manual form structure
- Manual AJAX handling
- Manual error display
- Manual loading overlay

### After (Component):
- 40 lines of code
- Clean, readable
- All features included
- Easy to maintain

## 🔧 Controller Requirements

Your controller should return JSON for AJAX requests:

```php
public function update(Request $request)
{
    $validated = $request->validate([
        'field_name' => 'required|string',
    ]);
    
    // Update logic here
    
    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => trans('messages.updated'),
            'redirect' => route('admin.index'), // Optional
        ]);
    }
    
    return redirect()->back()->with('success', trans('messages.updated'));
}
```

## ✅ Checklist

When using this component, make sure:

- [ ] `formAction` prop is set to correct route
- [ ] `formMethod` matches your controller method
- [ ] ~~Loading overlay included~~ **Automatically included!**
- [ ] Controller returns JSON for AJAX requests
- [ ] All form fields are wrapped in column divs
- [ ] Success message translation exists
- [ ] Back URL is correct (if provided)

## 🎉 Benefits

1. **80% less code** - No need to write form structure, AJAX, alerts
2. **Consistent UI** - All forms look and behave the same
3. **Built-in features** - Loading, validation, success animation
4. **Easy maintenance** - Update component, all forms benefit
5. **Developer friendly** - Simple, clean syntax
6. **User friendly** - Smooth animations, clear feedback

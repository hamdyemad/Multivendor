# Global Form Error Handler

A reusable JavaScript component that provides consistent error handling and loading states for all CRUD forms across the application.

## Features

- ✅ **Progress Loading Overlay** - Shows loading spinner with progress bar during form submission
- ✅ **Error Alert Display** - Shows comprehensive error alert at top of form
- ✅ **Field-Specific Errors** - Displays validation errors under each input field
- ✅ **Automatic Error Clearing** - Clears previous errors before new submission
- ✅ **Success Handling** - Shows success message and redirects automatically
- ✅ **Select2 Support** - Special handling for Select2 dropdown fields
- ✅ **RTL Support** - Works with right-to-left languages
- ✅ **Responsive Design** - Mobile-friendly error display
- ✅ **Auto-initialization** - Can be enabled with simple data attributes

## Quick Start

### Method 1: Auto-initialization (Recommended)

Add data attributes to your form:

```html
<form id="myForm" 
      method="POST" 
      action="/my-route" 
      data-auto-error-handler
      data-loading-text="Creating item..."
      data-redirect-url="/items">
    @csrf
    
    <!-- Your form fields -->
    <input type="text" name="title" class="form-control">
    <div class="error-message text-danger" id="error-title" style="display: none;"></div>
    
    <button type="submit">Submit</button>
</form>

<!-- Include the component -->
<x-form-error-handler />
```

### Method 2: Manual initialization

```html
<!-- Include the component -->
<x-form-error-handler :autoInit="false" />

<script>
$(document).ready(function() {
    new FormErrorHandler({
        formSelector: '#myForm',
        loadingText: 'Processing...',
        redirectUrl: '/items'
    });
});
</script>
```

## Component Usage

### Basic Component

```blade
<x-form-error-handler />
```

### With Custom Options

```blade
<x-form-error-handler 
    formSelector="#productForm"
    loadingText="Creating product..."
    redirectUrl="{{ route('products.index') }}"
    :redirectDelay="2000"
    :showProgressBar="true"
    :showErrorAlert="true"
    :showFieldErrors="true"
    :scrollOffset="100"
/>
```

## Form Field Component

Use the `form-field` component to automatically include error containers:

```blade
<x-form-field 
    name="title" 
    label="Product Title" 
    :required="true" 
    placeholder="Enter title" 
/>

<x-form-field 
    name="category_id" 
    label="Category" 
    type="select" 
    :required="true"
    :options="$categories" 
/>

<x-form-field 
    name="description" 
    label="Description" 
    type="textarea" 
    rows="4" 
/>
```

## Error Container Patterns

The error handler looks for error containers in this order:

1. `#error-{fieldName}` (dots replaced with escaped dots)
2. `#error-{fieldName}` (dots replaced with dashes)
3. `[data-error-for="{fieldName}"]`
4. `.error-{fieldName}` (dots replaced with dashes)

### Examples:

```html
<!-- For field name="title" -->
<div id="error-title" class="error-message text-danger" style="display: none;"></div>

<!-- For field name="translations[1][title]" -->
<div id="error-translations-1-title" class="error-message text-danger" style="display: none;"></div>

<!-- Using data attribute -->
<div data-error-for="vendor_id" class="error-message text-danger" style="display: none;"></div>
```

## Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `formSelector` | string | `'#form'` | CSS selector for the form |
| `loadingText` | string | `'Processing...'` | Text shown in loading overlay |
| `successText` | string | `'Success!'` | Text shown on success |
| `redirectUrl` | string | `null` | URL to redirect after success |
| `redirectDelay` | number | `1500` | Delay before redirect (ms) |
| `scrollOffset` | number | `100` | Offset when scrolling to errors |
| `showProgressBar` | boolean | `true` | Show progress bar in loading |
| `showErrorAlert` | boolean | `true` | Show error alert at top |
| `showFieldErrors` | boolean | `true` | Show errors under fields |

## Data Attributes

You can configure the handler using data attributes on the form:

```html
<form data-auto-error-handler
      data-loading-text="Creating product..."
      data-success-text="Product created!"
      data-redirect-url="/products"
      data-redirect-delay="2000"
      data-show-progress-bar="true"
      data-show-error-alert="true"
      data-show-field-errors="true"
      data-scroll-offset="50">
```

## Error Response Format

The handler expects validation errors in this format:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "title": ["The title field is required."],
        "email": ["The email field is required.", "The email must be valid."],
        "translations.1.name": ["Name is required for English"]
    }
}
```

## Success Response Format

For success responses:

```json
{
    "success": true,
    "message": "Item created successfully!",
    "redirect": "/items/123"
}
```

## CSS Classes

The handler uses these CSS classes:

- `.error-message` - Error message styling
- `.is-invalid` - Invalid field styling
- `.border-danger` - Danger border styling
- `#form-error-alert` - Error alert styling
- `.form-error-list` - Error list styling

## Examples

### Product Form

```blade
<form id="productForm" 
      method="POST" 
      action="{{ route('products.store') }}"
      data-auto-error-handler
      data-loading-text="Creating product..."
      data-redirect-url="{{ route('products.index') }}">
    @csrf
    
    <x-form-field name="title" label="Title" :required="true" />
    <x-form-field name="sku" label="SKU" :required="true" />
    <x-form-field name="vendor_id" label="Vendor" type="select" :options="$vendors" :required="true" />
    
    <button type="submit">Create Product</button>
</form>

<x-form-error-handler />
```

### Vendor Form

```blade
<form id="vendorForm" 
      method="POST" 
      action="{{ route('vendors.store') }}"
      data-auto-error-handler
      data-loading-text="Creating vendor..."
      data-redirect-url="{{ route('vendors.index') }}">
    @csrf
    
    <x-form-field name="translations[1][name]" label="Name (English)" :required="true" />
    <x-form-field name="translations[2][name]" label="Name (Arabic)" :required="true" dir="rtl" />
    <x-form-field name="email" label="Email" type="email" :required="true" />
    
    <button type="submit">Create Vendor</button>
</form>

<x-form-error-handler />
```

## Migration Guide

### From Custom Error Handling

Replace this:

```javascript
$('#form').on('submit', function(e) {
    e.preventDefault();
    // Custom AJAX submission with error handling
});
```

With this:

```html
<form id="form" data-auto-error-handler>
    <!-- form fields -->
</form>
<x-form-error-handler />
```

### Adding Error Containers

Add error containers to existing forms:

```html
<!-- Before -->
<input type="text" name="title" class="form-control">

<!-- After -->
<input type="text" name="title" class="form-control">
<div class="error-message text-danger" id="error-title" style="display: none;"></div>
```

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Dependencies

- jQuery 3.x
- Bootstrap 5.x (for alert styling)
- LoadingOverlay.js (optional, for progress bars)
- Unicons (for error icons)

## Troubleshooting

### Error containers not found

Make sure error containers follow the naming convention:
- Field `name="title"` → Container `id="error-title"`
- Field `name="translations[1][title]"` → Container `id="error-translations-1-title"`

### Form not submitting

Check that:
1. Form has correct `id` attribute
2. Component is included in the page
3. JavaScript console for errors

### Styling issues

Ensure the global CSS is included:
```blade
@vite(['resources/assets/scss/form-error-handler.scss'])
```

## Contributing

When adding new form types:

1. Add error containers for all required fields
2. Use the `form-field` component when possible
3. Test with various error scenarios
4. Ensure RTL support for Arabic fields
5. Test on mobile devices

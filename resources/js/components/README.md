# Tags Input Component

A reusable, customizable tags input component for Laravel applications with support for multiple languages, themes, and configurations.

## Installation

1. **Include the CSS:**
```scss
// In your main SCSS file
@import 'components/tags-input';
```

2. **Include the JavaScript:**
```javascript
// In your main JS file or via script tag
import TagsInput from './components/tags-input.js';
// OR
<script src="{{ asset('js/components/tags-input.js') }}"></script>
```

## Usage

### Blade Component (Recommended)

```blade
<x-tags-input 
    name="keywords" 
    :value="old('keywords', $model->keywords ?? '')" 
    placeholder="Type keywords and press Enter..." 
    language="en"
    :allow-duplicates="true"
    :max-tags="10"
    theme="primary"
    size="md"
/>
```

### Manual HTML + JavaScript

```html
<div class="tags-input-container" data-language="en">
    <div class="tags-display"></div>
    <input type="text" class="tags-input" placeholder="Type and press Enter...">
    <input type="hidden" name="keywords" value="">
</div>

<script>
new TagsInput('.tags-input-container', {
    placeholder: 'Type keywords...',
    language: 'en',
    allowDuplicates: true,
    maxTags: 10
});
</script>
```

### jQuery Plugin

```javascript
$('.tags-input-container').tagsInput({
    placeholder: 'Type keywords...',
    language: 'en',
    allowDuplicates: true,
    maxTags: 10
});
```

## Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `placeholder` | string | 'Type and press Enter...' | Placeholder text for English |
| `rtlPlaceholder` | string | 'اكتب واضغط Enter...' | Placeholder text for Arabic |
| `language` | string | 'en' | Language code ('en' or 'ar') |
| `allowDuplicates` | boolean | true | Allow duplicate tags |
| `maxTags` | number | null | Maximum number of tags (null = unlimited) |
| `delimiter` | string | ',' | Delimiter for separating tags |

## Blade Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | 'tags' | Form field name |
| `value` | string | '' | Initial value (comma-separated) |
| `placeholder` | string | 'Type and press Enter...' | English placeholder |
| `rtlPlaceholder` | string | 'اكتب واضغط Enter...' | Arabic placeholder |
| `language` | string | 'en' | Language ('en' or 'ar') |
| `allowDuplicates` | boolean | true | Allow duplicate tags |
| `maxTags` | number | null | Maximum tags limit |
| `delimiter` | string | ',' | Tag delimiter |
| `theme` | string | 'primary' | Color theme |
| `size` | string | 'md' | Size variant |
| `required` | boolean | false | Required field |
| `disabled` | boolean | false | Disabled state |
| `class` | string | '' | Additional CSS classes |
| `id` | string | auto | Component ID |

## Themes

Available themes:
- `primary` (default blue)
- `success` (green)
- `warning` (yellow)
- `danger` (red)
- `info` (cyan)

```blade
<x-tags-input theme="success" />
```

## Sizes

Available sizes:
- `sm` (small)
- `md` (medium, default)
- `lg` (large)

```blade
<x-tags-input size="lg" />
```

## Methods

### JavaScript API

```javascript
const tagsInput = new TagsInput('.tags-input-container');

// Get all tags
const tags = tagsInput.getTags();

// Set tags programmatically
tagsInput.setTags(['tag1', 'tag2', 'tag3']);

// Add a tag
tagsInput.addTagProgrammatically('new-tag');

// Clear all tags
tagsInput.clearTags();

// Destroy the component
tagsInput.destroy();
```

### Events

```javascript
// Listen for tag changes
$('.tags-input-container').on('tags:changed', function(event, tags) {
    console.log('Tags updated:', tags);
});
```

## Examples

### Basic Usage
```blade
<x-tags-input name="keywords" placeholder="Enter SEO keywords..." />
```

### Arabic Support
```blade
<x-tags-input 
    name="arabic_keywords" 
    language="ar"
    placeholder="أدخل الكلمات المفتاحية..."
/>
```

### With Validation
```blade
<x-tags-input 
    name="required_tags" 
    :required="true"
    :max-tags="5"
    :allow-duplicates="false"
/>
```

### Custom Theme and Size
```blade
<x-tags-input 
    name="categories" 
    theme="success"
    size="lg"
    placeholder="Add categories..."
/>
```

## Styling Customization

Override CSS variables:

```scss
.tags-input-container {
    --tags-primary-color: #your-color;
    --tags-border-radius: 12px;
    --tags-font-size: 16px;
}
```

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ (with polyfills)
- Mobile browsers

## Dependencies

- jQuery 3.x+
- Modern CSS support (flexbox, CSS variables)

# Variant Configuration Hierarchical Key Selection Fix

## Issue
When selecting a root key like "Door Type" that has child keys, the system was incorrectly showing the variant selector instead of showing the child key selector. The hierarchical key selection was not working because the `parent_key_id` field was missing from the data passed to JavaScript.

## Root Cause
1. The `VariantsConfigurationKeyResource` was not including the `parent_key_id` field in its transformation
2. The controller was using the wrong resource (`VariantsConfigurationResource` instead of `VariantsConfigurationKeyResource`) for variant keys
3. Without `parent_key_id`, the JavaScript function `getChildKeys()` couldn't identify which keys are children of the selected key

## Changes Made

### 1. Updated VariantsConfigurationKeyResource
**File**: `Modules/CatalogManagement/app/Http/Resources/VariantsConfigurationKeyResource.php`

Added `parent_key_id` field to the resource transformation:
```php
return [
    "id" => $this->id,
    "name" => $this->getTranslation('name', $locale),
    "parent_key_id" => $this->parent_key_id,  // ADDED
    'parent' => VariantsConfigurationKeyResource::make($this->whenLoaded('parent')),
    'children' => VariantsConfigurationKeyResource::collection($this->whenLoaded('childrenKeys')),
];
```

### 2. Fixed Controller to Use Correct Resource
**File**: `Modules/CatalogManagement/app/Http/Controllers/VariantsConfigurationController.php`

**Added import**:
```php
use Modules\CatalogManagement\app\Http\Resources\VariantsConfigurationKeyResource;
```

**Updated create() method**:
```php
$variantKeys = VariantsConfigurationKeyResource::collection($variantKeys)->resolve();
// Changed from: VariantsConfigurationResource::collection($variantKeys)->resolve();
```

**Updated edit() method**:
```php
$variantKeys = VariantsConfigurationKeyResource::collection($variantKeys)->resolve();
// Changed from: VariantsConfigurationResource::collection($variantKeys)->resolve();
```

### 3. Added Debug Logging (Temporary)
**File**: `Modules/CatalogManagement/resources/views/variants-config/form.blade.php`

Added console.log statements to help debug the hierarchical selection:
- In `getChildKeys()` function to see what keys are being filtered
- In root key change handler to see the selected key and available children

## How It Works Now

1. **Root Key Selection**: User selects a root key (e.g., "Door Type")
2. **Check for Children**: JavaScript checks if the selected key has child keys using `getChildKeys(selectedKeyId)`
3. **Show Appropriate Selector**:
   - If child keys exist → Show child key selector
   - If no child keys → Show variant selector (final key reached)
4. **Nested Selection**: Process continues recursively until the final key is reached
5. **Load Variants**: Once final key is selected, load variants for that key

## Data Flow

```
Backend (Controller)
  ↓
VariantConfigurationKey::with('translations')->get()
  ↓
VariantsConfigurationKeyResource::collection()
  ↓
JSON with parent_key_id field
  ↓
JavaScript (allVariantKeys array)
  ↓
getChildKeys(parentKeyId) filters by parent_key_id
  ↓
Hierarchical selection works correctly
```

## Testing

To test the fix:
1. Go to: http://127.0.0.1:8000/en/eg/admin/variants-configurations/create
2. Select "Door Type" (a key with children)
3. Should see: Child key selector appears (not variant selector)
4. Select a child key
5. Should see: Either another child key selector or variant selector (depending on hierarchy)

## Files Modified

1. `Modules/CatalogManagement/app/Http/Resources/VariantsConfigurationKeyResource.php`
2. `Modules/CatalogManagement/app/Http/Controllers/VariantsConfigurationController.php`
3. `Modules/CatalogManagement/resources/views/variants-config/form.blade.php` (debug logging)

## Next Steps

Once confirmed working:
1. Remove debug console.log statements from form.blade.php
2. Test with multiple levels of key hierarchy
3. Test variant selection after final key is selected
4. Test edit mode with existing hierarchical configurations

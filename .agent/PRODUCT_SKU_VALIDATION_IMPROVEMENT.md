# Product SKU Validation Error Messages - Improvement

## Problem
When editing products with multiple variants, SKU uniqueness errors were showing like:
```json
{
    "errors": {
        "variants.0.sku": ["SKU must be unique"],
        "variants.1.sku": ["SKU must be unique"],
        "variants.4.sku": ["SKU must be unique"]
    }
}
```

Users couldn't identify which variant (size/color combination) had the duplicate SKU.

## Solution
Enhanced error messages to include variant details (size, color) and the actual SKU value.

## Changes Made

### 1. UpdateProductRequest
**File**: `Modules/CatalogManagement/app/Http/Requests/Product/UpdateProductRequest.php`

Added variant description to error messages:
- Extracts size and color from variant data
- Builds descriptive text like "(Size: L, Color: Red)"
- Appends SKU value to error message
- Falls back to variant index if no size/color available

### 2. StoreProductRequest
**File**: `Modules/CatalogManagement/app/Http/Requests/Product/StoreProductRequest.php`

Same improvements for product creation.

### 3. UpdateStockPricingRequest
**File**: `Modules/CatalogManagement/app/Http/Requests/Product/UpdateStockPricingRequest.php`

Same improvements for stock/pricing updates.

## New Error Format

### Before:
```json
{
    "errors": {
        "variants.0.sku": ["SKU must be unique"],
        "variants.1.sku": ["SKU must be unique"]
    }
}
```

### After:
```json
{
    "errors": {
        "variants.0.sku": ["SKU must be unique (Size: L, Color: Red) - SKU: PROD-L-RED"],
        "variants.1.sku": ["SKU must be unique (Size: XL, Color: Blue) - SKU: PROD-XL-BLUE"]
    }
}
```

## Benefits

1. **Clear Identification**: Users can immediately see which variant has the duplicate SKU
2. **Size/Color Info**: Shows the exact size and color combination
3. **SKU Value**: Displays the actual SKU that's duplicated
4. **Better UX**: Reduces time spent debugging SKU conflicts
5. **Fallback**: If size/color not available, shows variant number

## Implementation Details

The error message is built dynamically:
```php
// Build variant description
$variantDescription = [];
if (!empty($variant['size'])) {
    $variantDescription[] = "Size: {$variant['size']}";
}
if (!empty($variant['color'])) {
    $variantDescription[] = "Color: {$variant['color']}";
}
$variantInfo = !empty($variantDescription) 
    ? ' (' . implode(', ', $variantDescription) . ')' 
    : " (Variant #" . ($index + 1) . ")";

// Add to error with SKU value
$validator->errors()->add(
    "variants.{$index}.sku",
    __('catalogmanagement::product.sku_unique') . $variantInfo . " - SKU: {$variant['sku']}"
);
```

## Example Scenarios

### Scenario 1: Product with Size and Color
```json
{
    "variants": [
        {"size": "M", "color": "Black", "sku": "SHIRT-M-BLK"},
        {"size": "L", "color": "Black", "sku": "SHIRT-M-BLK"}  // Duplicate!
    ]
}
```

**Error**: `"SKU must be unique (Size: L, Color: Black) - SKU: SHIRT-M-BLK"`

### Scenario 2: Product with Only Size
```json
{
    "variants": [
        {"size": "Small", "sku": "PANTS-S"},
        {"size": "Medium", "sku": "PANTS-S"}  // Duplicate!
    ]
}
```

**Error**: `"SKU must be unique (Size: Medium) - SKU: PANTS-S"`

### Scenario 3: Product without Size/Color
```json
{
    "variants": [
        {"sku": "ITEM-001"},
        {"sku": "ITEM-001"}  // Duplicate!
    ]
}
```

**Error**: `"SKU must be unique (Variant #2) - SKU: ITEM-001"`

## Testing

Test the improved error messages by:
1. Creating/editing a product with duplicate SKUs
2. Verify error message includes variant details
3. Check both API and web interface display

## Files Modified

1. `Modules/CatalogManagement/app/Http/Requests/Product/UpdateProductRequest.php`
2. `Modules/CatalogManagement/app/Http/Requests/Product/StoreProductRequest.php`
3. `Modules/CatalogManagement/app/Http/Requests/Product/UpdateStockPricingRequest.php`

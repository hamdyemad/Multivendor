# Tags Support Added to Import/Export

## Overview
Added support for product tags in both Excel import and export functionality. Tags can now be included in the products sheet with separate columns for English and Arabic.

## Changes Made

### 1. Export - ProductsSheetExport.php

**Added Columns:**
- `tags_en` - Product tags in English
- `tags_ar` - Product tags in Arabic

**Position:** Added after `material_en` and `material_ar` columns, before `meta_title_en`

**Implementation:**
- Tags are exported from product translations
- Uses the same translation retrieval method as other translatable fields
- Tags are stored as comma-separated values

### 2. Import - ProductsSheetImport.php

**Added Fields:**
- `tags_{langCode}` - Handles tags for each language during import

**Implementation:**
- Added to `storeTranslations()` method for new products
- Added to `updateTranslations()` method for existing products
- Tags are stored in the translations table with `lang_key = 'tags'`

### 3. Bulk Upload Instructions

**Updated View:** `Modules/CatalogManagement/resources/views/product/bulk-upload.blade.php`

**Added Documentation:**
- `tags_en` column description
- `tags_ar` column description
- Instructions on format (comma-separated values)

### 4. Translation Keys

**English Translations:** `Modules/CatalogManagement/lang/en/product.php`
```php
'col_tags_en_desc' => 'Product tags in English (optional, comma-separated)',
'col_tags_source' => 'Comma-separated tags (e.g., "modern, luxury, premium")',
'col_tags_ar_desc' => 'Product tags in Arabic (optional, comma-separated)',
```

**Arabic Translations:** `Modules/CatalogManagement/lang/ar/product.php`
```php
'col_tags_en_desc' => 'وسوم المنتج بالإنجليزية (اختياري، مفصولة بفواصل)',
'col_tags_source' => 'وسوم مفصولة بفواصل (مثال: "حديث, فاخر, مميز")',
'col_tags_ar_desc' => 'وسوم المنتج بالعربية (اختياري، مفصولة بفواصل)',
```

## Excel Column Structure

### Products Sheet - Updated Order:
```
id
sku
[vendor_id] (admin only)
title_en
title_ar
description_en
description_ar
summary_en
summary_ar
features_en
features_ar
instructions_en
instructions_ar
extra_description_en
extra_description_ar
material_en
material_ar
tags_en          ← NEW
tags_ar          ← NEW
meta_title_en
meta_title_ar
meta_description_en
meta_description_ar
meta_keywords_en
meta_keywords_ar
department
main_category
sub_category
brand
have_varient
status
featured_product
max_per_order
```

## Usage Examples

### Export Example:
When exporting a product with tags:
```
tags_en: "modern, luxury, premium, waterproof"
tags_ar: "حديث, فاخر, مميز, مقاوم للماء"
```

### Import Example:
In your Excel file:
```
| tags_en                              | tags_ar                        |
|--------------------------------------|--------------------------------|
| modern, luxury, premium, waterproof  | حديث, فاخر, مميز, مقاوم للماء |
```

## Tag Format

- **Format:** Comma-separated values
- **Example (EN):** "modern, luxury, premium"
- **Example (AR):** "حديث, فاخر, مميز"
- **Optional:** Tags are not required fields
- **Storage:** Stored in translations table with `lang_key = 'tags'`

## How Tags Work

1. **Storage:** Tags are stored as translations in the `translations` table
2. **Retrieval:** Retrieved using `getTranslation('tags', $languageCode)`
3. **Display:** Can be split into array using `explode(',', $tags)`
4. **Update:** When importing, tags are updated or created via `updateOrCreate()`

## Files Modified

1. `Modules/CatalogManagement/app/Exports/ProductsSheetExport.php`
   - Added `tags_en` and `tags_ar` to headings
   - Added tags to map() method

2. `Modules/CatalogManagement/app/Imports/ProductsSheetImport.php`
   - Added `tags` to storeTranslations() method
   - Added `tags` to updateTranslations() method

3. `Modules/CatalogManagement/resources/views/product/bulk-upload.blade.php`
   - Added tags column documentation rows

4. `Modules/CatalogManagement/lang/en/product.php`
   - Added 3 translation keys for tags columns

5. `Modules/CatalogManagement/lang/ar/product.php`
   - Added 3 translation keys for tags columns (Arabic)

## Testing Recommendations

1. **Export Test:**
   - Export a product that has tags
   - Verify tags_en and tags_ar columns contain correct values

2. **Import Test - New Product:**
   - Create Excel with tags in both languages
   - Import and verify tags are stored correctly

3. **Import Test - Update Product:**
   - Export existing product
   - Modify tags in Excel
   - Import and verify tags are updated

4. **Import Test - Empty Tags:**
   - Import product without tags
   - Verify no errors occur

5. **Import Test - Special Characters:**
   - Test tags with special characters
   - Test tags with Arabic characters
   - Verify proper encoding

## Notes

- Tags are optional fields - products can be imported without tags
- Tags support both English and Arabic languages
- Tags are comma-separated for easy editing in Excel
- Tags follow the same translation pattern as other product fields
- Empty tag fields will not create translation entries

## Date Implemented
January 25, 2026

# Import Complete Sync - Excel as Source of Truth

## Overview
Implemented comprehensive sync functionality across all import sheets. When importing products via Excel, the system now treats the Excel file as the single source of truth and deletes any data that is NOT present in the Excel file.

## Affected Sheets

### 1. Variants Sheet
- Deletes variants not in Excel
- If product has 4 variants but Excel has 1, the 3 missing variants are deleted

### 2. Images Sheet  
- Deletes images (main and additional) not in Excel
- Also deletes physical image files from storage
- If product has 5 images but Excel has 2, the 3 missing images are deleted

### 3. Variant Stock Sheet
- Deletes stock entries not in Excel
- If variant has stock in 5 regions but Excel has 2, the 3 missing regions are deleted

## Behavior

### Before
- Excel import would only ADD or UPDATE data
- Existing data not in Excel would remain unchanged
- Result: Data accumulation over time

### After
- Excel import SYNCS all data
- Existing data not in Excel is DELETED
- Result: Excel file is the complete source of truth

## Implementation Details

### Files Modified
1. `Modules/CatalogManagement/app/Imports/VariantsSheetImport.php`
2. `Modules/CatalogManagement/app/Imports/ImagesSheetImport.php`
3. `Modules/CatalogManagement/app/Imports/VariantStockSheetImport.php`

### Common Pattern

Each sheet now follows this pattern:

1. **Track Processed Items**
   - Added tracking array (e.g., `$processedVariantsByProduct`)
   - Key: Parent ID (product_id or variant_id)
   - Value: Array of processed item IDs

2. **Record During Processing**
   - When creating or updating an item, add its ID to the tracking array
   - This happens for both new items and updated existing items

3. **Delete Unprocessed Items**
   - After all rows are processed, call cleanup method
   - Compare database items with processed items
   - Delete any items not in the processed list

### Specific Implementations

#### 1. VariantsSheetImport

**Tracking:**
```php
protected array $processedVariantsByProduct = [];
// Key: vendor_product_id
// Value: [variant_id, variant_id, ...]
```

**Cleanup Method:**
```php
private function deleteUnprocessedVariants(): void
{
    foreach ($this->processedVariantsByProduct as $vendorProductId => $processedVariantIds) {
        $existingVariants = VendorProductVariant::where('vendor_product_id', $vendorProductId)->get();
        foreach ($existingVariants as $variant) {
            if (!in_array($variant->id, $processedVariantIds)) {
                $this->logBulkActivity('deleted', $variant, $variant->toArray(), []);
                $variant->delete();
            }
        }
    }
}
```

**Features:**
- Logs deletion activity with reason
- Cascades to stock entries if configured

#### 2. ImagesSheetImport

**Tracking:**
```php
protected array $processedImagesByProduct = [];
// Key: product_id
// Value: [attachment_id, attachment_id, ...]
```

**Cleanup Method:**
```php
private function deleteUnprocessedImages(): void
{
    foreach ($this->processedImagesByProduct as $productId => $processedImageIds) {
        $existingImages = Attachment::where('attachable_id', $productId)
            ->where('attachable_type', Product::class)
            ->whereIn('type', ['main_image', 'additional_image'])
            ->get();
        foreach ($existingImages as $image) {
            if (!in_array($image->id, $processedImageIds)) {
                // Delete physical file
                if ($image->path && Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                // Delete database record
                $image->delete();
            }
        }
    }
}
```

**Features:**
- Deletes both main and additional images
- Removes physical files from storage
- Cleans up database records

#### 3. VariantStockSheetImport

**Tracking:**
```php
protected array $processedStockByVariant = [];
// Key: vendor_product_variant_id
// Value: [stock_id, stock_id, ...]
```

**Cleanup Method:**
```php
private function deleteUnprocessedStock(): void
{
    foreach ($this->processedStockByVariant as $variantId => $processedStockIds) {
        $existingStocks = VendorProductVariantStock::where('vendor_product_variant_id', $variantId)->get();
        foreach ($existingStocks as $stock) {
            if (!in_array($stock->id, $processedStockIds)) {
                $stock->delete();
            }
        }
    }
}
```

**Features:**
- Removes stock entries for regions not in Excel
- Maintains data integrity

## Use Cases

### Use Case 1: Reducing Variants
- Product has 4 color variants: Red, Blue, Green, Yellow
- Vendor decides to discontinue Green and Yellow
- Export product to Excel
- Remove Green and Yellow rows from variants sheet
- Import Excel file
- **Result:** Product now only has Red and Blue variants (Green and Yellow deleted)

### Use Case 2: Changing Product Images
- Product has 5 images
- Vendor wants to replace with 3 new images
- Export product to Excel
- Replace image URLs in images sheet (keep only 3)
- Import Excel file
- **Result:** Old 5 images deleted (including files), new 3 images added

### Use Case 3: Reducing Stock Regions
- Variant has stock in 5 regions: Cairo, Alex, Giza, Luxor, Aswan
- Vendor decides to only serve Cairo and Alex
- Export product to Excel
- Remove Giza, Luxor, Aswan rows from variant_stock sheet
- Import Excel file
- **Result:** Stock entries for Giza, Luxor, Aswan deleted

### Use Case 4: Complete Product Refresh
- Product has old variants, images, and stock
- Create new Excel with completely new data
- Import Excel file
- **Result:** All old data deleted, only new data exists

### Use Case 5: Updating Without Deletion
- Product has 3 variants, 2 images, stock in 3 regions
- Excel file contains the same 3 variants, 2 images, 3 regions (with updated values)
- Import Excel file
- **Result:** All data updated, nothing deleted

## Safety Features

1. **Vendor Ownership Check**
   - Vendors can only delete data from their own products
   - Admin can delete data from any product

2. **Activity Logging (Variants Only)**
   - Variant deletions are logged in activity_log table
   - Includes full variant data before deletion
   - Traceable back to bulk upload source

3. **Physical File Cleanup (Images)**
   - When images are deleted, physical files are removed from storage
   - Prevents orphaned files accumulating on disk

4. **Cascade Considerations**
   - Variant deletion may cascade to stock entries (depends on DB constraints)
   - Stock deletion is explicit and controlled
   - Image deletion removes both DB record and physical file

5. **Tracking Only Processed Products**
   - Only products/variants that appear in Excel are tracked
   - Products not in Excel are not affected
   - Prevents accidental deletion of unrelated data

## Testing Recommendations

### 1. Test Variants Sync
- Export a product with 4 variants
- Remove 2 variants from Excel
- Import and verify 2 variants are deleted

### 2. Test Images Sync
- Export a product with 5 images
- Remove 3 images from Excel
- Import and verify:
  - 3 images deleted from database
  - 3 physical files deleted from storage

### 3. Test Stock Sync
- Export a variant with stock in 5 regions
- Remove 3 regions from Excel
- Import and verify 3 stock entries are deleted

### 4. Test Vendor Permissions
- Vendor should only be able to delete their own data
- Admin should be able to delete any data

### 5. Test Activity Logs (Variants)
- Verify variant deletions are logged correctly
- Check that old variant data is preserved in logs

### 6. Test Complete Product Update
- Export product with variants, images, and stock
- Modify all sheets (add, update, remove items)
- Import and verify all changes applied correctly

### 7. Test No Changes Scenario
- Export product
- Import same file without modifications
- Verify nothing is deleted (all items match)

## Notes

- **This is a destructive operation** - Deleted data cannot be recovered except from activity logs (variants only)
- **Users should be aware** that the Excel file becomes the complete source of truth
- **Consider adding a warning message** in the UI about this sync behavior
- **Backup recommendation** - Users should keep backups of their Excel files
- **Physical files** - Image files are permanently deleted from storage
- **Stock bookings** - Existing stock bookings are not affected by stock entry deletion
- **Database constraints** - Ensure proper foreign key constraints are in place

## Important Warnings for Users

⚠️ **WARNING: The Excel import now SYNCS data, not just adds/updates!**

When you import an Excel file:
- Any variants NOT in the Excel will be DELETED
- Any images NOT in the Excel will be DELETED (including files)
- Any stock entries NOT in the Excel will be DELETED

**Always ensure your Excel file contains ALL the data you want to keep!**

## Related Files
- `Modules/CatalogManagement/app/Imports/VariantsSheetImport.php` - Variants sync
- `Modules/CatalogManagement/app/Imports/ImagesSheetImport.php` - Images sync
- `Modules/CatalogManagement/app/Imports/VariantStockSheetImport.php` - Stock sync
- `Modules/CatalogManagement/app/Imports/ProductsImport.php` - Import orchestrator
- `app/Models/ActivityLog.php` - Activity logging model

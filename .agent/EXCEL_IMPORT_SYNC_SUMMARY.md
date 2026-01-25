# Excel Import Sync - Quick Reference

## ⚠️ CRITICAL CHANGE

**The Excel import now SYNCS data instead of just adding/updating!**

## What This Means

When you import an Excel file for an existing product:

| Sheet | What Gets Deleted |
|-------|------------------|
| **variants** | Any variants in database NOT in Excel |
| **images** | Any images in database NOT in Excel (+ physical files) |
| **variant_stock** | Any stock entries in database NOT in Excel |

## Example Scenarios

### Scenario 1: Reducing Variants
```
Database: Product has 4 variants (Red, Blue, Green, Yellow)
Excel:    Product has 2 variants (Red, Blue)
Result:   Green and Yellow variants DELETED
```

### Scenario 2: Changing Images
```
Database: Product has 5 images
Excel:    Product has 3 different images
Result:   Old 5 images DELETED (files + DB), new 3 images ADDED
```

### Scenario 3: Reducing Stock Regions
```
Database: Variant has stock in 5 regions
Excel:    Variant has stock in 2 regions
Result:   3 stock entries DELETED
```

## How It Works

For each sheet, the system:

1. **Tracks** what's in the Excel file during import
2. **Compares** with what's in the database
3. **Deletes** anything in database but NOT in Excel

## Safety Rules

✅ **Vendors** can only delete their own product data
✅ **Admins** can delete any product data
✅ **Variant deletions** are logged in activity_log
✅ **Image files** are removed from storage
✅ **Only processed products** are affected (products not in Excel are safe)

## Best Practices

1. **Always export before import** - Get current data first
2. **Keep backups** - Save your Excel files
3. **Review before import** - Check what you're removing
4. **Test with one product** - Before bulk operations
5. **Use export-modify-import workflow** - Safest approach

## Workflow Recommendation

```
1. Export product(s) to Excel
2. Modify Excel file (add/update/remove rows)
3. Import Excel file
4. System syncs: adds new, updates existing, deletes missing
```

## What's NOT Affected

- Products not in the Excel file
- Product basic info (title, description, etc.)
- Vendor product settings (max_per_order, status, etc.)
- Stock bookings (orders)
- Other products' data

## Recovery

- **Variants**: Can be recovered from activity_log table
- **Images**: Physical files are permanently deleted
- **Stock**: No recovery (just re-add via import)

## Technical Details

### Modified Files
1. `Modules/CatalogManagement/app/Imports/VariantsSheetImport.php`
2. `Modules/CatalogManagement/app/Imports/ImagesSheetImport.php`
3. `Modules/CatalogManagement/app/Imports/VariantStockSheetImport.php`

### Key Methods Added
- `deleteUnprocessedVariants()` - Removes variants not in Excel
- `deleteUnprocessedImages()` - Removes images not in Excel
- `deleteUnprocessedStock()` - Removes stock entries not in Excel

### Tracking Arrays
- `$processedVariantsByProduct` - Tracks variants per product
- `$processedImagesByProduct` - Tracks images per product
- `$processedStockByVariant` - Tracks stock per variant

## User Warning Message (Recommended)

Consider adding this warning to the bulk upload page:

```
⚠️ IMPORTANT: Excel import now SYNCS your data!

When you import an Excel file:
• Variants NOT in Excel will be DELETED
• Images NOT in Excel will be DELETED
• Stock entries NOT in Excel will be DELETED

Make sure your Excel file contains ALL data you want to keep!
We recommend exporting first, then modifying, then importing.
```

## Date Implemented
January 25, 2026

## Related Documentation
See `.agent/IMPORT_VARIANT_SYNC_COMPLETE.md` for detailed technical documentation.

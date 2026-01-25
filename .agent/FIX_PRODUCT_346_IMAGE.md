# Fix Product 346 Incorrect Image

## Problem
Product ID 346 has an incorrect main image showing a person's photo instead of a product image.

## Solutions

### Solution 1: Fix via Admin Panel (Easiest & Recommended)

1. **Navigate to the product edit page:**
   ```
   http://127.0.0.1:8000/en/eg/admin/products/346/edit
   ```

2. **Scroll to the "Main Image" section (Step 2)**

3. **Remove the current incorrect image:**
   - Click the remove/delete button on the current image

4. **Upload the correct product image:**
   - Click "Choose File" or drag and drop the correct product image
   - Make sure it's an actual product photo, not a person

5. **Save the product:**
   - Click "Update Product" button at the bottom

### Solution 2: Fix via PHP Script

1. **Run the provided PHP script:**
   ```bash
   php fix_product_346_image.php
   ```

2. **The script will:**
   - Find the product
   - Show current image details
   - Ask for confirmation to delete
   - Delete both the physical file and database record

3. **After deletion:**
   - Upload a new image via the admin panel

### Solution 3: Fix via SQL (Advanced)

1. **Run the SQL queries to inspect:**
   ```bash
   mysql -u your_user -p your_database < fix_product_346_image.sql
   ```

2. **Review the output to confirm the image**

3. **Uncomment the DELETE statement in the SQL file**

4. **Run again to delete the incorrect image**

5. **Upload new image via admin panel**

## Prevention

To prevent incorrect images in the future:

1. **Validate image uploads:**
   - Ensure images are product photos, not personal photos
   - Use descriptive filenames

2. **Review before saving:**
   - Always preview the image before saving the product

3. **Use bulk upload carefully:**
   - When using Excel import, verify image URLs are correct
   - Test with one product before bulk importing

## Image Requirements

For product images:
- **Type:** Product photos only (no people, unless modeling the product)
- **Format:** JPG, PNG, WEBP
- **Size:** Recommended 800x800px or larger
- **Quality:** High resolution, clear, well-lit
- **Background:** Preferably white or neutral

## Files Created

1. `fix_product_346_image.php` - PHP script to remove incorrect image
2. `fix_product_346_image.sql` - SQL queries to inspect and fix
3. `.agent/FIX_PRODUCT_346_IMAGE.md` - This guide

## Quick Fix Command

If you just want to delete the image quickly via database:

```sql
-- Find the product's base product ID
SELECT product_id FROM vendor_products WHERE id = 346;

-- Delete the main image (replace PRODUCT_ID with the result above)
DELETE FROM attachments 
WHERE attachable_type = 'Modules\\CatalogManagement\\app\\Models\\Product'
  AND type = 'main_image'
  AND attachable_id = PRODUCT_ID;
```

Then upload a new image via the admin panel.

## Notes

- The incorrect image appears to be a test/demo image
- Make sure to delete the physical file from storage as well
- After fixing, verify the product page displays correctly
- Consider adding image validation rules if this happens frequently

## Date
January 25, 2026

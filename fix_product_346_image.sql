-- SQL Script to fix Product 346 incorrect image
-- This will help you identify and remove the incorrect image

-- Step 1: Find the product and its base product
SELECT 
    vp.id as vendor_product_id,
    vp.sku,
    vp.product_id,
    p.id as base_product_id
FROM vendor_products vp
LEFT JOIN products p ON vp.product_id = p.id
WHERE vp.id = 346;

-- Step 2: Find the main image attachment
SELECT 
    a.id,
    a.attachable_id,
    a.attachable_type,
    a.type,
    a.path,
    a.created_at
FROM attachments a
WHERE a.attachable_type = 'Modules\\CatalogManagement\\app\\Models\\Product'
  AND a.type = 'main_image'
  AND a.attachable_id = (
      SELECT product_id FROM vendor_products WHERE id = 346
  );

-- Step 3: Find all images for this product
SELECT 
    a.id,
    a.type,
    a.path,
    a.created_at
FROM attachments a
WHERE a.attachable_type = 'Modules\\CatalogManagement\\app\\Models\\Product'
  AND a.attachable_id = (
      SELECT product_id FROM vendor_products WHERE id = 346
  )
ORDER BY a.type, a.created_at;

-- Step 4: DELETE the incorrect main image (UNCOMMENT AFTER VERIFYING)
-- WARNING: This will permanently delete the image record
-- Make sure to backup first!

-- DELETE FROM attachments 
-- WHERE attachable_type = 'Modules\\CatalogManagement\\app\\Models\\Product'
--   AND type = 'main_image'
--   AND attachable_id = (
--       SELECT product_id FROM vendor_products WHERE id = 346
--   );

-- Step 5: After deleting, you can upload a new image via the admin panel
-- Or insert a new image record if you have the correct image path:

-- INSERT INTO attachments (attachable_id, attachable_type, type, path, created_at, updated_at)
-- VALUES (
--     (SELECT product_id FROM vendor_products WHERE id = 346),
--     'Modules\\CatalogManagement\\app\\Models\\Product',
--     'main_image',
--     'products/YOUR_IMAGE_PATH.jpg',  -- Replace with actual path
--     NOW(),
--     NOW()
-- );

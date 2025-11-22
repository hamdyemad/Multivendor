# Status Column Migration Summary

## Overview
Removed `status` and `status_message` columns from the `products` table and kept them only in the `vendor_products` table. This change reflects the business logic that product approval status is vendor-specific, not product-specific.

---

## Changes Made

### 1. Database Migration ✅
**File**: `database/migrations/2025_11_22_165000_remove_status_from_products_table.php`

- **Removed columns from `products` table:**
  - `status` (enum: pending, approved, requested, rejected)
  - `status_message` (text, nullable)

- **Status remains in `vendor_products` table:**
  - `status` (enum: pending, approved, rejected) - Already exists
  - `rejection_reason` (text, nullable) - Already exists

**To run the migration:**
```bash
php artisan migrate
```

---

### 2. Product Model Updates ✅
**File**: `Modules/CatalogManagement/app/Models/Product.php`

**Removed:**
- `'status' => 'string'` from `$casts` array
- Status filter from `scopeFilter()` method

**Kept:**
- All other model functionality intact
- Relationships with vendor_products remain

---

### 3. ProductRepository Updates ✅
**File**: `Modules/CatalogManagement/app/Repositories/ProductRepository.php`

#### `createProduct()` Method
**Before:**
```php
$status = $isVendorCreated ? 'pending' : 'approved';
$product = Product::create([
    'status' => $status,
    // ... other fields
]);
```

**After:**
```php
$product = Product::create([
    // No status field
    // ... other fields
]);
```

#### `updateProduct()` Method
**Before:**
```php
if (in_array($userType, UserType::vendorIds())) {
    $status = 'pending';
} else {
    $status = $data['status'] ?? $product->status;
}
$product->update([
    'status' => $status,
    // ... other fields
]);
```

**After:**
```php
$product->update([
    // No status field
    // ... other fields
]);
```

#### `handleProductVariants()` Method
**Added status handling for VendorProduct:**
```php
// Determine status based on user role
$currentUser = Auth::user();
$userType = $currentUser->user_type_id;

// Get or create VendorProduct
$vendorProduct = VendorProduct::firstOrCreate(
    ['vendor_id' => $vendorId, 'product_id' => $product->id],
    [
        // ... other fields
        'status' => in_array($userType, UserType::vendorIds()) ? 'pending' : 'approved',
    ]
);

// Determine if status should change (only if vendor is editing)
if (in_array($userType, UserType::vendorIds())) {
    $status = 'pending'; // Vendor editing: reset to pending
} else {
    $status = $data['status'] ?? $vendorProduct->status; // Admin: keep or update
}

// Update VendorProduct
$vendorProduct->update([
    // ... other fields
    'status' => $status,
]);
```

---

## Business Logic

### Product Creation
- **Vendor creates product**: `vendor_products.status = 'pending'`
- **Admin creates product**: `vendor_products.status = 'approved'`

### Product Update
- **Vendor edits product**: `vendor_products.status = 'pending'` (requires re-approval)
- **Admin edits product**: `vendor_products.status` remains unchanged (or updated if admin changes it)

### Product Approval Workflow
1. Vendor creates/edits product → VendorProduct status = 'pending'
2. Admin reviews product
3. Admin approves → VendorProduct status = 'approved'
4. Admin rejects → VendorProduct status = 'rejected' + rejection_reason

---

## Important Notes

### ⚠️ Controllers Need Update
The following controllers still reference `product->status` and need to be updated:

1. **ProductBankController.php**
   - Line 72: `$query->where('status', $request->status);`
   - Line 99: `'status' => $product->status,`
   - Lines 363-365: Status counts
   - Line 394: `$product->update(['status' => 'approved'])`

2. **VendorProductController.php**
   - Line 482: `Product::where('status', 'approved')` - This should filter by vendor_products.status

3. **ProductController.php**
   - Already handles vendor_products.status correctly ✅

### 📝 TODO: Update Controllers
After running the migration, update these controllers to:
- Query `vendor_products.status` instead of `products.status`
- Use joins or whereHas clauses to filter by vendor product status

Example:
```php
// OLD
$query->where('status', $request->status);

// NEW
$query->whereHas('vendorProducts', function($q) use ($request) {
    $q->where('status', $request->status);
});
```

---

## Testing Checklist

- [ ] Run migration: `php artisan migrate`
- [ ] Test vendor product creation (should be pending)
- [ ] Test admin product creation (should be approved)
- [ ] Test vendor product edit (should reset to pending)
- [ ] Test admin product edit (should keep status)
- [ ] Test product approval workflow
- [ ] Test product rejection workflow
- [ ] Update ProductBankController status queries
- [ ] Update VendorProductController status queries
- [ ] Test product bank filtering by status
- [ ] Test vendor product requests filtering

---

## Database Structure

### products table
```sql
- id
- slug
- is_active
- configuration_type
- vendor_id
- brand_id
- department_id
- category_id
- sub_category_id
- created_by_user_id
- timestamps
- soft_deletes
```

### vendor_products table
```sql
- id
- vendor_id
- product_id
- tax_id
- sku
- points
- max_per_order
- offer_date_view
- is_active
- is_featured
- status (enum: pending, approved, rejected) ✅
- rejection_reason (text, nullable) ✅
- timestamps
- soft_deletes
```

---

## Status: PARTIALLY COMPLETE ⚠️

✅ **Completed:**
- Migration created
- Product model updated
- ProductRepository updated
- Status logic moved to vendor_products

⚠️ **Pending:**
- Run migration
- Update ProductBankController
- Update VendorProductController
- Test all workflows

---

## Next Steps

1. **Run the migration:**
   ```bash
   php artisan migrate
   ```

2. **Update ProductBankController** to query vendor_products.status

3. **Update VendorProductController** to query vendor_products.status

4. **Test the complete workflow** from vendor creation to admin approval

5. **Clear caches:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   ```

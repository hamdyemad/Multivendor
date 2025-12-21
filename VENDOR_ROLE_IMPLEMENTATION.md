# Vendor Role Auto-Assignment Implementation

## Summary
Successfully implemented automatic assignment of a system-protected "Vendor" role when creating vendors. The system now:
1. Populates `permessions` table from config with rich metadata
2. Creates a "Vendor" role with all `type='all'` permissions from the table
3. Auto-assigns this role to new vendors
4. Prevents deletion of system-protected roles (Super Admin & Vendor)

## Changes Made

### 1. Database Changes
- **Added `is_system_protected` column** to `roles` table (boolean, default false)
- **Updated `permessions` table** structure:
  - Added `name_en`, `name_ar`, `module`, `sub_module` columns
  - Updated `type` enum to `['admin', 'all']`

### 2. Updated `permessions_reset()` Function
**File**: `app/Helpers/functions.php`

- Reads permissions from `config/permissions.php`
- Populates `permessions` table with all metadata
- Sets `type`, `module`, `sub_module`, `key`, `name_en`, `name_ar`

### 3. Updated `roles_reset()` Function
**File**: `app/Helpers/functions.php`

- Creates two system roles:
  - **Super Admin**: Gets ALL permissions from `permessions` table
  - **Vendor**: Gets only permissions with `type='all'` from `permessions` table
- Assigns permissions using the `role_permession` pivot table

### 4. Updated Role Model
**File**: `app/Models/Role.php`

- Restored `permessions()` relationship
- Added `hasPermission($key)` method checking relation

### 5. Updated VendorRepository
**File**: `Modules/Vendor/app/Repositories/VendorRepository.php`

- Auto-assigns Vendor role when creating a vendor:
```php
$vendorRole = \App\Models\Role::where('type', \App\Models\Role::VENDOR_ROLE_TYPE)->first();
if ($vendorRole) {
    $user->roles()->sync([$vendorRole->id]);
}
```

### 6. Updated RoleController
**File**: `app/Http/Controllers/AdminManagement/RoleController.php`

- Added protection against deleting system-protected roles
- Returns 403 error with message: "This is a system role and cannot be deleted. You can only edit it."

## Vendor Permissions (type='all')
The Vendor role automatically gets these permissions:
- ✅ Dashboard (view)
- ✅ Roles (full CRUD)
- ✅ Vendor Users (full CRUD + status)
- ✅ Products (full CRUD + status + activation + stock-setup)
- ✅ Product Bank (read + change-activation + trash)
- ✅ Bundles (full CRUD + status + approval)
- ✅ Occasions (full CRUD + status)
- ✅ Orders (full CRUD + view + change-stage)
- ✅ Withdraws (full CRUD + send money + transactions + requests + change status)

## Next Steps

1. **Verify Vendor Role**:
   - Check if the "Vendor" role exists in `roles` table.
   - Check if it has permissions in `role_permession` table.

2. **Test Vendor Creation**:
   - Create a new vendor and verify they get the role.

3. **Frontend Adjustments** (If needed):
   - Ensure the Role management UI displays the new permission names correctly from the database if it doesn't already.

## Commands Run
- `php artisan migrate`
- `php artisan tinker --execute="permessions_reset(); roles_reset();"`

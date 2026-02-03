# Bnaia Vendor Logo - Storage Path Fix

## Issue
Logo was not accessible via URL because the path was pointing to `assets/img/logo.png` instead of the storage folder.

**Error**: `http://127.0.0.1:8000/storage/assets/img/logo.png` returned 404

## Solution
Copy the logo from `public/assets/img/logo.png` to `storage/app/public/vendor-images/logo.png` and save the path as `vendor-images/logo.png`.

**Result**: Logo is now accessible at `http://127.0.0.1:8000/storage/vendor-images/logo.png`

---

## Changes Made

### 1. routes/admin.php ✅

**New Vendor Creation** (lines 145-175):
```php
// Attach logo if exists
$logoPath = public_path('assets/img/logo.png');
if (file_exists($logoPath)) {
    // Delete existing logo attachment if any
    \App\Models\Attachment::where('attachable_type', \Modules\Vendor\app\Models\Vendor::class)
        ->where('attachable_id', $bnaiaVendor->id)
        ->where('type', 'logo')
        ->delete();
    
    // Copy logo to storage/app/public/vendor-images/
    $storagePath = 'vendor-images/logo.png';
    $destinationPath = storage_path('app/public/' . $storagePath);
    
    // Create directory if it doesn't exist
    $directory = dirname($destinationPath);
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }
    
    // Copy the logo file
    copy($logoPath, $destinationPath);
    
    $attachment = new \App\Models\Attachment();
    $attachment->attachable_type = \Modules\Vendor\app\Models\Vendor::class;
    $attachment->attachable_id = $bnaiaVendor->id;
    $attachment->type = 'logo';
    $attachment->path = $storagePath;  // ← 'vendor-images/logo.png'
    $attachment->save();
    
    echo "  ✓ Attached logo to Bnaia vendor\n";
}
```

**Existing Vendor Update** (lines 180-210):
```php
// Update logo if exists
$logoPath = public_path('assets/img/logo.png');
if (file_exists($logoPath)) {
    // Delete existing logo attachment if any
    \App\Models\Attachment::where('attachable_type', \Modules\Vendor\app\Models\Vendor::class)
        ->where('attachable_id', $bnaiaVendor->id)
        ->where('type', 'logo')
        ->delete();
    
    // Copy logo to storage/app/public/vendor-images/
    $storagePath = 'vendor-images/logo.png';
    $destinationPath = storage_path('app/public/' . $storagePath);
    
    // Create directory if it doesn't exist
    $directory = dirname($destinationPath);
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }
    
    // Copy the logo file
    copy($logoPath, $destinationPath);
    
    $attachment = new \App\Models\Attachment();
    $attachment->attachable_type = \Modules\Vendor\app\Models\Vendor::class;
    $attachment->attachable_id = $bnaiaVendor->id;
    $attachment->type = 'logo';
    $attachment->path = $storagePath;  // ← 'vendor-images/logo.png'
    $attachment->save();
    
    echo "  ✓ Updated logo for Bnaia vendor\n";
}
```

---

### 2. app/Http/Controllers/Api/InjectDataController.php ✅

**New Vendor Creation** (lines 1077-1100):
```php
// Attach logo from public/assets/img/logo.png
$logoPath = public_path('assets/img/logo.png');
if (file_exists($logoPath)) {
    // Copy logo to storage/app/public/vendor-images/
    $storagePath = 'vendor-images/logo.png';
    $destinationPath = storage_path('app/public/' . $storagePath);
    
    // Create directory if it doesn't exist
    $directory = dirname($destinationPath);
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }
    
    // Copy the logo file
    copy($logoPath, $destinationPath);
    
    $attachment = new Attachment();
    $attachment->attachable_type = Vendor::class;
    $attachment->attachable_id = $bnaiaVendor->id;
    $attachment->type = 'logo';
    $attachment->path = $storagePath;  // ← 'vendor-images/logo.png'
    $attachment->save();
    
    Log::info("Attached logo to Bnaia vendor");
}
```

**Existing Vendor Update** (lines 1115-1145):
```php
// Update logo if exists
$logoPath = public_path('assets/img/logo.png');
if (file_exists($logoPath)) {
    // Delete existing logo attachment if any
    Attachment::where('attachable_type', Vendor::class)
        ->where('attachable_id', $bnaiaVendor->id)
        ->where('type', 'logo')
        ->delete();
    
    // Copy logo to storage/app/public/vendor-images/
    $storagePath = 'vendor-images/logo.png';
    $destinationPath = storage_path('app/public/' . $storagePath);
    
    // Create directory if it doesn't exist
    $directory = dirname($destinationPath);
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }
    
    // Copy the logo file
    copy($logoPath, $destinationPath);
    
    $attachment = new Attachment();
    $attachment->attachable_type = Vendor::class;
    $attachment->attachable_id = $bnaiaVendor->id;
    $attachment->type = 'logo';
    $attachment->path = $storagePath;  // ← 'vendor-images/logo.png'
    $attachment->save();
    
    Log::info("Updated logo for Bnaia vendor");
}
```

---

## File Structure

### Before
```
public/
  assets/
    img/
      logo.png  ← Source file

attachments table:
  path: 'assets/img/logo.png'  ← Not accessible via /storage/
```

### After
```
public/
  assets/
    img/
      logo.png  ← Source file (kept)

storage/
  app/
    public/
      vendor-images/
        logo.png  ← Copied file

attachments table:
  path: 'vendor-images/logo.png'  ← Accessible via /storage/
```

---

## URL Access

### Before (404 Error)
```
http://127.0.0.1:8000/storage/assets/img/logo.png  ← 404 Not Found
```

### After (Working)
```
http://127.0.0.1:8000/storage/vendor-images/logo.png  ← ✅ Works!
```

---

## How It Works

1. **Source**: Logo exists at `public/assets/img/logo.png`
2. **Copy**: File is copied to `storage/app/public/vendor-images/logo.png`
3. **Database**: Path saved as `vendor-images/logo.png`
4. **Access**: Laravel serves it via `/storage/vendor-images/logo.png`

The `/storage` URL is mapped to `storage/app/public/` by Laravel's storage link.

---

## Testing

### 1. Run Seeder
```bash
GET /admin/seeder
```

Expected output:
```
🏢 Creating/Updating Bnaia Vendor...
  ✓ Created Bnaia user
  ✓ Attached logo to Bnaia vendor
  ✓ Assigned X departments to Bnaia vendor
  ✓ Created Bnaia vendor (ID: 1)
```

### 2. Check File Exists
```bash
# Check if file was copied
ls storage/app/public/vendor-images/logo.png
```

### 3. Check Database
```sql
SELECT * FROM attachments 
WHERE attachable_type = 'Modules\\Vendor\\app\\Models\\Vendor' 
AND type = 'logo';

-- Expected path: 'vendor-images/logo.png'
```

### 4. Test URL Access
```
http://127.0.0.1:8000/storage/vendor-images/logo.png
```

Should display the logo image.

---

## Important Notes

### Storage Link
Make sure the storage link is created:
```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

### Directory Creation
The code automatically creates the `vendor-images` directory if it doesn't exist:
```php
$directory = dirname($destinationPath);
if (!file_exists($directory)) {
    mkdir($directory, 0755, true);
}
```

### File Overwrite
The logo file `logo.png` will be overwritten each time the seeder runs, ensuring it's always up to date.

---

## Summary

✅ Logo copied from `public/assets/img/logo.png` to `storage/app/public/vendor-images/logo.png`
✅ Path saved as `vendor-images/logo.png` in database
✅ Logo accessible at `http://127.0.0.1:8000/storage/vendor-images/logo.png`
✅ Directory created automatically if it doesn't exist
✅ Works in both seeder route and brand injection API
✅ Updates existing vendors if they already exist

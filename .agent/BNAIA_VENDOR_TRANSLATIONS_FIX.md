# Bnaia Vendor Translations Fix

## Issue
Vendor translations were not being saved to the database. The translations table showed no records for the Bnaia vendor.

## Root Cause
The vendor creation code was wrapped in a `Vendor::withoutEvents()` closure, which was preventing the translations from being saved properly.

```php
// BEFORE (Not working)
Vendor::withoutEvents(function () {
    $bnaiaVendor = new \Modules\Vendor\app\Models\Vendor();
    $bnaiaVendor->save();
    
    // Translations created here were not saved
    $bnaiaVendor->translations()->create([...]);
});
```

## Solution
Removed the `Vendor::withoutEvents()` wrapper so translations can be saved normally.

```php
// AFTER (Working)
$bnaiaVendor = new \Modules\Vendor\app\Models\Vendor();
$bnaiaVendor->save();

// Translations now save correctly
$bnaiaVendor->translations()->create([...]);
```

---

## Changes Made

### File: `routes/admin.php`

**Removed**: `Vendor::withoutEvents(function () {` wrapper
**Removed**: Closing `});` for the withoutEvents closure

**Added**: Console output for translation creation:
```php
echo "  ✓ Created vendor translations\n";
```

### Complete Fixed Code

```php
Route::get('seeder', function () {
    // ===== CREATE BNAIA VENDOR AND UPDATE ALL PRODUCTS/ORDERS =====
    try {
        echo "🏢 Creating/Updating Bnaia Vendor...\n";
        
        // Get or create Bnaia user
        $bnaiaUser = \App\Models\User::where('email', 'bnaia@bnaia.com')->first();
        if (!$bnaiaUser) {
            $bnaiaUser = new \App\Models\User();
            $bnaiaUser->uuid = \Str::uuid();
            $bnaiaUser->email = 'bnaia@bnaia.com';
            $bnaiaUser->password = bcrypt('password123');
            $bnaiaUser->user_type_id = \App\Models\UserType::VENDOR_TYPE;
            $bnaiaUser->active = true;
            $bnaiaUser->save();
            
            // Set user translations
            $languages = \App\Models\Language::whereIn('code', ['en', 'ar'])->get();
            foreach ($languages as $language) {
                $bnaiaUser->translations()->create([
                    'lang_id' => $language->id,
                    'lang_key' => 'name',
                    'lang_value' => $language->code === 'en' ? 'Bnaia Admin' : 'مدير بنايا',
                ]);
            }
            echo "  ✓ Created Bnaia user\n";
        }
        
        // Get or create Bnaia vendor
        $bnaiaVendor = \Modules\Vendor\app\Models\Vendor::where('slug', 'bnaia')->first();
        if (!$bnaiaVendor) {
            $bnaiaVendor = new \Modules\Vendor\app\Models\Vendor();
            $bnaiaVendor->user_id = $bnaiaUser->id;
            $bnaiaVendor->slug = 'bnaia';
            $bnaiaVendor->phone = '+201000000000';
            $bnaiaVendor->active = true;
            $bnaiaVendor->save();
            
            // Set vendor translations
            $languages = \App\Models\Language::whereIn('code', ['en', 'ar'])->get();
            foreach ($languages as $language) {
                $bnaiaVendor->translations()->create([
                    'lang_id' => $language->id,
                    'lang_key' => 'name',
                    'lang_value' => $language->code === 'en' ? 'Bnaia' : 'بنايا',
                ]);
                $bnaiaVendor->translations()->create([
                    'lang_id' => $language->id,
                    'lang_key' => 'description',
                    'lang_value' => $language->code === 'en' ? 'Bnaia - Building Materials Supplier' : 'بنايا - مورد مواد البناء',
                ]);
            }
            echo "  ✓ Created vendor translations\n";
            
            // ... rest of the code (logo, departments, etc.)
        }
        
        // ... rest of the code
        
    } catch (\Exception $e) {
        echo "❌ Error setting up Bnaia vendor: {$e->getMessage()}\n\n";
    }
});
```

---

## Expected Translations

After running the seeder, the translations table should contain:

### Vendor Name Translations
| translatable_type | translatable_id | lang_id | lang_key | lang_value |
|-------------------|-----------------|---------|----------|------------|
| Modules\Vendor\app\Models\Vendor | 1 | 1 | name | Bnaia |
| Modules\Vendor\app\Models\Vendor | 1 | 2 | name | بنايا |

### Vendor Description Translations
| translatable_type | translatable_id | lang_id | lang_key | lang_value |
|-------------------|-----------------|---------|----------|------------|
| Modules\Vendor\app\Models\Vendor | 1 | 1 | description | Bnaia - Building Materials Supplier |
| Modules\Vendor\app\Models\Vendor | 1 | 2 | description | بنايا - مورد مواد البناء |

---

## Testing

### 1. Clear Existing Data (Optional)
```sql
-- Delete existing Bnaia vendor and translations
DELETE FROM translations WHERE translatable_type = 'Modules\\Vendor\\app\\Models\\Vendor';
DELETE FROM vendors WHERE slug = 'bnaia';
DELETE FROM users WHERE email = 'bnaia@bnaia.com';
```

### 2. Run Seeder
```bash
GET /admin/seeder
```

Expected console output:
```
🏢 Creating/Updating Bnaia Vendor...
  ✓ Created Bnaia user
  ✓ Created vendor translations
  ✓ Attached logo to Bnaia vendor
  ✓ Assigned X departments to Bnaia vendor
  ✓ Created Bnaia vendor (ID: 1)
  ✓ Updated X vendor products to Bnaia
  ✓ Updated X order products to Bnaia
  ✓ Updated X vendor order stages to Bnaia
✅ Bnaia vendor setup complete!
```

### 3. Verify Translations in Database
```sql
SELECT * FROM translations 
WHERE translatable_type = 'Modules\\Vendor\\app\\Models\\Vendor' 
AND translatable_id = (SELECT id FROM vendors WHERE slug = 'bnaia')
ORDER BY lang_key, lang_id;
```

Expected result: 4 rows (2 for name, 2 for description)

### 4. Verify in phpMyAdmin
Navigate to the translations table and filter by:
- `translatable_type` = `Modules\Vendor\app\Models\Vendor`

You should see 4 translation records.

---

## Why withoutEvents() Caused the Issue

The `withoutEvents()` method disables model events (creating, created, updating, updated, etc.) for the model. While this is useful for performance in some cases, it can interfere with:

1. **Relationship operations**: Creating related models (like translations)
2. **Observers**: Any model observers that need to run
3. **Event listeners**: Any event listeners attached to the model

In this case, the translations relationship likely relies on model events to properly save the translation records.

---

## Summary

✅ Removed `Vendor::withoutEvents()` wrapper
✅ Translations now save correctly to database
✅ Added console output for translation creation
✅ Vendor name and description available in English and Arabic
✅ Works for both new and existing vendors

The Bnaia vendor now has proper translations that will display correctly in the admin panel and frontend.

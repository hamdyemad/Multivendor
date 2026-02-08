# Request Quotation Number Implementation - Complete

## Overview
Added automatic quotation number generation for Request Quotations, similar to the Order number system.

## Changes Made

### 1. Database Migration
**File:** `database/migrations/2026_02_08_120000_add_quotation_number_to_request_quotations_table.php`
- Added `quotation_number` column (string, unique, nullable)
- Auto-generated quotation numbers for existing records in format: `RQ-000001`, `RQ-000002`, etc.

### 2. Model Updates
**File:** `Modules/Order/app/Models/RequestQuotation.php`
- Added `quotation_number` to fillable array
- Added `boot()` method with `creating` event to auto-generate quotation numbers
- Added `generateQuotationNumber()` static method:
  - Uses database locking to prevent race conditions
  - Format: `RQ-XXXXXX` (e.g., RQ-000001, RQ-000002)
  - Includes retry mechanism (5 attempts)
  - Fallback to timestamp-based number if all retries fail

### 3. Controller Updates
**File:** `Modules/Order/app/Http/Controllers/RequestQuotationController.php`
- Added `quotation_number` column to DataTable
- Displays as bold primary text
- Added to rawColumns for HTML rendering
- Included in view modal data array

### 4. View Updates
**File:** `Modules/Order/resources/views/request-quotations/index.blade.php`
- Added quotation number column to table header (after # column)
- Updated DataTable columns configuration to include quotation_number
- Made quotation_number searchable
- Updated order index to column 5 (created_at)
- Added quotation number to view modal (displayed at top with status)
- Reorganized modal layout with customer information section

### 5. Translation Updates
**Files:**
- `Modules/Order/lang/en/request-quotation.php`
- `Modules/Order/lang/ar/request-quotation.php`

Added translations:
- English: `'quotation_number' => 'Quotation Number'`
- Arabic: `'quotation_number' => 'رقم طلب العرض'`

## Features

### Quotation Number Format
- **Prefix:** RQ- (Request Quotation)
- **Length:** 6 digits with leading zeros
- **Examples:** RQ-000001, RQ-000023, RQ-001234

### Auto-Generation
- Numbers are generated automatically when creating new request quotations
- Uses database locking to prevent duplicate numbers in concurrent requests
- Sequential numbering based on last quotation number

### Display Locations
1. **DataTable:** Second column (after #), searchable
2. **View Modal:** Top section with status, bold primary color
3. **Database:** Unique indexed column

## Testing Checklist
- [x] Migration runs successfully
- [x] Existing records get quotation numbers
- [x] New quotations auto-generate numbers
- [x] Numbers are unique and sequential
- [x] DataTable displays quotation numbers
- [x] View modal shows quotation number
- [x] Translations work in both English and Arabic
- [x] Search by quotation number works

## Database Schema
```sql
ALTER TABLE request_quotations 
ADD COLUMN quotation_number VARCHAR(255) NULL UNIQUE AFTER id;
```

## Usage Example
```php
// Creating a new request quotation
$quotation = RequestQuotation::create([
    'customer_id' => 1,
    'notes' => 'Test quotation',
    // quotation_number is auto-generated
]);

echo $quotation->quotation_number; // Output: RQ-000001
```

## Status
✅ **COMPLETE** - All features implemented and tested

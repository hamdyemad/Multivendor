# Data Injection Artisan Commands

## Overview

Two powerful Artisan commands have been created to inject data from the external API in the background, perfect for large imports that would timeout in the browser.

---

## Commands

### 1. `inject:data` - Single Type Import

Import a specific data type.

#### Syntax
```bash
php artisan inject:data {type} [options]
```

#### Arguments
- `type` - Type of data to inject (required)

#### Options
- `--truncate` - Truncate existing data before injection
- `--limit-pages=N` - Limit number of pages to process
- `--start-page=N` - Starting page number (default: 1)

#### Examples

```bash
# Import departments
php artisan inject:data departments --truncate

# Import main categories only
php artisan inject:data main_categories --truncate

# Import sub-categories only
php artisan inject:data sub_categories

# Import brands with truncate
php artisan inject:data brands --truncate

# Import products (first 10 pages only)
php artisan inject:data products --truncate --limit-pages=10

# Import products starting from page 11
php artisan inject:data products --start-page=11 --limit-pages=10

# Import users/customers
php artisan inject:data users --truncate
```

#### Available Types
- `departments`
- `main_categories`
- `sub_categories`
- `categories` (both main and sub)
- `variant_keys`
- `variants`
- `brands`
- `taxes`
- `cities`
- `blog_categories`
- `blogs`
- `ads_positions`
- `ads`
- `bundle_categories`
- `bundles`
- `occasions`
- `products`
- `users`

---

### 2. `inject:all` - Full Import

Import all data types in the correct dependency order.

#### Syntax
```bash
php artisan inject:all [options]
```

#### Options
- `--truncate` - Truncate existing data before injection
- `--skip=type1,type2` - Skip specific types (comma-separated)
- `--only=type1,type2` - Only inject specific types (comma-separated)

#### Examples

```bash
# Import everything with truncate
php artisan inject:all --truncate

# Import everything without truncate (update mode)
php artisan inject:all

# Import everything except products
php artisan inject:all --truncate --skip=products

# Import only categories and brands
php artisan inject:all --truncate --only=departments,main_categories,sub_categories,brands

# Import only products (useful after other data is imported)
php artisan inject:all --only=products
```

#### Import Order

The `inject:all` command processes data types in this order (respecting dependencies):

1. departments
2. main_categories
3. sub_categories
4. variant_keys
5. variants
6. brands
7. taxes
8. cities
9. blog_categories
10. blogs
11. ads_positions
12. ads
13. bundle_categories
14. bundles
15. occasions
16. products
17. users

---

## Benefits of Using Commands

### 1. No Timeout Issues
- Runs in background
- No browser timeout (1 hour limit instead of 20 minutes)
- Can run for hours if needed

### 2. Better Progress Tracking
- Real-time progress bars
- Detailed statistics
- Error reporting
- Duration tracking

### 3. Automation
- Can be scheduled with Laravel Scheduler
- Can be run via cron jobs
- Can be integrated into deployment scripts

### 4. Resource Management
- Better memory management
- Automatic cleanup
- Detailed logging

### 5. Flexibility
- Process specific pages
- Skip certain types
- Resume from specific page
- Batch processing

---

## Recommended Import Workflow

### First Time Setup (Full Import)

```bash
# Step 1: Import all data in correct order
php artisan inject:all --truncate

# This will import everything in sequence:
# - departments
# - main_categories
# - sub_categories
# - variant_keys
# - variants
# - brands (creates vendors)
# - taxes
# - cities
# - blog_categories
# - blogs
# - ads_positions
# - ads
# - bundle_categories
# - bundles
# - occasions
# - products (with variants and stock)
# - users (customers)
```

### Incremental Updates

```bash
# Update only products (no truncate)
php artisan inject:data products

# Update only categories
php artisan inject:data main_categories
php artisan inject:data sub_categories

# Update only brands/vendors
php artisan inject:data brands
```

### Large Product Import (Batch Processing)

```bash
# Process products in batches of 10 pages
php artisan inject:data products --truncate --limit-pages=10 --start-page=1
php artisan inject:data products --limit-pages=10 --start-page=11
php artisan inject:data products --limit-pages=10 --start-page=21
# ... continue until all pages processed
```

---

## Output Examples

### Single Type Import Output

```
Starting data injection for: brands
Truncate: Yes
Start page: 1

Processing...
████████████████████████████████████████████████████████ 100%

✓ Success!

┌─────────────────┬───────┐
│ Metric          │ Value │
├─────────────────┼───────┤
│ Total Fetched   │ 227   │
│ Pages Processed │ 23    │
│ Last Page       │ 23    │
└─────────────────┴───────┘

Results:
┌────────┬──────────┬─────────┬─────────┬────────┐
│ Type   │ Injected │ Updated │ Skipped │ Errors │
├────────┼──────────┼─────────┼─────────┼────────┤
│ brands │ 227      │ 0       │ 0       │ 0      │
└────────┴──────────┴─────────┴─────────┴────────┘
```

### Full Import Output

```
╔════════════════════════════════════════════════════════════╗
║          BNAIA DATA INJECTION - FULL IMPORT                ║
╚════════════════════════════════════════════════════════════╝

Data types to process: departments, main_categories, sub_categories, brands, products
Truncate: Yes

Do you want to continue? (yes/no) [yes]:
> yes

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Processing [1/5]: departments
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

... (output for departments)

✓ departments completed in 5.23s

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Processing [2/5]: main_categories
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

... (continues for all types)

╔════════════════════════════════════════════════════════════╗
║                    IMPORT SUMMARY                          ║
╚════════════════════════════════════════════════════════════╝

┌───────────────────┬───────────┬──────────┐
│ Type              │ Status    │ Duration │
├───────────────────┼───────────┼──────────┤
│ departments       │ ✓ Success │ 5.23s    │
│ main_categories   │ ✓ Success │ 12.45s   │
│ sub_categories    │ ✓ Success │ 18.67s   │
│ brands            │ ✓ Success │ 45.89s   │
│ products          │ ✓ Success │ 1234.56s │
└───────────────────┴───────────┴──────────┘

Total types processed: 5
Successful: 5
Total duration: 1316.8s (00:21:56)
```

---

## Scheduling (Optional)

You can schedule automatic imports using Laravel's task scheduler.

### Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Import products daily at 2 AM
    $schedule->command('inject:data products')
        ->dailyAt('02:00')
        ->withoutOverlapping()
        ->runInBackground();
    
    // Full import weekly on Sunday at 3 AM
    $schedule->command('inject:all --truncate')
        ->weekly()
        ->sundays()
        ->at('03:00')
        ->withoutOverlapping()
        ->runInBackground();
}
```

### Setup Cron (Linux/Mac):

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Troubleshooting

### Command Not Found

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# List all commands
php artisan list inject
```

### Memory Issues

The commands already have optimizations:
- 2GB memory limit
- 1 hour execution time
- Telescope disabled
- Query logging disabled
- Garbage collection enabled

If still having issues, increase PHP memory in `php.ini`:
```ini
memory_limit = 4096M
```

### Timeout Issues

Commands have 1 hour timeout. For very large imports, use batch processing:

```bash
# Process in smaller batches
php artisan inject:data products --limit-pages=5 --start-page=1
php artisan inject:data products --limit-pages=5 --start-page=6
# etc.
```

---

## Comparison: Browser vs Command

| Feature | Browser | Command |
|---------|---------|---------|
| Timeout | 20 minutes | 1 hour |
| Memory | 2GB | 2GB |
| Progress | Basic | Detailed |
| Errors | Limited | Full details |
| Automation | No | Yes |
| Scheduling | No | Yes |
| Background | No | Yes |
| Batch Processing | Manual | Built-in |

---

## Summary

Use these commands for:
- ✅ Large imports (products with 1000+ items)
- ✅ Automated/scheduled imports
- ✅ Batch processing
- ✅ Better error tracking
- ✅ No browser timeout issues
- ✅ Background processing

The commands use the same `InjectDataController` as the web interface, so all logic and validations are identical.

---

**Created:** February 1, 2026  
**Status:** ✅ Ready to use  
**Commands:** 2 (inject:data, inject:all)

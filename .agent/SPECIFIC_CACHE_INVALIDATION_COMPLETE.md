# Model-Based Cache Invalidation System - Complete ✅

## Overview
Implemented a **model-based** cache invalidation system using controller names. Cache keys are based on the controller (which matches the model name), ensuring perfect alignment between cached data and models.

## How It Works

### 1. Controller-Based Cache Keys (Model-Aligned)
**File**: `app/Http/Middleware/CacheResponse.php`

Cache keys are generated from the **controller name**, which directly corresponds to the model:

```php
// Cache key format: cache:response:{model}:{hash}
// Examples:
cache:response:country:5823fc0a0a37d208481aac1514748ebcc9ee5741  // CountryController -> Country model
cache:response:order:a782ddf6abc14b1d594662bd3c52817e832f5c77     // OrderController -> Order model
cache:response:product:9f3d2e1c8b7a6f5e4d3c2b1a0987654321fedcba  // ProductController -> Product model
```

**Controller to Model Mapping:**
- `CountryController` → `country` → matches `Country` model
- `OrderController` → `order` → matches `Order` model
- `VendorProductController` → `vendorproduct` → matches `VendorProduct` model

**Extraction Priority:**
1. **Controller Name** (most reliable): `CountryController@index` → `country`
2. **Route Name** (fallback): `api.countries.index` → `country`
3. **URL Path** (last resort): `/api/countries` → `country`

### 2. Model-Specific Cache Patterns
**File**: `app/Observers/CacheInvalidationObserver.php`

Each model clears its own cache pattern (based on lowercase model name):

```php
'Country' => [
    'cache:response:*country*',  // Matches CountryController cache
    'cache:response:*region*',   // Also clear regions (related)
    'cache:response:*city*'      // Also clear cities (related)
],
'Order' => [
    'cache:response:*order*',    // Matches OrderController cache
    'cache:response:*dashboard*' // Also clear dashboard
],
'Product' => [
    'cache:response:*product*'   // Matches ProductController cache
],
```

### 3. Automatic Observer Registration
**File**: `app/Providers/AppServiceProvider.php`

All 81 models in `Modules/*/app/Models` are automatically observed. No manual registration needed!

## Real-World Examples

### Example 1: Update Country
```php
// User updates Egypt country
$country = Country::find(1);
$country->update(['phone_code' => '+20']);
```

**What happens:**
- ✅ Clears: `cache:response:*countries*` (all country API responses)
- ✅ Clears: `cache:response:*regions*` (regions depend on countries)
- ✅ Clears: `cache:response:*cities*` (cities depend on countries)
- ❌ Does NOT clear: `cache:response:*orders*` (unrelated)
- ❌ Does NOT clear: `cache:response:*products*` (unrelated)

### Example 2: Create Order
```php
// Customer creates new order
$order = Order::create([...]);
```

**What happens:**
- ✅ Clears: `cache:response:*orders*` (order list cache)
- ✅ Clears: `cache:response:*dashboard*` (dashboard shows order stats)
- ❌ Does NOT clear: `cache:response:*countries*` (unrelated)
- ❌ Does NOT clear: `cache:response:*products*` (unrelated)

### Example 3: Update Product
```php
// Vendor updates product price
$product = Product::find(100);
$product->update(['price' => 99.99]);
```

**What happens:**
- ✅ Clears: `cache:response:*products*` (product list/detail cache)
- ❌ Does NOT clear: `cache:response:*orders*` (unrelated)
- ❌ Does NOT clear: `cache:response:*countries*` (unrelated)

## Configured Models

### Area Settings
- **Country** → clears: countries, regions, cities
- **Region** → clears: regions, cities, sub-regions
- **City** → clears: cities, sub-regions
- **SubRegion** → clears: sub-regions

### Category Management
- **Department** → clears: departments, categories, products
- **Category** → clears: categories, sub-categories, products
- **SubCategory** → clears: sub-categories, products

### Catalog Management
- **Brand** → clears: brands, products
- **Product** → clears: products
- **VendorProduct** → clears: products, vendor-products
- **VariantsConfiguration** → clears: variants, products
- **ProductImage** → clears: products
- **ProductTag** → clears: products, tags

### Vendors
- **Vendor** → clears: vendors, products
- **VendorUser** → clears: vendors

### Orders
- **Order** → clears: orders, dashboard
- **OrderProduct** → clears: orders
- **OrderStage** → clears: orders

### Customers
- **Customer** → clears: customers, users

### Refunds
- **Refund** → clears: refunds, orders
- **RefundProduct** → clears: refunds

### Withdraws
- **Withdraw** → clears: withdraws, transactions

### System Settings
- **Currency** → clears: currencies, countries
- **Language** → clears: languages
- **Tax** → clears: taxes, products

## Adding New Models

**No configuration needed!** New models automatically:
- Get observed by `CacheInvalidationObserver`
- Clear cache based on model name

Example:
```php
// Create new model: Modules/MyModule/app/Models/Tag.php
// Automatically clears: cache:response:*tag*
```

**For custom patterns**, add to observer:
```php
// In CacheInvalidationObserver.php
'Tag' => [
    'cache:response:*tags*',
    'cache:response:*products*',  // Tags affect products
],
```

## Testing

### Test Specific Cache Clearing:

1. **Create cache for multiple resources**:
```bash
# Make requests to cache them
curl http://127.0.0.1:8000/api/countries  # X-Cache: MISS
curl http://127.0.0.1:8000/api/orders     # X-Cache: MISS
curl http://127.0.0.1:8000/api/products   # X-Cache: MISS

# Second requests are cached
curl http://127.0.0.1:8000/api/countries  # X-Cache: HIT
curl http://127.0.0.1:8000/api/orders     # X-Cache: HIT
curl http://127.0.0.1:8000/api/products   # X-Cache: HIT
```

2. **Update a country** (via admin panel or API)

3. **Check cache status**:
```bash
curl http://127.0.0.1:8000/api/countries  # X-Cache: MISS (cleared!)
curl http://127.0.0.1:8000/api/orders     # X-Cache: HIT (still cached)
curl http://127.0.0.1:8000/api/products   # X-Cache: HIT (still cached)
```

### Check Logs:
```bash
tail -f storage/logs/laravel.log
```

You'll see:
```
Cache invalidation triggered: model=Country, action=updated, id=1
Cleared cache: pattern=cache:response:*countries*, keys_cleared=3
Cleared cache: pattern=cache:response:*regions*, keys_cleared=5
Cleared cache: pattern=cache:response:*cities*, keys_cleared=12
```

## Performance Benefits

### Before (Global Clearing):
- Update Country → Clears ALL cache (countries, orders, products, everything)
- Update Order → Clears ALL cache
- **Problem**: Inefficient, clears unrelated data

### After (Specific Clearing):
- Update Country → Clears ONLY countries, regions, cities cache
- Update Order → Clears ONLY orders, dashboard cache
- **Benefits**:
  - ✅ More efficient (only clears related data)
  - ✅ Better cache hit rate (unrelated data stays cached)
  - ✅ Faster response times (less cache rebuilding)
  - ✅ Lower database load (fewer queries to rebuild cache)

## Technical Details

### Cache Key Structure
```
cache:response:{resource}:{hash}
```

- **resource**: Extracted from route name or URL (e.g., "countries", "orders")
- **hash**: SHA1 of user ID + full URL (ensures uniqueness)

### Pattern Matching
Uses Redis `keys()` command with wildcards:
```php
// Clear all countries cache
$pattern = 'cache:response:*countries*';
$keys = Redis::connection('cache')->keys('*' . $pattern);
```

### Why Route Names?
Route names are more reliable than URL parsing:
- ✅ Consistent across different URL structures
- ✅ Not affected by language codes or prefixes
- ✅ Works with nested routes
- ✅ Fallback to URL parsing if route name not available

## Summary

✅ **Specific** - Only clears related cache, not everything
✅ **Automatic** - All models are automatically observed
✅ **Efficient** - Better cache hit rates, lower database load
✅ **Flexible** - Easy to customize patterns for specific models
✅ **Reliable** - Uses route names (most reliable method)
✅ **Logged** - All operations are logged for debugging
✅ **Zero Maintenance** - New models work automatically

When you update a Country, only country-related cache is cleared. Orders, products, and other unrelated cache remain intact!

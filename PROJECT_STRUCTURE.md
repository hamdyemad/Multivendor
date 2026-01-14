# Bnaia E-Commerce Platform - Project Structure Documentation

## Project Overview
**Name:** Bnaia  
**Type:** Multi-Vendor E-Commerce Platform  
**Framework:** Laravel 12.x  
**Architecture:** Modular (using nwidart/laravel-modules)  
**Languages:** English, Arabic (RTL Support)  
**Database:** MySQL  

## Technology Stack

### Backend
- **PHP:** ^8.2
- **Laravel:** ^12.18
- **Authentication:** Laravel Sanctum ^4.0
- **Localization:** mcamara/laravel-localization ^2.3
- **Excel Import/Export:** maatwebsite/excel ^3.1.55
- **DataTables:** yajra/laravel-datatables 12.0
- **Firebase:** kreait/firebase-php 7.9
- **GeoIP:** torann/geoip ^3.0

### Frontend
- **CSS Framework:** Bootstrap 5
- **JavaScript:** jQuery, Vite
- **DataTables:** datatables.net with Bootstrap 5 theme
- **Select2:** Enhanced select boxes
- **Icons:** Unicons

## Project Structure

```
bnaia/
├── app/                          # Core application code
│   ├── Actions/                  # Action classes (business logic)
│   ├── Console/                  # Artisan commands
│   ├── DTOs/                     # Data Transfer Objects
│   ├── Enums/                    # Enumeration classes
│   ├── Events/                   # Event classes
│   ├── Exceptions/               # Custom exception handlers
│   ├── Helpers/                  # Helper functions
│   │   └── functions.php         # Global helper functions
│   ├── Http/                     # HTTP layer
│   │   ├── Controllers/          # Base controllers
│   │   ├── Middleware/           # Middleware
│   │   └── Requests/             # Form requests
│   ├── Interfaces/               # Interface contracts
│   ├── Listeners/                # Event listeners
│   ├── Mail/                     # Mailable classes
│   ├── Models/                   # Core Eloquent models
│   │   ├── ActivityLog.php       # Activity logging
│   │   ├── Attachment.php        # Morph model for images/files
│   │   ├── Language.php          # Multi-language support
│   │   ├── Permission.php        # Permission system
│   │   ├── Role.php              # Role-based access control
│   │   ├── Translation.php       # Translation model
│   │   ├── User.php              # User authentication
│   │   └── UserType.php          # User types (Admin, Vendor, etc.)
│   ├── Observers/                # Model observers
│   ├── Providers/                # Service providers
│   ├── Repositories/             # Repository pattern
│   ├── Services/                 # Service layer
│   └── Traits/                   # Reusable traits
│       ├── Translation.php       # Multi-language trait
│       └── Res.php               # API response trait
│
├── Modules/                      # Modular application structure
│   ├── Accounting/               # Financial accounting module
│   ├── AreaSettings/             # Geographic settings
│   │   ├── Models/
│   │   │   ├── Country.php       # Countries
│   │   │   ├── Region.php        # Regions/States
│   │   │   └── City.php          # Cities
│   │   └── Services/
│   │
│   ├── CatalogManagement/        # Product catalog management
│   │   ├── Models/
│   │   │   ├── Product.php       # Bank products (catalog)
│   │   │   ├── VendorProduct.php # Vendor's products
│   │   │   ├── VendorProductVariant.php # Product variants
│   │   │   ├── VendorProductVariantStock.php # Stock per region
│   │   │   ├── Brand.php         # Product brands
│   │   │   ├── Tax.php           # Tax configurations
│   │   │   ├── Promocode.php     # Promotional codes
│   │   │   ├── Occasion.php      # Special occasions/sales
│   │   │   ├── OccasionProduct.php # Products in occasions
│   │   │   ├── Bundle.php        # Product bundles
│   │   │   ├── BundleCategory.php # Bundle categories
│   │   │   ├── VariantsConfiguration.php # Variant configs
│   │   │   ├── VariantConfigurationKey.php # Variant keys
│   │   │   ├── StockBooking.php  # Stock reservation system
│   │   │   └── Review.php        # Product reviews
│   │   ├── Exports/              # Excel export classes
│   │   │   ├── ProductsExport.php
│   │   │   ├── ProductsSheetExport.php
│   │   │   ├── ImagesSheetExport.php
│   │   │   ├── VariantsSheetExport.php
│   │   │   ├── VariantStockSheetExport.php
│   │   │   ├── OccasionsSheetExport.php
│   │   │   └── OccasionProductsSheetExport.php
│   │   ├── Imports/              # Excel import classes
│   │   │   ├── ProductsImport.php
│   │   │   ├── ProductsSheetImport.php
│   │   │   ├── ImagesSheetImport.php
│   │   │   ├── VariantsSheetImport.php
│   │   │   └── VariantStockSheetImport.php
│   │   └── Services/
│   │
│   ├── CategoryManagment/        # Category hierarchy
│   │   ├── Models/
│   │   │   ├── Department.php    # Top-level categories
│   │   │   ├── Category.php      # Main categories
│   │   │   └── SubCategory.php   # Sub-categories
│   │   └── Services/
│   │
│   ├── Customer/                 # Customer management
│   │   ├── Models/
│   │   │   ├── Customer.php      # Customer accounts
│   │   │   └── Address.php       # Customer addresses
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── CustomerAuthController.php # Auth API
│   │   │       └── CustomerApiController.php  # Customer API
│   │   └── Transformers/
│   │       ├── CustomerApiResource.php
│   │       └── AddressResource.php
│   │
│   ├── Order/                    # Order management
│   │   ├── Models/
│   │   │   ├── Order.php         # Orders
│   │   │   ├── OrderProduct.php  # Order items
│   │   │   ├── OrderStage.php    # Order stages (pending, processing, etc.)
│   │   │   ├── VendorOrderStage.php # Vendor-specific stages
│   │   │   ├── OrderFulfillment.php # Fulfillment tracking
│   │   │   └── OrderStatusHistory.php # Status changes log
│   │   └── Services/
│   │
│   ├── Report/                   # Reporting module
│   │   └── Controllers/
│   │
│   ├── SystemSetting/            # System configuration
│   │   ├── Models/
│   │   │   ├── Slider.php        # Homepage sliders
│   │   │   ├── FAQ.php           # FAQs
│   │   │   ├── Ad.php            # Advertisements
│   │   │   └── FooterContent.php # Footer settings
│   │   └── Controllers/
│   │
│   ├── Vendor/                   # Vendor management
│   │   ├── Models/
│   │   │   └── Vendor.php        # Vendor accounts
│   │   └── Services/
│   │
│   └── Withdraw/                 # Financial withdrawals
│       ├── Models/
│       │   ├── Transaction.php   # Financial transactions
│       │   └── WithdrawRequest.php # Withdrawal requests
│       └── Services/
│
├── database/
│   ├── migrations/               # Database migrations
│   ├── seeders/                  # Database seeders
│   └── factories/                # Model factories
│
├── resources/
│   ├── views/                    # Blade templates
│   │   ├── layout/               # Layout templates
│   │   │   ├── app.blade.php     # Main layout
│   │   │   └── auth.blade.php    # Auth layout
│   │   ├── partials/             # Reusable partials
│   │   │   ├── _menu.blade.php   # Sidebar menu
│   │   │   ├── _header.blade.php # Header
│   │   │   └── _footer.blade.php # Footer
│   │   ├── pages/                # Page views
│   │   │   ├── dashboard/        # Dashboard views
│   │   │   ├── admin_management/ # Admin CRUD
│   │   │   └── vendor_users_management/ # Vendor users
│   │   └── components/           # Blade components
│   ├── js/                       # JavaScript files
│   ├── scss/                     # SASS stylesheets
│   └── lang/                     # Language files
│       ├── en/                   # English translations
│       └── ar/                   # Arabic translations
│
├── routes/
│   ├── web.php                   # Web routes
│   ├── admin.php                 # Admin routes
│   ├── api.php                   # API routes
│   └── console.php               # Console routes
│
├── config/                       # Configuration files
│   ├── app.php                   # App config
│   ├── database.php              # Database config
│   ├── permissions.php           # Permissions config
│   ├── languages.php             # Language config
│   ├── responses.php             # API responses config
│   └── order_stage_transitions.php # Order workflow
│
├── public/                       # Public assets
│   ├── assets/                   # Static assets
│   │   ├── admin_products_demo.xlsx # Admin import template
│   │   └── vendor_products_demo.xlsx # Vendor import template
│   ├── storage/                  # Symlink to storage
│   └── build/                    # Compiled assets
│
└── storage/
    ├── app/                      # Application storage
    ├── framework/                # Framework files
    └── logs/                     # Log files
```

## Key Architectural Patterns

### 1. **Modular Architecture**
- Each module is self-contained with its own:
  - Models
  - Controllers
  - Views
  - Routes
  - Migrations
  - Translations
  - Services

### 2. **Repository Pattern**
- Data access logic separated from business logic
- Located in `app/Repositories/` and `Modules/*/app/Repositories/`

### 3. **Service Layer**
- Business logic encapsulated in service classes
- Located in `app/Services/` and `Modules/*/app/Services/`

### 4. **Action Classes**
- Complex operations isolated in action classes
- Located in `app/Actions/` and `Modules/*/app/Actions/`

### 5. **Resource/Transformer Pattern**
- API responses formatted using Resource classes
- Located in `Modules/*/app/Transformers/` or `Modules/*/app/Http/Resources/`

## Database Architecture

### Core Tables
- `users` - User accounts (Admin, Vendor, Customer)
- `user_types` - User type definitions
- `roles` - RBAC roles
- `permissions` - RBAC permissions
- `languages` - Supported languages
- `translations` - Multi-language content (polymorphic)
- `attachments` - File storage (polymorphic)
- `activity_logs` - System activity tracking

### Geographic Tables
- `countries` - Countries
- `regions` - States/Regions
- `cities` - Cities

### Catalog Tables
- `products` - Bank products (shared catalog)
- `vendor_products` - Vendor-specific products
- `vendor_product_variants` - Product variants
- `vendor_product_variant_stocks` - Stock per region
- `brands` - Product brands
- `taxes` - Tax configurations
- `promocodes` - Promotional codes
- `occasions` - Special occasions
- `occasion_products` - Products in occasions
- `bundles` - Product bundles
- `bundle_categories` - Bundle categories
- `variants_configurations` - Variant configurations
- `variant_configuration_keys` - Variant keys
- `stock_bookings` - Stock reservation system
- `reviews` - Product reviews

### Category Tables
- `departments` - Top-level categories
- `categories` - Main categories
- `sub_categories` - Sub-categories

### Order Tables
- `orders` - Customer orders
- `order_products` - Order line items
- `order_stages` - Order workflow stages
- `vendor_order_stages` - Vendor-specific order stages
- `order_fulfillments` - Fulfillment tracking
- `order_status_histories` - Status change logs

### Customer Tables
- `customers` - Customer accounts
- `addresses` - Customer addresses

### Vendor Tables
- `vendors` - Vendor accounts

### Financial Tables
- `transactions` - Financial transactions
- `withdraw_requests` - Withdrawal requests

## Key Relationships

### Polymorphic Relationships
1. **Attachments (Images/Files)**
   - `attachable_type` + `attachable_id`
   - Used by: Products, Vendors, etc.
   - Types: `main_image`, `additional_image`

2. **Translations**
   - `translatable_type` + `translatable_id`
   - Used by: Products, Categories, Brands, etc.
   - Fields: `lang_id`, `lang_key`, `lang_value`

### Product Hierarchy
```
Product (Bank Product - Shared Catalog)
  └── VendorProduct (Vendor's Product Instance)
      └── VendorProductVariant (Product Variants)
          └── VendorProductVariantStock (Stock per Region)
```

### Category Hierarchy
```
Department (Top Level)
  └── Category (Main Category)
      └── SubCategory (Sub Category)
```

### Order Workflow
```
Order
  ├── OrderProduct (Line Items)
  ├── OrderStage (Global Stage)
  ├── VendorOrderStage (Per Vendor Stage)
  └── OrderFulfillment (Delivery Tracking)
```

## User Types & Roles

### User Types
1. **Super Admin** - Full system access
2. **Admin** - Administrative access
3. **Vendor** - Vendor portal access
4. **Customer** - Customer portal access (API)

### Permission System
- Role-Based Access Control (RBAC)
- Permissions defined in `config/permissions.php`
- Format: `resource.action` (e.g., `products.create`, `orders.view`)

## Multi-Language Support

### Implementation
- **Trait:** `App\Traits\Translation`
- **Model:** `App\Models\Translation`
- **Helper:** `getTranslation($key, $locale)`

### Supported Languages
- English (en)
- Arabic (ar) with RTL support

### Translation Storage
- Polymorphic relationship
- Fields: `lang_id`, `lang_key`, `lang_value`
- Example keys: `title`, `description`, `name`, `details`

### How Translations Work

#### 1. Database Structure
```sql
translations table:
- id (primary key)
- translatable_type (morphs - e.g., 'Modules\CatalogManagement\app\Models\Product')
- translatable_id (morphs - e.g., product ID)
- lang_id (foreign key to languages table)
- lang_key (e.g., 'title', 'description', 'name')
- lang_value (the actual translated text)
- created_at
- updated_at
```

#### 2. Using Translation Trait
Any model that needs translations must use the `Translation` trait:

```php
use App\Traits\Translation;

class Product extends Model
{
    use Translation;
    
    // The model automatically gets translations() relationship
}
```

#### 3. Storing Translations
```php
// Create a product
$product = Product::create([...]);

// Store English translation
$product->translations()->create([
    'lang_id' => 1, // English language ID
    'lang_key' => 'title',
    'lang_value' => 'Product Title'
]);

// Store Arabic translation
$product->translations()->create([
    'lang_id' => 2, // Arabic language ID
    'lang_key' => 'title',
    'lang_value' => 'عنوان المنتج'
]);
```

#### 4. Retrieving Translations
```php
// Get translation for current locale
$title = $product->getTranslation('title', app()->getLocale());

// Get translation for specific locale
$titleEn = $product->getTranslation('title', 'en');
$titleAr = $product->getTranslation('title', 'ar');

// Using magic property (if configured)
$title = $product->title; // Returns translation for current locale
```

#### 5. Common Translation Keys
- **Products:** `title`, `details`, `summary`, `features`, `instructions`, `extra_description`, `material`, `meta_title`, `meta_description`, `meta_keywords`
- **Categories:** `name`, `description`
- **Brands:** `name`, `description`
- **Vendors:** `name`, `description`
- **Occasions:** `name`, `description`

#### 6. Bulk Translation Loading
```php
// Eager load translations to avoid N+1 queries
$products = Product::with('translations')->get();

// Group translations by key for easier access
$translations = $product->translations->groupBy('lang_key');
$titleTranslations = $translations['title'] ?? collect();
```

#### 7. Translation in Forms
```blade
<!-- Loop through languages -->
@foreach($languages as $language)
    <div class="form-group">
        <label>Title ({{ $language->name }})</label>
        <input type="text" 
               name="title_{{ $language->code }}" 
               value="{{ old('title_' . $language->code, $product->getTranslation('title', $language->code)) }}">
    </div>
@endforeach
```

## File Attachments System

### Implementation
- **Model:** `App\Models\Attachment`
- **Storage:** `storage/app/public/`
- **Public Access:** `public/storage/` (symlink)

### How Attachments Work

#### 1. Database Structure
```sql
attachments table:
- id (primary key)
- attachable_type (morphs - e.g., 'Modules\CatalogManagement\app\Models\Product')
- attachable_id (morphs - e.g., product ID)
- type (e.g., 'main_image', 'additional_image', 'document')
- path (file path in storage)
- created_at
- updated_at
- deleted_at (soft deletes)
```

#### 2. Model Relationships
Any model that needs attachments defines morph relationships:

```php
class Product extends Model
{
    /**
     * Get the main product image
     */
    public function mainImage()
    {
        return $this->morphOne(Attachment::class, 'attachable')
                    ->where('type', 'main_image');
    }

    /**
     * Get additional product images
     */
    public function additionalImages()
    {
        return $this->morphMany(Attachment::class, 'attachable')
                    ->where('type', 'additional_image')
                    ->orderBy('id');
    }
    
    /**
     * Get all attachments
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
```

#### 3. Storing Attachments
```php
// Store main image
if ($request->hasFile('main_image')) {
    $path = $request->file('main_image')->store('products', 'public');
    
    $product->attachments()->create([
        'type' => 'main_image',
        'path' => $path
    ]);
}

// Store multiple additional images
if ($request->hasFile('additional_images')) {
    foreach ($request->file('additional_images') as $image) {
        $path = $image->store('products', 'public');
        
        $product->attachments()->create([
            'type' => 'additional_image',
            'path' => $path
        ]);
    }
}
```

#### 4. Retrieving Attachments
```php
// Get main image
$mainImage = $product->mainImage;
$mainImageUrl = $mainImage ? asset('storage/' . $mainImage->path) : null;

// Get all additional images
$additionalImages = $product->additionalImages;
foreach ($additionalImages as $image) {
    $url = asset('storage/' . $image->path);
}

// Get specific attachment by type
$document = $product->attachments()->where('type', 'document')->first();
```

#### 5. Updating Attachments
```php
// Update main image (delete old, create new)
if ($request->hasFile('main_image')) {
    // Delete old main image
    $oldImage = $product->mainImage;
    if ($oldImage) {
        Storage::disk('public')->delete($oldImage->path);
        $oldImage->delete();
    }
    
    // Store new image
    $path = $request->file('main_image')->store('products', 'public');
    $product->attachments()->create([
        'type' => 'main_image',
        'path' => $path
    ]);
}
```

#### 6. Deleting Attachments
```php
// Delete attachment and file
$attachment = Attachment::find($id);
if ($attachment) {
    // Delete physical file
    Storage::disk('public')->delete($attachment->path);
    
    // Delete database record
    $attachment->delete(); // Soft delete
    // or
    $attachment->forceDelete(); // Permanent delete
}
```

#### 7. Common Attachment Types
- **Products:**
  - `main_image` - Primary product image
  - `additional_image` - Gallery images
  
- **Vendors:**
  - `logo` - Vendor logo
  - `cover` - Cover image
  - `document` - Legal documents
  
- **Categories:**
  - `icon` - Category icon
  - `banner` - Category banner

#### 8. Attachment in Views
```blade
<!-- Display main image -->
@if($product->mainImage)
    <img src="{{ asset('storage/' . $product->mainImage->path) }}" 
         alt="{{ $product->title }}">
@else
    <img src="{{ asset('assets/img/no-image.png') }}" 
         alt="No image">
@endif

<!-- Display additional images -->
@foreach($product->additionalImages as $image)
    <img src="{{ asset('storage/' . $image->path) }}" 
         alt="{{ $product->title }}">
@endforeach
```

#### 9. Eager Loading Attachments
```php
// Avoid N+1 queries
$products = Product::with(['mainImage', 'additionalImages'])->get();

// Or load all attachments
$products = Product::with('attachments')->get();
```

#### 10. Attachment Validation
```php
// In Form Request
public function rules()
{
    return [
        'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];
}
```

#### 11. Storage Configuration
```php
// config/filesystems.php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
]
```

#### 12. Creating Storage Symlink
```bash
# Create symlink from public/storage to storage/app/public
php artisan storage:link
```

### Attachment vs Direct File Storage

**Use Attachment Model When:**
- Need to track file metadata
- Multiple files per model
- Different file types (images, documents)
- Need soft deletes
- Need to query files

**Use Direct Column When:**
- Single file per model
- Simple use case
- No metadata needed
- Example: `avatar` column in users table

## Import/Export System

### Excel Import Structure
**Sheets:**
1. `products` - Product data with SKU
2. `images` - Product images
3. `variants` - Product variants with pricing
4. `variant_stock` - Stock per region
5. `occasions` - Occasions (admin only)
6. `occasion_products` - Products in occasions (admin only)

### Excel Export Structure
- Same structure as import
- Respects user role (admin vs vendor)
- Applies current filters
- File naming: `products_export_YYYY-MM-DD_HHMMSS.xlsx`

## API Endpoints

### Authentication
- `POST /api/auth/register` - Customer registration
- `POST /api/auth/login` - Customer login
- `POST /api/auth/logout` - Logout
- `GET /api/auth/profile` - Get profile
- `POST /api/auth/update-profile` - Update profile

### Customer API
- Multi-language support via `lang` header
- Country-specific via `X-Country-Code` header
- Authentication via Sanctum tokens

## Stock Management System

### Stock Booking Flow
1. **Booked** - Order placed, stock reserved
2. **Allocated** - Order confirmed, preparing
3. **Fulfilled** - Order delivered
4. **Released** - Order cancelled, stock released

### Stock Calculation
- `total_stock` - Total available
- `booked_stock` - Reserved for orders
- `allocated_stock` - Being prepared
- `fulfilled_stock` - Delivered
- `remaining_stock` - Available for new orders

## File Storage

### Storage Structure
- **Public Storage:** `storage/app/public/`
- **Symlink:** `public/storage/`
- **Attachments:** Stored via `Attachment` model
- **Types:** `main_image`, `additional_image`

## Configuration Files

### Important Configs
- `config/permissions.php` - Permission definitions
- `config/languages.php` - Language settings
- `config/responses.php` - API response messages
- `config/order_stage_transitions.php` - Order workflow
- `config/modules.php` - Module configuration

## Helper Functions

### Global Helpers (`app/Helpers/functions.php`)
- `isAdmin()` - Check if user is admin
- `isVendor()` - Check if user is vendor
- `currency()` - Get current currency
- `getTranslation()` - Get translation

## Development Guidelines

### Code Organization
1. **Controllers** - Handle HTTP requests
2. **Services** - Business logic
3. **Repositories** - Data access
4. **Actions** - Complex operations
5. **DTOs** - Data transfer
6. **Resources** - API responses

### Naming Conventions
- **Models:** Singular (e.g., `Product`, `Order`)
- **Tables:** Plural (e.g., `products`, `orders`)
- **Controllers:** Singular + Controller (e.g., `ProductController`)
- **Services:** Singular + Service (e.g., `ProductService`)

### Best Practices
1. Use service layer for business logic
2. Use repositories for data access
3. Use form requests for validation
4. Use resources for API responses
5. Use translations for all user-facing text
6. Log important actions via `ActivityLog`

## Testing

### Test Structure
- `tests/Feature/` - Feature tests
- `tests/Unit/` - Unit tests
- Module-specific tests in `Modules/*/tests/`

## Deployment

### Requirements
- PHP ^8.2
- MySQL
- Composer
- Node.js & NPM
- Redis (optional, for caching)

### Build Commands
```bash
composer install
npm install
npm run build
php artisan migrate
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Important Notes

1. **Product System:**
   - Bank products are shared catalog
   - Vendors create instances via VendorProduct
   - Each vendor can have different pricing/stock

2. **Multi-Tenancy:**
   - Country-based filtering
   - Vendor-based data isolation
   - Region-based stock management

3. **Order Workflow:**
   - Configurable stages
   - Vendor-specific tracking
   - Stock booking system

4. **Permissions:**
   - Role-based access control
   - Module-level permissions
   - Action-level permissions

---

**Last Updated:** January 2025  
**Version:** 1.0  
**Maintained By:** Development Team

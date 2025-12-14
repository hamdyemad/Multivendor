# Project Architecture Guide

## 📋 Overview

This is a **Laravel 12** multi-vendor e-commerce platform using a **modular architecture** with the `nwidart/laravel-modules` package. The application follows a clean, layered architecture pattern with **Service-Repository-Interface** structure.

---

## 🏗️ Architecture Layers

### Core Architecture Pattern

```
Controller → Service → Repository → Model
     ↓          ↓           ↓
  Request    Interface   Interface
     ↓          ↓
   DTO      Action
```

### Layer Responsibilities

#### 1. **Controllers** (`app/Http/Controllers` or `Modules/{Module}/app/Http/Controllers`)
- Handle HTTP requests and responses
- Validate incoming data using Form Requests
- Call Service layer methods
- Return views or JSON responses
- **Should NOT contain business logic**

#### 2. **Services** (`app/Services` or `Modules/{Module}/app/Services`)
- Business logic layer
- Orchestrate multiple repository calls if needed
- Handle complex business rules
- Depend on Repository Interfaces (dependency injection)
- Example: `UserService`, `VendorService`, `ProductService`

```php
class VendorService
{
    public function __construct(public VendorInterface $vendorInterface)
    {
    }

    public function createVendor(array $data)
    {
        return $this->vendorInterface->createVendor($data);
    }
}
```

#### 3. **Repositories** (`app/Repositories` or `Modules/{Module}/app/Repositories`)
- Data access layer
- Implement Repository Interfaces
- Handle database queries and Eloquent operations
- Manage transactions
- Handle relationships and eager loading
- Example: `UserRepository`, `VendorRepository`, `ProductRepository`

```php
class VendorRepository implements VendorInterface
{
    public function getAllVendors(array $filters = [], int $perPage = 10)
    {
        $query = Vendor::with(['user', 'country', 'translations'])
            ->filter($filters);
        return ($perPage == 0) ? $query->get() : $query->latest()->paginate($perPage);
    }

    public function createVendor(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create vendor logic with relationships
        });
    }
}
```

#### 4. **Interfaces** (`app/Interfaces` or `Modules/{Module}/app/Interfaces`)
- Define contracts for Repositories
- Ensure consistent method signatures
- Enable dependency injection and testability
- Example: `UserInterface`, `VendorInterface`, `ProductInterface`

```php
interface VendorInterface
{
    public function getAllVendors(array $filters = [], int $perPage = 10);
    public function getVendorById(int $id);
    public function createVendor(array $data);
    public function updateVendor(int $id, array $data);
    public function deleteVendor(int $id);
}
```

#### 5. **Actions** (`app/Actions` or `Modules/{Module}/app/Actions`)
- Encapsulate specific business operations
- Complex queries or business logic
- Used by Repositories when logic is complex
- Can inject Services or Repositories
- Example: `UserAction`, `VendorAction`

```php
class VendorAction
{
    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected VendorInterface $vendorInterface
    ) {}

    public function getDataTable($data)
    {
        // Complex DataTables logic
        // Filtering, sorting, pagination
        return [
            'data' => $tableData,
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords,
        ];
    }
}
```

#### 6. **DTOs (Data Transfer Objects)** (`app/DTOs` or `Modules/{Module}/app/DTOs`)
- Transfer data between layers
- Type-safe data containers
- Validation logic
- Convert request data to structured format
- Extend base `FilterDTO`

```php
class VendorFilterDTO extends FilterDTO
{
    private array $errors = [];

    public function __construct(
        public ?string $search = null,
        public ?string $country_id = null,
        public ?int $per_page = null,
        public ?string $paginated = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            search: $request->input('search'),
            country_id: $request->input('country_id'),
            per_page: $request->integer('per_page', 15),
            paginated: $request->input('paginated', null)
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'country_id' => $this->country_id,
            'per_page' => $this->per_page,
            'paginated' => $this->paginated,
        ], fn($value) => $value !== null);
    }

    public function validate(): bool
    {
        $this->errors = [];
        // Validation logic
        return count($this->errors) === 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
```

#### 7. **Models** (`app/Models` or `Modules/{Module}/app/Models`)
- Eloquent models
- Define relationships
- Use traits for common functionality
- Implement scopes for filtering
- Handle translations (polymorphic)

```php
class Vendor extends BaseModel
{
    use HasTranslation, HasSlug, HasCountries, HasFilterScopes;

    protected $fillable = ['user_id', 'type', 'active', 'slug'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'vendors_activities');
    }
}
```

---

## 📦 Module Structure

Each module follows this structure:

```
Modules/{ModuleName}/
├── app/
│   ├── Actions/              # Business operation handlers
│   ├── DTOs/                 # Data Transfer Objects
│   ├── Http/
│   │   ├── Controllers/      # HTTP request handlers
│   │   ├── Requests/         # Form request validation
│   │   └── Resources/        # API resources/transformers
│   ├── Interfaces/           # Repository contracts
│   │   └── Api/              # API-specific interfaces
│   ├── Models/               # Eloquent models
│   ├── Repositories/         # Data access implementations
│   │   └── Api/              # API-specific repositories
│   ├── Services/             # Business logic layer
│   │   └── Api/              # API-specific services
│   └── Providers/
│       ├── {Module}ServiceProvider.php
│       ├── EventServiceProvider.php
│       └── RouteServiceProvider.php
├── config/
│   └── config.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── lang/
│   ├── ar/                   # Arabic translations
│   └── en/                   # English translations
├── resources/
│   ├── views/
│   └── assets/
├── routes/
│   ├── web.php
│   ├── api.php
│   └── admin.php (optional)
├── tests/
│   ├── Feature/
│   └── Unit/
├── composer.json
├── module.json
├── package.json
└── vite.config.js
```

---

## 🔧 Key Modules

### 1. **AreaSettings** - Geographic Management
- Countries, Cities, Regions, SubRegions
- Translation support for area names

### 2. **CatalogManagement** - Product & Catalog
- Products, ProductVariants, VendorProducts
- Brands, Taxes, VariantConfigurations
- Promocodes, Reviews, Occasions
- Bundles and Bundle Categories

### 3. **CategoryManagment** - Category Structure
- Departments, Categories, SubCategories
- Activities (vendor activities)

### 4. **Customer** - Customer Management
- Customer accounts, addresses
- OTP verification, FCM tokens
- Password reset tokens

### 5. **Order** - Order Processing
- Orders, OrderProducts, OrderFulfillment
- Cart, Wishlist
- Order stages and transitions
- Pipeline pattern for order creation

### 6. **Vendor** - Vendor Management
- Vendor profiles, documents
- Vendor requests (registration)
- Vendor-product relationships

### 7. **Withdraw** - Financial Transactions
- Withdrawal management for vendors

### 8. **SystemSetting** - System Configuration
- Currencies, Messages
- Activity logs
- Points system (rewards)

---

## 🔗 Dependency Injection & Service Binding

### App Level (`app/Providers/AppServiceProvider.php`)

```php
public function register()
{
    $this->app->bind(UserInterface::class, UserRepository::class);
    $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
}
```

### Module Level (`Modules/{Module}/app/Providers/{Module}ServiceProvider.php`)

```php
public function register(): void
{
    $this->app->bind(
        VendorInterface::class,
        VendorRepository::class
    );

    $this->app->bind(
        VendorApiRepositoryInterface::class,
        VendorApiRepository::class
    );
}
```

---

## 🌐 Multi-Language Support

### Translation System
- Uses polymorphic `translations` table
- Each translatable model has a `translations` relationship
- Translation keys: `name`, `description`, `meta_title`, `meta_description`, `meta_keywords`

### Usage Pattern

```php
// Store translations
foreach ($data['translations'] as $languageId => $fields) {
    $language = Language::find($languageId);
    
    $model->translations()->updateOrCreate(
        [
            'lang_id' => $language->id,
            'lang_key' => 'name',
        ],
        [
            'lang_value' => $fields['name'],
        ]
    );
}

// Retrieve translations
$name = $vendor->getTranslation('name', app()->getLocale());
```

---

## 🗄️ Database Patterns

### Soft Deletes
Most models use soft deletes (`deleted_at` column)

### Country Filtering
Models can be filtered by country using `HasCountries` trait and `ModelCountry` pivot

### Slugs
Most models have slugs for SEO-friendly URLs using `HasSlug` trait

### Timestamps
All models track `created_at` and `updated_at`

### Observers
- `GlobalModelObserver` - Tracks all model changes for activity logging
- Automatically registered for all App and Module models

---

## 📝 Common Traits

### `app/Traits/`

1. **`Res`** - Response helper
   - `sendData()` - Standardized JSON responses
   
2. **`HasFilterScopes`** - Query filtering
   - `scopeFilter()` - Apply filter arrays to queries
   
3. **`HasSlug`** - Automatic slug generation
   - Generates unique slugs from name translations
   
4. **`HasCountries`** - Multi-country support
   - `scopeCountryFilter()` - Filter by country
   
5. **`LogsActivity`** - Activity logging
   - `logActivity()` - Log user actions
   
6. **`Translation`** - Translation helpers
   - `getTranslation()` - Get translated value

---

## 🎯 How to Create a New Feature

### Example: Creating a "Brand" feature in CatalogManagement module

#### Step 1: Create Interface
```php
// Modules/CatalogManagement/app/Interfaces/BrandRepositoryInterface.php
interface BrandRepositoryInterface
{
    public function getAllBrands(array $filters = [], int $perPage = 10);
    public function getBrandById(int $id);
    public function createBrand(array $data);
    public function updateBrand(int $id, array $data);
    public function deleteBrand(int $id);
}
```

#### Step 2: Create Repository
```php
// Modules/CatalogManagement/app/Repositories/BrandRepository.php
class BrandRepository implements BrandRepositoryInterface
{
    public function getAllBrands(array $filters = [], int $perPage = 10)
    {
        $query = Brand::with(['translations'])->filter($filters);
        return ($perPage == 0) ? $query->get() : $query->latest()->paginate($perPage);
    }

    public function createBrand(array $data)
    {
        return DB::transaction(function () use ($data) {
            $brand = Brand::create([
                'active' => $data['active'] ?? true,
            ]);
            
            $this->storeTranslations($brand, $data);
            
            return $brand;
        });
    }
    
    // ... other methods
}
```

#### Step 3: Create Service
```php
// Modules/CatalogManagement/app/Services/BrandService.php
class BrandService
{
    public function __construct(public BrandRepositoryInterface $brandInterface)
    {
    }

    public function getAllBrands(array $filters = [], int $perPage = 10)
    {
        return $this->brandInterface->getAllBrands($filters, $perPage);
    }

    public function createBrand(array $data)
    {
        return $this->brandInterface->createBrand($data);
    }
    
    // ... other methods
}
```

#### Step 4: Create DTO (if needed for filtering)
```php
// Modules/CatalogManagement/app/DTOs/BrandFilterDTO.php
class BrandFilterDTO extends FilterDTO
{
    private array $errors = [];

    public function __construct(
        public ?string $search = null,
        public ?string $active = null,
        public ?int $per_page = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            search: $request->input('search'),
            active: $request->input('active'),
            per_page: $request->integer('per_page', 15)
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'active' => $this->active,
            'per_page' => $this->per_page,
        ], fn($value) => $value !== null);
    }

    public function validate(): bool
    {
        $this->errors = [];
        return true;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
```

#### Step 5: Create Action (if complex logic needed)
```php
// Modules/CatalogManagement/app/Actions/BrandAction.php
class BrandAction
{
    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected BrandRepositoryInterface $brandInterface
    ) {}

    public function getDataTable($data)
    {
        // Complex DataTables logic
        // Return formatted data for frontend
    }
}
```

#### Step 6: Create Controller
```php
// Modules/CatalogManagement/app/Http/Controllers/BrandController.php
class BrandController extends Controller
{
    public function __construct(
        protected BrandService $brandService,
        protected LanguageService $languageService
    ) {}

    public function index(Request $request)
    {
        $filters = BrandFilterDTO::fromRequest($request)->toArray();
        $brands = $this->brandService->getAllBrands($filters, 15);
        
        return view('catalogmanagement::brand.index', compact('brands'));
    }

    public function store(StoreBrandRequest $request)
    {
        $data = $request->validated();
        $brand = $this->brandService->createBrand($data);
        
        return redirect()->route('brands.index')
            ->with('success', __('catalogmanagement::brand.created_successfully'));
    }
    
    // ... other CRUD methods
}
```

#### Step 7: Register Binding
```php
// Modules/CatalogManagement/app/Providers/CatalogManagementServiceProvider.php
public function register(): void
{
    $this->app->bind(
        BrandRepositoryInterface::class,
        BrandRepository::class
    );
}
```

#### Step 8: Create Routes
```php
// Modules/CatalogManagement/routes/web.php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('brands', BrandController::class);
});
```

---

## 🧪 Testing Structure

### Feature Tests
- Test full workflows (Controller → Service → Repository)
- Test with database transactions
- Located in `tests/Feature/` or `Modules/{Module}/tests/Feature/`

### Unit Tests
- Test individual classes in isolation
- Mock dependencies
- Located in `tests/Unit/` or `Modules/{Module}/tests/Unit/`

---

## 🔑 Key Technologies & Packages

- **Laravel 12** - Framework
- **PHP 8.2+** - Language version
- **nwidart/laravel-modules** - Modular architecture
- **yajra/laravel-datatables** - DataTables integration
- **mcamara/laravel-localization** - Localization
- **maatwebsite/excel** - Excel import/export
- **Laravel Sanctum** - API authentication
- **Laravel UI** - Authentication scaffolding

---

## 📊 Database Relationships

### Common Patterns

1. **Polymorphic Translations**
   - `Translation` model polymorphic to all translatable models
   - `translatable_type`, `translatable_id`

2. **Polymorphic Attachments**
   - `Attachment` model for images and documents
   - Types: `logo`, `banner`, `docs`, `image`

3. **Pivot Tables**
   - `user_role` - Users to Roles (many-to-many)
   - `vendors_activities` - Vendors to Activities (many-to-many)
   - `model_countries` - Any model to Countries (polymorphic many-to-many)

4. **Activity Logging**
   - `activity_logs` table tracks all changes
   - `GlobalModelObserver` automatically logs model events

---

## 🎨 Frontend Structure

### Views Location
- Core: `resources/views/`
- Modules: `Modules/{Module}/resources/views/`

### Assets
- CSS: `resources/scss/` and `public/css/`
- JS: `resources/js/` and `public/js/`
- Module assets: `Modules/{Module}/resources/assets/`

### Blade Components
- Reusable components in `resources/views/components/`
- Module components in `Modules/{Module}/resources/views/components/`

---

## 🚀 Development Workflow

### 1. Plan the Feature
- Identify module (existing or new)
- Define data structure (models, migrations)
- List CRUD operations needed

### 2. Database First
- Create migration
- Create model with relationships
- Add to seeder if needed

### 3. Build from Bottom Up
- Create Interface (contract)
- Create Repository (data access)
- Create Service (business logic)
- Create Action (if complex logic)
- Create DTO (if filtering needed)

### 4. Wire Up
- Register bindings in ServiceProvider
- Create Controller
- Create Form Requests for validation
- Create Routes

### 5. Frontend
- Create views
- Add translations
- Add JavaScript if needed

### 6. Test
- Write feature tests
- Write unit tests
- Manual testing

---

## 📚 When to Send Requests to AI

When you want me to help you with a new feature or modification, provide:

### Minimum Required Information

1. **Module Name** (if existing) or specify if it's a new module
2. **Feature Description** - What you want to build
3. **CRUD Operations** - Create, Read, Update, Delete (which ones?)
4. **Fields/Data Structure** - What data needs to be stored
5. **Relationships** - How it connects to other models
6. **Special Requirements** - Filters, validation rules, business logic

### Example Request Format

```
Module: CatalogManagement
Feature: Product Reviews
Operations: Create, Read, Update, Delete
Fields:
  - customer_id (foreign key to customers)
  - product_id (foreign key to products)
  - rating (integer 1-5)
  - comment (text, translatable)
  - status (enum: pending, approved, rejected)
Relationships:
  - belongsTo Customer
  - belongsTo Product
Special Requirements:
  - Only approved reviews show on frontend
  - Customers can only review products they purchased
  - Calculate average rating for products
```

---

## ✅ Best Practices

1. **Always use Interfaces** - Never inject concrete classes in constructors
2. **Use Transactions** - Wrap multi-step operations in DB transactions
3. **Keep Controllers Thin** - Business logic belongs in Services/Repositories
4. **Use DTOs for Complex Filters** - Type safety and validation
5. **Follow Naming Conventions** - `{Entity}Interface`, `{Entity}Repository`, `{Entity}Service`
6. **Use Traits for Reusable Logic** - Don't repeat yourself
7. **Eager Load Relationships** - Prevent N+1 queries
8. **Use Scopes for Filtering** - Keep query logic in models
9. **Validate Early** - Use Form Requests for input validation
10. **Log Important Actions** - Use `LogsActivity` trait

---

## 🔍 Code Examples Repository

For more examples, check:
- **User Management**: `app/Services/UserService.php`, `app/Repositories/UserRepository.php`
- **Vendor CRUD**: `Modules/Vendor/app/` - Complete CRUD with translations
- **Product Management**: `Modules/CatalogManagement/app/` - Complex with variants
- **Order Processing**: `Modules/Order/app/` - Pipeline pattern
- **API Structure**: Look in `*/Api/` folders for API implementations

---

## 📖 Additional Documentation

- [Form Error Handler](docs/FORM-ERROR-HANDLER.md)
- [CRUD Summary](Modules/CategoryManagment/CRUD_SUMMARY.md)
- [Country Code Implementation](COUNTRY_CODE_URL_IMPLEMENTATION.md)

---

## 🎯 Quick Reference

### Creating New Module
```bash
php artisan module:make ModuleName
```

### Creating Module Components
```bash
php artisan module:make-model ModelName ModuleName
php artisan module:make-controller ControllerName ModuleName
php artisan module:make-request RequestName ModuleName
php artisan module:make-migration create_table_name ModuleName
php artisan module:make-seeder SeederName ModuleName
```

### Running Migrations
```bash
php artisan migrate
php artisan module:migrate ModuleName
```

### Running Seeders
```bash
php artisan db:seed
php artisan module:seed ModuleName
```

---

**This guide should give you (and me) a complete understanding of how to work with this project architecture. When you need a new feature, just describe it, and I'll implement it following these patterns!** 🚀

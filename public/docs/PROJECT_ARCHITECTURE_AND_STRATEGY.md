# Bnaia Multi-Vendor E-Commerce Platform
## Complete Architecture, Strategy & Implementation Guide

---

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [System Architecture](#system-architecture)
4. [Module Structure](#module-structure)
5. [Core Concepts](#core-concepts)
6. [Data Flow & Cycles](#data-flow--cycles)
7. [Module Deep Dive](#module-deep-dive)
8. [Integration Points](#integration-points)
9. [Security & Authorization](#security--authorization)
10. [Performance & Caching](#performance--caching)
11. [API Documentation](#api-documentation)
12. [Development Guidelines](#development-guidelines)

---

## 🎯 Project Overview

**Project Name:** Bnaia (بنايا)  
**Type:** Multi-Vendor E-Commerce Platform  
**Framework:** Laravel 12.x with Modular Architecture  
**Languages:** English, Arabic (Full RTL Support)  
**Database:** MySQL with Redis Caching  
**Architecture Pattern:** Modular Monolith with Service-Repository Pattern

### Business Model

Bnaia operates as a **marketplace platform** connecting multiple vendors with customers:

- **Vendors** list their products on the platform
- **Customers** browse and purchase from multiple vendors in a single order
- **Platform (Bnaia)** manages the marketplace, takes commission, and handles payments
- **Multi-vendor orders** are split and fulfilled by individual vendors
- **Commission-based revenue** model with configurable rates per product/department

### Key Features

1. **Multi-Vendor Management** - Vendors manage their own products, inventory, and orders
2. **Bank Product System** - Shared product catalog that vendors can adopt
3. **Advanced Inventory** - Multi-variant products with region-based stock management
4. **Order Splitting** - Single customer order split across multiple vendors
5. **Commission System** - Flexible commission rates (product-level or department-level)
6. **Points & Rewards** - Customer loyalty points system
7. **Refund Management** - Complete refund workflow with vendor approval
8. **Financial Management** - Vendor balance tracking and withdrawal system
9. **Multi-Language** - Full English/Arabic support with RTL
10. **Role-Based Access** - Granular permissions for Admin, Vendor, Customer

---


## 🛠 Technology Stack

### Backend Technologies

| Technology | Version | Purpose |
|------------|---------|---------|
| **PHP** | ^8.2 | Server-side language |
| **Laravel** | ^12.18 | Web application framework |
| **MySQL** | 8.0+ | Primary database |
| **Redis** | Latest | Caching & sessions |
| **Laravel Sanctum** | ^4.0 | API authentication |
| **nwidart/laravel-modules** | ^12.0 | Modular architecture |
| **yajra/laravel-datatables** | 12.0 | Server-side datatables |
| **maatwebsite/excel** | ^3.1.55 | Excel import/export |
| **mcamara/laravel-localization** | ^2.3 | Multi-language routing |
| **kreait/firebase-php** | 7.9 | Push notifications |
| **Predis** | ^3.3 | Redis client for PHP |

### Frontend Technologies

| Technology | Purpose |
|------------|---------|
| **Bootstrap 5** | CSS framework |
| **jQuery** | JavaScript library |
| **Vite** | Asset bundling |
| **DataTables.net** | Interactive tables |
| **Select2** | Enhanced select boxes |
| **Unicons** | Icon library |
| **Chart.js** | Data visualization |

### Development Tools

- **Composer** - PHP dependency management
- **NPM** - JavaScript package management
- **Laravel Mix/Vite** - Asset compilation
- **PHPUnit** - Testing framework
- **Laravel Debugbar** - Development debugging

---

## 🏗 System Architecture

### Architectural Pattern: Modular Monolith

The system follows a **modular monolith** architecture where:

1. **Core Application** (`app/`) - Shared functionality, base models, services
2. **Modules** (`Modules/`) - Self-contained business domains
3. **Shared Resources** - Database, cache, authentication

```
┌─────────────────────────────────────────────────────────┐
│                    Laravel Application                   │
├─────────────────────────────────────────────────────────┤
│  Core Layer (app/)                                       │
│  ├── Authentication & Authorization                      │
│  ├── Base Models (User, Role, Permission)               │
│  ├── Shared Services (CacheService, UserService)        │
│  ├── Global Helpers & Traits                            │
│  └── Common Middleware                                   │
├─────────────────────────────────────────────────────────┤
│  Module Layer (Modules/)                                 │
│  ├── AreaSettings      ├── Order                        │
│  ├── CatalogManagement ├── Refund                       │
│  ├── CategoryManagment ├── Report                       │
│  ├── Customer          ├── SystemSetting                │
│  ├── Vendor            ├── Withdraw                     │
│  └── Accounting                                          │
├─────────────────────────────────────────────────────────┤
│  Infrastructure Layer                                    │
│  ├── MySQL Database                                      │
│  ├── Redis Cache                                         │
│  ├── File Storage                                        │
│  └── External APIs (Firebase, Payment Gateways)         │
└─────────────────────────────────────────────────────────┘
```

### Design Patterns Used

#### 1. Repository Pattern
Separates data access logic from business logic.

```php
// Repository handles database queries
class ProductRepository {
    public function findById($id) {
        return Product::with('translations', 'mainImage')->find($id);
    }
}

// Service uses repository
class ProductService {
    public function __construct(
        private ProductRepository $repository
    ) {}
    
    public function getProduct($id) {
        return $this->repository->findById($id);
    }
}
```

#### 2. Service Layer Pattern
Encapsulates business logic.

```php
class OrderService {
    public function createOrder($data) {
        // Complex business logic
        // - Validate stock
        // - Calculate totals
        // - Apply discounts
        // - Create order
        // - Book stock
        // - Send notifications
    }
}
```

#### 3. Observer Pattern
Handles model events and side effects.

```php
class ProductObserver {
    public function created(Product $product) {
        // Clear cache
        // Send notifications
        // Log activity
    }
}
```

#### 4. Resource/Transformer Pattern
Formats API responses consistently.

```php
class ProductResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'title' => $this->getTranslation('title'),
            'price' => $this->price,
            'image' => $this->mainImage?->url,
        ];
    }
}
```


#### 5. Polymorphic Relationships
Reusable models for attachments and translations.

```php
// Attachment model works with any model
class Product extends Model {
    public function mainImage() {
        return $this->morphOne(Attachment::class, 'attachable')
                    ->where('type', 'main_image');
    }
}

// Translation model works with any model
class Category extends Model {
    use Translation;
    
    public function translations() {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
```

---

## 📦 Module Structure

### Module Organization

Each module follows a consistent structure:

```
Modules/ModuleName/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # HTTP request handlers
│   │   ├── Requests/         # Form validation
│   │   └── Resources/        # API response transformers
│   ├── Models/               # Eloquent models
│   ├── Repositories/         # Data access layer
│   ├── Services/             # Business logic
│   ├── Observers/            # Model event handlers
│   ├── Exports/              # Excel export classes
│   ├── Imports/              # Excel import classes
│   └── Providers/            # Service providers
├── config/                   # Module configuration
├── database/
│   ├── migrations/           # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── lang/                     # Translation files
│   ├── en/                   # English translations
│   └── ar/                   # Arabic translations
├── resources/
│   ├── views/                # Blade templates
│   └── assets/               # CSS/JS assets
├── routes/
│   ├── web.php               # Web routes
│   └── api.php               # API routes
├── tests/                    # Module tests
├── module.json               # Module metadata
└── composer.json             # Module dependencies
```

### Active Modules

| Module | Purpose | Key Models |
|--------|---------|------------|
| **AreaSettings** | Geographic data management | Country, Region, City |
| **CatalogManagement** | Product catalog & inventory | Product, VendorProduct, VendorProductVariant, Stock |
| **CategoryManagment** | Category hierarchy | Department, Category, SubCategory |
| **Customer** | Customer management & API | Customer, Address |
| **Order** | Order processing & fulfillment | Order, OrderProduct, OrderStage |
| **Refund** | Return & refund management | RefundRequest, RefundRequestItem |
| **Vendor** | Vendor management | Vendor, VendorRequest |
| **Withdraw** | Financial withdrawals | Transaction, WithdrawRequest |
| **Accounting** | Financial accounting | AccountingEntry |
| **Report** | Analytics & reporting | Various aggregations |
| **SystemSetting** | Platform configuration | Slider, FAQ, Ad |

---

## 🔑 Core Concepts

### 1. Multi-Language System

#### How It Works

Every translatable content is stored in a polymorphic `translations` table:

```sql
translations
├── id
├── translatable_type  (e.g., 'Modules\CatalogManagement\app\Models\Product')
├── translatable_id    (e.g., product ID)
├── lang_id            (foreign key to languages table)
├── lang_key           (e.g., 'title', 'description')
├── lang_value         (the actual translated text)
└── timestamps
```

#### Usage Example

```php
// Storing translations
$product = Product::create([...]);

$product->translations()->create([
    'lang_id' => 1, // English
    'lang_key' => 'title',
    'lang_value' => 'Product Title'
]);

$product->translations()->create([
    'lang_id' => 2, // Arabic
    'lang_key' => 'title',
    'lang_value' => 'عنوان المنتج'
]);

// Retrieving translations
$title = $product->getTranslation('title', app()->getLocale());
$titleEn = $product->getTranslation('title', 'en');
$titleAr = $product->getTranslation('title', 'ar');
```

#### Common Translation Keys

- **Products**: `title`, `details`, `summary`, `features`, `instructions`, `meta_title`, `meta_description`
- **Categories**: `name`, `description`
- **Brands**: `name`, `description`
- **Vendors**: `name`, `description`, `about`


### 2. File Attachment System

#### How It Works

Files (images, documents) are stored using a polymorphic `attachments` table:

```sql
attachments
├── id
├── attachable_type  (e.g., 'Modules\CatalogManagement\app\Models\Product')
├── attachable_id    (e.g., product ID)
├── type             (e.g., 'main_image', 'additional_image', 'document')
├── path             (file path in storage)
└── timestamps
```

#### Usage Example

```php
// Storing attachments
if ($request->hasFile('main_image')) {
    $path = $request->file('main_image')->store('products', 'public');
    
    $product->attachments()->create([
        'type' => 'main_image',
        'path' => $path
    ]);
}

// Retrieving attachments
$mainImage = $product->mainImage; // morphOne relationship
$imageUrl = asset('storage/' . $mainImage->path);

$additionalImages = $product->additionalImages; // morphMany relationship
```

#### Common Attachment Types

- **Products**: `main_image`, `additional_image`
- **Vendors**: `logo`, `banner`, `document`
- **Categories**: `icon`, `banner`

### 3. Bank Product System

#### Concept

The **Bank Product** system is a unique feature where:

1. **Admin creates "Bank Products"** - A shared catalog of products
2. **Vendors adopt products** - Vendors create `VendorProduct` instances from bank products
3. **Vendors set their own pricing** - Each vendor can have different prices for the same product
4. **Vendors manage their own stock** - Independent inventory management

#### Product Hierarchy

```
Product (Bank Product - Admin Created)
  ↓ adopted by
VendorProduct (Vendor's Instance)
  ↓ has variants
VendorProductVariant (Size, Color, etc.)
  ↓ has stock per region
VendorProductVariantStock (Region-specific inventory)
```

#### Example Flow

```
1. Admin creates Bank Product:
   - Product: "iPhone 15 Pro"
   - SKU: "IPHONE15PRO"
   - Category: Electronics
   
2. Vendor A adopts product:
   - VendorProduct: Links to "iPhone 15 Pro"
   - Price: 45,000 EGP
   - Commission: 10%
   
3. Vendor A creates variants:
   - Variant 1: 128GB, Black
   - Variant 2: 256GB, White
   
4. Vendor A sets stock per region:
   - Cairo: 10 units (128GB Black)
   - Alexandria: 5 units (128GB Black)
```

### 4. Multi-Variant Product System

#### Variant Configuration

Products can have multiple variant types (Size, Color, Material, etc.):

```sql
variants_configurations
├── id
├── product_id
├── key (e.g., 'size', 'color')
└── timestamps

variant_configuration_keys
├── id
├── variant_configuration_id
├── key (e.g., 'small', 'red')
├── value (display value)
└── timestamps
```

#### Variant Combinations

Each `VendorProductVariant` represents a unique combination:

```php
// Example: T-Shirt with Size and Color
Variant 1: Size=Small, Color=Red
Variant 2: Size=Small, Color=Blue
Variant 3: Size=Large, Color=Red
Variant 4: Size=Large, Color=Blue
```

#### Stock Management

Stock is tracked per variant per region:

```sql
vendor_product_variant_stocks
├── id
├── vendor_product_variant_id
├── region_id
├── total_stock
├── booked_stock
├── allocated_stock
├── fulfilled_stock
└── remaining_stock (calculated)
```


### 5. Stock Booking System

#### Stock States

```
┌─────────────┐
│ Total Stock │ = 100 units
└─────────────┘
       │
       ├─→ Booked (20)      - Reserved for pending orders
       ├─→ Allocated (30)   - Being prepared for delivery
       ├─→ Fulfilled (40)   - Already delivered
       └─→ Remaining (10)   - Available for new orders
```

#### Stock Booking Flow

```
1. Customer places order
   → Stock status: BOOKED
   → remaining_stock decreases
   
2. Vendor confirms order
   → Stock status: ALLOCATED
   → booked_stock decreases, allocated_stock increases
   
3. Order delivered
   → Stock status: FULFILLED
   → allocated_stock decreases, fulfilled_stock increases
   
4. Order cancelled
   → Stock status: RELEASED
   → Stock returns to remaining_stock
```

#### Stock Calculation

```php
remaining_stock = total_stock - booked_stock - allocated_stock - fulfilled_stock
```

### 6. Commission System

#### Commission Calculation

Commission can be set at two levels:

1. **Product Level** - Specific commission for a product
2. **Department Level** - Default commission for all products in a department

```php
// Priority: Product commission > Department commission
$commission = $vendorProduct->commission 
              ?? $vendorProduct->product->department->commission 
              ?? 0;

$commissionAmount = ($productPrice * $commission) / 100;
```

#### Commission in Orders

```sql
order_products
├── price (product price)
├── commission_percentage (stored at order time)
└── commission_amount (calculated: price * commission_percentage / 100)
```

#### Vendor Balance Calculation

```php
// Vendor's earnings from delivered orders
$ordersPrice = sum(order_products.price + shipping_cost)
               WHERE vendor_order_stages.stage = 'deliver'
               AND is_refunded = false;

// Platform's commission
$bnaiaCommission = sum(order_products.commission_amount)
                   WHERE vendor_order_stages.stage = 'deliver'
                   AND is_refunded = false;

// Vendor's available balance
$totalBalance = $ordersPrice - $bnaiaCommission;

// Vendor's remaining balance (after withdrawals)
$totalRemaining = $totalBalance - $totalSent;
```

---

## 🔄 Data Flow & Cycles

### Order Lifecycle

```
┌──────────────────────────────────────────────────────────────┐
│                     ORDER LIFECYCLE                           │
└──────────────────────────────────────────────────────────────┘

1. ORDER CREATION
   ├─→ Customer adds products to cart (multiple vendors)
   ├─→ Customer proceeds to checkout
   ├─→ System calculates:
   │   ├─→ Product prices
   │   ├─→ Shipping costs (per vendor)
   │   ├─→ Taxes
   │   ├─→ Discounts (promo codes, vendor discounts)
   │   ├─→ Points usage
   │   └─→ Final total
   ├─→ Order created with status: NEW
   ├─→ Stock BOOKED for all products
   └─→ Notifications sent to vendors

2. VENDOR PROCESSING
   ├─→ Vendor receives notification
   ├─→ Vendor reviews order items
   ├─→ Vendor changes status to: IN_PROGRESS
   ├─→ Stock changes from BOOKED to ALLOCATED
   ├─→ Vendor prepares items for delivery
   └─→ Vendor marks as ready for delivery

3. DELIVERY
   ├─→ Delivery driver picks up items
   ├─→ Driver delivers to customer
   ├─→ Vendor changes status to: DELIVERED
   ├─→ Stock changes from ALLOCATED to FULFILLED
   ├─→ Customer earns loyalty points
   ├─→ Vendor balance updated
   └─→ Commission calculated and recorded

4. POST-DELIVERY
   ├─→ Customer can review products
   ├─→ Customer can request refund (if eligible)
   └─→ Order marked as complete

5. CANCELLATION (Can happen at any stage before delivery)
   ├─→ Customer/Vendor/Admin cancels order
   ├─→ Stock RELEASED back to inventory
   ├─→ Payment refunded (if paid)
   ├─→ Points returned (if used)
   └─→ Order marked as CANCELLED
```


### Refund Lifecycle

```
┌──────────────────────────────────────────────────────────────┐
│                    REFUND LIFECYCLE                           │
└──────────────────────────────────────────────────────────────┘

1. REFUND REQUEST
   ├─→ Customer views delivered orders
   ├─→ Customer selects products to refund
   ├─→ System checks refund eligibility:
   │   ├─→ Global refund enabled?
   │   ├─→ Product refundable?
   │   ├─→ Within refund period?
   │   └─→ Not already refunded?
   ├─→ System calculates refund amount:
   │   ├─→ Product price + tax
   │   ├─→ Shipping (if enabled)
   │   ├─→ Proportional discounts
   │   ├─→ Return shipping cost
   │   └─→ Points adjustments
   ├─→ Refund request created (status: PENDING)
   └─→ Vendor notified

2. VENDOR REVIEW
   ├─→ Vendor reviews refund request
   ├─→ Vendor can:
   │   ├─→ APPROVE - Accept refund
   │   └─→ REJECT - Decline with reason
   └─→ Customer notified of decision

3. RETURN PROCESSING (If approved)
   ├─→ Status: IN_PROGRESS
   ├─→ Driver scheduled to pick up items
   ├─→ Status: PICKED_UP
   ├─→ Vendor receives returned items
   └─→ Vendor inspects items

4. REFUND COMPLETION
   ├─→ Status: REFUNDED
   ├─→ Customer refunded via original payment method
   ├─→ Points adjusted:
   │   ├─→ Earned points deducted
   │   └─→ Used points returned
   ├─→ Vendor balance adjusted
   ├─→ Commission reversed
   ├─→ Stock returned to inventory
   └─→ Order products marked as refunded

5. FINANCIAL IMPACT
   ├─→ Vendor Balance:
   │   └─→ Automatically recalculated (excludes refunded products)
   ├─→ Platform Commission:
   │   └─→ Automatically recalculated (excludes refunded products)
   └─→ Customer Account:
       ├─→ Money refunded
       └─→ Points adjusted
```

### Withdrawal Lifecycle

```
┌──────────────────────────────────────────────────────────────┐
│                  WITHDRAWAL LIFECYCLE                         │
└──────────────────────────────────────────────────────────────┘

1. VENDOR REQUESTS WITHDRAWAL
   ├─→ Vendor checks available balance
   ├─→ Vendor submits withdrawal request
   ├─→ System validates:
   │   ├─→ Sufficient balance?
   │   ├─→ Minimum withdrawal amount met?
   │   └─→ No pending withdrawals?
   ├─→ Withdrawal request created (status: PENDING)
   └─→ Admin notified

2. ADMIN REVIEW
   ├─→ Admin reviews withdrawal request
   ├─→ Admin verifies vendor bank details
   ├─→ Admin can:
   │   ├─→ APPROVE - Process withdrawal
   │   └─→ REJECT - Decline with reason
   └─→ Vendor notified

3. PAYMENT PROCESSING (If approved)
   ├─→ Status: PROCESSING
   ├─→ Finance team initiates bank transfer
   ├─→ Payment sent to vendor's bank account
   └─→ Status: COMPLETED

4. BALANCE UPDATE
   ├─→ Withdrawal amount added to total_sent
   ├─→ Vendor's remaining balance updated
   ├─→ Transaction recorded
   └─→ Vendor notified of completion
```

### Points System Lifecycle

```
┌──────────────────────────────────────────────────────────────┐
│                   POINTS LIFECYCLE                            │
└──────────────────────────────────────────────────────────────┘

1. EARNING POINTS
   ├─→ Customer completes order (status: DELIVERED)
   ├─→ System calculates points earned:
   │   └─→ points = order_total * points_per_currency
   ├─→ Points added to customer account
   ├─→ Transaction recorded (type: EARNED)
   └─→ Customer notified

2. USING POINTS
   ├─→ Customer applies points at checkout
   ├─→ System calculates discount:
   │   └─→ discount = points_used * currency_per_point
   ├─→ Order total reduced
   ├─→ Points deducted from customer account
   ├─→ Transaction recorded (type: USED)
   └─→ Order created with points_used field

3. POINTS ADJUSTMENT (Refund)
   ├─→ Customer refunds order
   ├─→ System adjusts points:
   │   ├─→ Deduct earned points from refunded products
   │   └─→ Return used points from refunded products
   ├─→ Transactions recorded
   └─→ Customer notified

4. POINTS EXPIRATION (If configured)
   ├─→ Scheduled job runs daily
   ├─→ Identifies expired points
   ├─→ Deducts expired points
   ├─→ Transaction recorded (type: EXPIRED)
   └─→ Customer notified
```


---

## 📚 Module Deep Dive

### AreaSettings Module

**Purpose:** Manage geographic data (Countries, Regions, Cities)

**Key Models:**
- `Country` - Countries with translations
- `Region` - States/Provinces within countries
- `City` - Cities within regions

**Features:**
- Multi-language support for geographic names
- Hierarchical structure (Country → Region → City)
- Active/inactive status for each level
- Default region/city selection
- **Caching system** for API performance

**API Endpoints:**
```
GET /api/area/countries
GET /api/area/regions?country_id={id}
GET /api/area/cities?region_id={id}
```

**Cache Strategy:**
- Cache key pattern: `countryapi:*`, `regionapi:*`, `cityapi:*`
- TTL: 1 hour (3600 seconds)
- Auto-invalidation on create/update/delete via observers
- Redis database: 1

---

### CatalogManagement Module

**Purpose:** Complete product catalog and inventory management

**Key Models:**

1. **Product (Bank Product)**
   - Admin-created shared catalog
   - SKU-based identification
   - Category hierarchy (Department → Category → SubCategory)
   - Multi-language content
   - Multiple images
   - Variant configurations

2. **VendorProduct**
   - Vendor's instance of a bank product
   - Custom pricing per vendor
   - Commission settings
   - Refund settings (is_able_to_refund, refund_days)
   - Active/inactive status
   - Featured/promoted flags

3. **VendorProductVariant**
   - Specific variant combination (e.g., Size=Large, Color=Red)
   - Variant-specific pricing
   - Variant-specific images
   - SKU per variant
   - Weight and dimensions

4. **VendorProductVariantStock**
   - Stock per variant per region
   - Stock booking states (booked, allocated, fulfilled)
   - Remaining stock calculation
   - Stock alerts

5. **Brand**
   - Product brands
   - Multi-language names and descriptions
   - Brand logo
   - Active/inactive status

6. **Tax**
   - Tax configurations
   - Percentage-based
   - Applied at product level

7. **Promocode**
   - Discount codes
   - Percentage or fixed amount
   - Usage limits
   - Expiration dates
   - Minimum order amount

8. **Occasion**
   - Special sales events
   - Date ranges
   - Featured products
   - Banners and images

9. **Bundle**
   - Product bundles
   - Bundle pricing
   - Bundle categories
   - Multi-product packages

10. **Review**
    - Customer product reviews
    - Star ratings
    - Review text
    - Verified purchase flag

**Features:**
- **Excel Import/Export** - Bulk product management
- **Multi-sheet structure** - Products, Images, Variants, Stock, Occasions
- **SKU-based sync** - Update existing products via SKU
- **Variant management** - Complex variant configurations
- **Stock tracking** - Real-time inventory management
- **Image management** - Multiple images per product/variant
- **Drag & drop sorting** - Custom product order
- **Cache system** - Bundle and category caching

**Import/Export Structure:**
```
Excel File:
├── Sheet 1: products (SKU, name, price, category, etc.)
├── Sheet 2: images (SKU, image URLs)
├── Sheet 3: variants (SKU, variant keys, price, stock)
├── Sheet 4: variant_stock (SKU, variant, region, stock)
├── Sheet 5: occasions (admin only)
└── Sheet 6: occasion_products (admin only)
```


---

### CategoryManagment Module

**Purpose:** Hierarchical category structure

**Key Models:**
- `Department` - Top-level categories (e.g., Electronics, Fashion)
- `Category` - Main categories (e.g., Smartphones, Laptops)
- `SubCategory` - Sub-categories (e.g., iPhone, Samsung)

**Hierarchy:**
```
Department (Electronics)
  └── Category (Smartphones)
      ├── SubCategory (iPhone)
      ├── SubCategory (Samsung)
      └── SubCategory (Huawei)
```

**Features:**
- Multi-language names and descriptions
- Category icons and banners
- Active/inactive status
- Drag & drop sorting
- Commission settings per department

---

### Customer Module

**Purpose:** Customer management and mobile API

**Key Models:**
- `Customer` - Customer accounts
- `Address` - Delivery addresses

**Features:**
- Customer registration and authentication (Sanctum)
- Profile management
- Multiple delivery addresses
- Order history
- Points balance
- Wishlist
- Reviews and ratings

**API Endpoints:**
```
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
GET  /api/auth/profile
PUT  /api/auth/update-profile
GET  /api/addresses
POST /api/addresses
PUT  /api/addresses/{id}
DELETE /api/addresses/{id}
```

**API Features:**
- Multi-language support via `lang` header
- Country-specific content via `X-Country-Code` header
- Token-based authentication
- JSON responses with consistent structure

---

### Order Module

**Purpose:** Complete order processing and fulfillment

**Key Models:**

1. **Order**
   - Customer order
   - Multiple vendors per order
   - Payment information
   - Shipping details
   - Points usage
   - Promo code application
   - Order totals and breakdowns

2. **OrderProduct**
   - Individual line items
   - Product details snapshot
   - Vendor assignment
   - Pricing and commission
   - Shipping cost per product
   - Refund tracking

3. **OrderStage**
   - Global order stages (new, in-progress, deliver, cancel, etc.)
   - Stage transitions configuration
   - Stage colors and icons

4. **VendorOrderStage**
   - Vendor-specific order status
   - Each vendor tracks their items independently
   - Stage history

5. **OrderFulfillment**
   - Delivery tracking
   - Driver assignment
   - Delivery notes
   - Proof of delivery

6. **OrderStatusHistory**
   - Complete audit trail
   - Status change timestamps
   - User who made changes
   - Notes and reasons

**Order Splitting:**
When a customer orders from multiple vendors:
```
Customer Order #1234
├── Vendor A Items (OrderProducts with vendor_id = A)
│   └── VendorOrderStage (tracks Vendor A's status)
├── Vendor B Items (OrderProducts with vendor_id = B)
│   └── VendorOrderStage (tracks Vendor B's status)
└── Vendor C Items (OrderProducts with vendor_id = C)
    └── VendorOrderStage (tracks Vendor C's status)
```

**Order Stage Transitions:**
Configured in `config/order_stage_transitions.php`:
```php
'new' => ['in-progress', 'cancel', 'want-to-return'],
'in-progress' => ['deliver', 'cancel'],
'deliver' => ['want-to-return', 'in-progress-return', 'refund'],
```

**Features:**
- Multi-vendor order splitting
- Independent vendor fulfillment
- Stock booking and allocation
- Commission calculation
- Points earning and usage
- Promo code application
- Shipping cost calculation
- Tax calculation
- Order notifications
- Status history tracking


---

### Refund Module

**Purpose:** Complete refund and return management

**Key Models:**

1. **RefundSettings**
   - Global refund configuration
   - Enable/disable refunds
   - Return shipping policy
   - Original shipping refund policy
   - Default refund days

2. **RefundRequest**
   - One request per vendor per order
   - Refund calculations
   - Status tracking (pending, approved, in_progress, picked_up, refunded, rejected)
   - Financial breakdowns
   - Points adjustments

3. **RefundRequestItem**
   - Individual products being refunded
   - Quantity and pricing
   - Tax and shipping amounts
   - Refund calculations

**Refund Eligibility:**
```php
Product is refundable if:
1. Global refund_enabled = true
2. Product is_able_to_refund = true
3. Within refund period:
   - Use product's refund_days if set
   - Otherwise use global refund_processing_days
4. Order status = delivered
5. Product not already refunded
```

**Refund Calculation:**
```
Base Refund = Products + Tax + Shipping - Product Discounts
+ Proportional Vendor Discounts (refunded)
+ Proportional Promo Code Discount (refunded)
- Proportional Vendor Fees (not refunded)
- Return Shipping Cost (if customer pays)
- Points Value Used (refunded separately as points)
= Total Refund Amount
```

**Points Adjustment:**
```
Points to Return = Points used to pay for these products
Points to Deduct = Points earned from these products
Net Points Change = Points to Return - Points to Deduct
```

**Vendor Balance Impact:**
```
When refund is completed:
1. Order products marked as is_refunded = true
2. Vendor balance automatically recalculated:
   - orders_price excludes refunded products
   - bnaia_commission excludes refunded products
   - total_balance = orders_price - bnaia_commission
   - total_remaining = total_balance - total_sent
```

**Features:**
- Product-level refund control
- Custom refund periods per product
- Automatic refund calculations
- Multi-vendor refund handling
- Points adjustment
- Commission reversal
- Stock return
- Vendor approval workflow
- Return shipping calculation
- Financial impact tracking

---

### Vendor Module

**Purpose:** Vendor account and business management

**Key Models:**
- `Vendor` - Vendor business accounts
- `VendorRequest` - Vendor registration requests

**Vendor Information:**
- Business details
- Contact information
- Bank account details
- Logo and banner
- Business documents
- Department associations
- Active/inactive status

**Vendor Financial Data:**
```php
// Calculated dynamically from delivered orders
$ordersPrice = sum(order_products.price + shipping_cost)
               WHERE vendor_order_stages.stage = 'deliver'
               AND is_refunded = false;

$bnaiaCommission = sum(order_products.commission_amount)
                   WHERE vendor_order_stages.stage = 'deliver'
                   AND is_refunded = false;

$totalBalance = $ordersPrice - $bnaiaCommission;
$totalSent = sum(withdraw_requests.amount WHERE status = 'completed');
$totalRemaining = $totalBalance - $totalSent;
```

**Features:**
- Vendor registration and approval
- Business profile management
- Product management
- Order management
- Financial dashboard
- Withdrawal requests
- Performance analytics
- Notification preferences


---

### Withdraw Module

**Purpose:** Vendor financial withdrawals

**Key Models:**

1. **Transaction**
   - All financial transactions
   - Types: order, withdrawal, refund, commission
   - Amount and currency
   - Related models (order, withdrawal, etc.)

2. **WithdrawRequest**
   - Vendor withdrawal requests
   - Amount requested
   - Status (pending, approved, processing, completed, rejected)
   - Bank details
   - Admin notes

**Withdrawal Flow:**
```
1. Vendor checks available balance (total_remaining)
2. Vendor submits withdrawal request
3. Admin reviews and approves/rejects
4. Finance team processes payment
5. Status updated to completed
6. Vendor's total_sent updated
7. Vendor's total_remaining recalculated
```

**Validation Rules:**
- Minimum withdrawal amount
- Sufficient balance
- No pending withdrawals
- Valid bank account details
- Vendor account active

---

### Accounting Module

**Purpose:** Financial accounting and reporting

**Key Models:**
- `AccountingEntry` - Double-entry accounting records

**Features:**
- Transaction recording
- Financial reports
- Revenue tracking
- Commission tracking
- Vendor payouts
- Tax calculations
- Profit/loss statements

---

### Report Module

**Purpose:** Analytics and business intelligence

**Features:**
- Sales reports
- Vendor performance
- Product analytics
- Customer insights
- Revenue reports
- Commission reports
- Refund statistics
- Inventory reports

**Report Types:**
- Daily/Weekly/Monthly/Yearly
- By vendor
- By product
- By category
- By region
- Custom date ranges

---

### SystemSetting Module

**Purpose:** Platform configuration and content management

**Key Models:**
- `Slider` - Homepage sliders
- `FAQ` - Frequently asked questions
- `Ad` - Advertisements
- `FooterContent` - Footer links and content

**Features:**
- Homepage customization
- Content management
- Platform settings
- Email templates
- Notification templates
- Payment gateway configuration
- Shipping method configuration

---

## 🔗 Integration Points

### Module Dependencies

```
┌─────────────────────────────────────────────────────────────┐
│                   MODULE DEPENDENCIES                        │
└─────────────────────────────────────────────────────────────┘

Order Module
├─→ Customer (customer data)
├─→ Vendor (vendor data)
├─→ CatalogManagement (products, variants, stock)
├─→ AreaSettings (shipping regions)
└─→ Accounting (financial records)

Refund Module
├─→ Order (order data, order products)
├─→ Customer (customer points)
├─→ Vendor (vendor balance)
├─→ CatalogManagement (product refund settings)
└─→ Accounting (refund transactions)

Withdraw Module
├─→ Vendor (vendor balance)
├─→ Order (delivered orders)
└─→ Accounting (withdrawal transactions)

CatalogManagement Module
├─→ CategoryManagment (product categories)
├─→ Vendor (vendor products)
└─→ AreaSettings (stock regions)

Customer Module
├─→ Order (order history)
├─→ AreaSettings (addresses)
└─→ CatalogManagement (wishlist, reviews)
```

### Cross-Module Communication

**1. Events & Listeners**
```php
// Order delivered event
event(new OrderDelivered($order));

// Listeners:
- UpdateVendorBalance
- AwardCustomerPoints
- SendDeliveryNotification
- UpdateStockBooking
```

**2. Observers**
```php
// Product observer
class ProductObserver {
    public function updated(Product $product) {
        // Clear cache
        Cache::tags(['products'])->flush();
        
        // Notify vendors
        event(new ProductUpdated($product));
    }
}
```

**3. Service Layer**
```php
// OrderService uses multiple repositories
class OrderService {
    public function __construct(
        private OrderRepository $orderRepo,
        private ProductRepository $productRepo,
        private StockRepository $stockRepo,
        private CustomerRepository $customerRepo
    ) {}
}
```


---

## 🔐 Security & Authorization

### Authentication

**Admin/Vendor:** Session-based authentication
```php
// Login
Auth::attempt($credentials);

// Check authentication
if (Auth::check()) {
    $user = Auth::user();
}
```

**Customer API:** Token-based authentication (Sanctum)
```php
// Login - returns token
$token = $customer->createToken('mobile-app')->plainTextToken;

// API request with token
Authorization: Bearer {token}
```

### Authorization (RBAC)

**Role-Based Access Control:**
```
User
├─→ has many Roles
│   └─→ has many Permissions
└─→ can perform actions based on permissions
```

**Permission Format:** `resource.action`
```
Examples:
- products.view
- products.create
- products.edit
- products.delete
- orders.view
- orders.manage
- vendors.approve
```

**Checking Permissions:**
```php
// In controller
if (!auth()->user()->can('products.create')) {
    abort(403);
}

// In blade
@can('products.create')
    <button>Create Product</button>
@endcan

// In policy
public function create(User $user) {
    return $user->hasPermission('products.create');
}
```

**User Types:**
1. **Super Admin** - All permissions
2. **Admin** - Configurable permissions
3. **Vendor** - Vendor-specific permissions
4. **Customer** - Customer-specific permissions

### Data Isolation

**Vendor Data Isolation:**
```php
// Vendors can only see their own data
$products = VendorProduct::where('vendor_id', auth()->user()->vendor_id)->get();

// Global scope
class VendorProduct extends Model {
    protected static function booted() {
        static::addGlobalScope('vendor', function ($query) {
            if (auth()->user()->isVendor()) {
                $query->where('vendor_id', auth()->user()->vendor_id);
            }
        });
    }
}
```

**Customer Data Isolation:**
```php
// Customers can only see their own data
$orders = Order::where('customer_id', auth()->id())->get();
```

### Input Validation

**Form Requests:**
```php
class CreateProductRequest extends FormRequest {
    public function rules() {
        return [
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
```

**API Validation:**
```php
$validated = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

### Rate Limiting

**API Rate Limits:**
```php
// config/app.php or RouteServiceProvider
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

// Authentication endpoints
RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});
```

### SQL Injection Prevention

**Always use parameter binding:**
```php
// ✅ Good - Parameter binding
DB::table('users')->where('email', $email)->first();

// ❌ Bad - String concatenation
DB::select("SELECT * FROM users WHERE email = '$email'");
```

### XSS Prevention

**Blade automatically escapes output:**
```blade
{{-- ✅ Escaped --}}
{{ $userInput }}

{{-- ❌ Unescaped (only for trusted content) --}}
{!! $trustedHtml !!}
```

### CSRF Protection

**All POST/PUT/DELETE requests require CSRF token:**
```blade
<form method="POST">
    @csrf
    <!-- form fields -->
</form>
```

---

## ⚡ Performance & Caching

### Caching Strategy

**Cache Driver:** Redis (Database 1)

**Cache Layers:**

1. **Query Result Caching**
```php
$countries = Cache::remember('countries:all', 3600, function () {
    return Country::with('translations')->where('active', true)->get();
});
```

2. **Model Caching**
```php
class Country extends Model {
    use Cacheable;
    
    protected $cacheFor = 3600; // 1 hour
}
```

3. **API Response Caching**
```php
// Middleware
public function handle($request, Closure $next) {
    $key = 'api:' . $request->fullUrl();
    
    if (Cache::has($key)) {
        return response()->json(Cache::get($key));
    }
    
    $response = $next($request);
    Cache::put($key, $response->getData(), 3600);
    
    return $response;
}
```

**Cache Invalidation:**
```php
// Observer pattern
class CountryObserver {
    public function saved(Country $country) {
        Cache::tags(['countries'])->flush();
        // or specific pattern
        app(CacheService::class)->forgetByPattern('countryapi:*');
    }
}
```

**Cache Keys Pattern:**
```
Module:Resource:Action:Parameters
Examples:
- countryapi:all:hash
- cityapi:region:5:hash
- bundleapi:category:3:hash
```


### Database Optimization

**1. Indexes**
```sql
-- Foreign keys
INDEX idx_order_products_order_id (order_id)
INDEX idx_order_products_vendor_id (vendor_id)

-- Frequently queried columns
INDEX idx_products_sku (sku)
INDEX idx_orders_status (status)
INDEX idx_vendors_active (active)

-- Composite indexes
INDEX idx_stock_variant_region (vendor_product_variant_id, region_id)
```

**2. Eager Loading**
```php
// ❌ N+1 Query Problem
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name; // Query per product
}

// ✅ Eager Loading
$products = Product::with('category')->get();
foreach ($products as $product) {
    echo $product->category->name; // No additional queries
}
```

**3. Query Optimization**
```php
// ✅ Select only needed columns
Product::select('id', 'title', 'price')->get();

// ✅ Chunk large datasets
Product::chunk(100, function ($products) {
    foreach ($products as $product) {
        // Process
    }
});

// ✅ Use exists() instead of count()
if (Product::where('sku', $sku)->exists()) {
    // SKU exists
}
```

### Asset Optimization

**1. Vite Build**
```bash
npm run build
```

**2. Image Optimization**
- Compress images before upload
- Use appropriate formats (WebP for web)
- Lazy loading for images
- CDN for static assets

**3. CSS/JS Minification**
- Automatic via Vite
- Remove unused CSS
- Code splitting

### Queue System

**Background Jobs:**
```php
// Dispatch job
SendOrderNotification::dispatch($order);

// Job class
class SendOrderNotification implements ShouldQueue {
    public function handle() {
        // Send notification
    }
}
```

**Queue Configuration:**
```env
QUEUE_CONNECTION=redis
```

**Common Queued Jobs:**
- Email notifications
- SMS notifications
- Push notifications
- Excel exports
- Image processing
- Report generation

---

## 📡 API Documentation

### API Structure

**Base URL:** `https://domain.com/api`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
lang: en|ar
X-Country-Code: EG
```

**Response Format:**
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Response data
    },
    "meta": {
        "current_page": 1,
        "total": 100,
        "per_page": 15
    }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### Authentication Endpoints

```
POST /api/auth/register
Body: {
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+201234567890"
}
Response: {
    "success": true,
    "data": {
        "customer": {...},
        "token": "1|abc123..."
    }
}

POST /api/auth/login
Body: {
    "email": "john@example.com",
    "password": "password123"
}
Response: {
    "success": true,
    "data": {
        "customer": {...},
        "token": "1|abc123..."
    }
}

POST /api/auth/logout
Headers: Authorization: Bearer {token}
Response: {
    "success": true,
    "message": "Logged out successfully"
}

GET /api/auth/profile
Headers: Authorization: Bearer {token}
Response: {
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "points": 1500
    }
}
```

### Product Endpoints

```
GET /api/products
Query: ?page=1&per_page=15&category_id=5&search=phone
Response: {
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "iPhone 15 Pro",
            "price": 45000,
            "image": "https://...",
            "rating": 4.5
        }
    ],
    "meta": {...}
}

GET /api/products/{id}
Response: {
    "success": true,
    "data": {
        "id": 1,
        "title": "iPhone 15 Pro",
        "description": "...",
        "price": 45000,
        "variants": [...],
        "images": [...],
        "reviews": [...]
    }
}
```

### Order Endpoints

```
POST /api/orders
Headers: Authorization: Bearer {token}
Body: {
    "address_id": 1,
    "payment_method": "cash",
    "items": [
        {
            "vendor_product_variant_id": 5,
            "quantity": 2
        }
    ],
    "promo_code": "SAVE10",
    "points_used": 100
}
Response: {
    "success": true,
    "data": {
        "order": {...},
        "order_number": "ORD-20260129-0001"
    }
}

GET /api/orders
Headers: Authorization: Bearer {token}
Response: {
    "success": true,
    "data": [
        {
            "id": 1,
            "order_number": "ORD-20260129-0001",
            "total": 1500,
            "status": "delivered",
            "created_at": "2026-01-29 10:00:00"
        }
    ]
}

GET /api/orders/{id}
Headers: Authorization: Bearer {token}
Response: {
    "success": true,
    "data": {
        "id": 1,
        "order_number": "ORD-20260129-0001",
        "items": [...],
        "total": 1500,
        "status": "delivered"
    }
}
```

### Refund Endpoints

```
GET /api/orders/{order}/refundable-products
Headers: Authorization: Bearer {token}
Response: {
    "success": true,
    "data": {
        "refundable": true,
        "products": [
            {
                "order_product_id": 1,
                "product_name": "iPhone 15 Pro",
                "quantity": 1,
                "refundable_quantity": 1,
                "refund_deadline": "2026-02-05",
                "estimated_refund": 45000
            }
        ]
    }
}

POST /api/refund-requests
Headers: Authorization: Bearer {token}
Body: {
    "order_id": 1,
    "items": [
        {
            "order_product_id": 1,
            "quantity": 1
        }
    ],
    "reason": "Product defective",
    "notes": "Screen not working"
}
Response: {
    "success": true,
    "data": {
        "refund_request": {...},
        "refund_number": "REF-20260129-0001"
    }
}

GET /api/refund-requests
Headers: Authorization: Bearer {token}
Response: {
    "success": true,
    "data": [
        {
            "id": 1,
            "refund_number": "REF-20260129-0001",
            "status": "pending",
            "total_refund_amount": 45000
        }
    ]
}
```


---

## 💻 Development Guidelines

### Code Organization

**1. Controller Responsibilities**
```php
class ProductController extends Controller {
    public function index(Request $request) {
        // 1. Validate input
        // 2. Call service layer
        // 3. Return response
        
        $products = $this->productService->getProducts($request->all());
        return view('products.index', compact('products'));
    }
}
```

**2. Service Layer**
```php
class ProductService {
    public function __construct(
        private ProductRepository $repository,
        private CacheService $cache
    ) {}
    
    public function getProducts(array $filters) {
        // Business logic here
        return $this->repository->getFiltered($filters);
    }
}
```

**3. Repository Layer**
```php
class ProductRepository {
    public function getFiltered(array $filters) {
        $query = Product::query();
        
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        return $query->paginate(15);
    }
}
```

### Naming Conventions

**Models:** Singular, PascalCase
```php
Product, Order, Customer, VendorProduct
```

**Tables:** Plural, snake_case
```sql
products, orders, customers, vendor_products
```

**Controllers:** Singular + Controller
```php
ProductController, OrderController, CustomerController
```

**Services:** Singular + Service
```php
ProductService, OrderService, CustomerService
```

**Repositories:** Singular + Repository
```php
ProductRepository, OrderRepository, CustomerRepository
```

**Variables:** camelCase
```php
$productPrice, $orderTotal, $customerName
```

**Methods:** camelCase, descriptive
```php
getProducts(), createOrder(), updateCustomer()
```

**Constants:** UPPER_SNAKE_CASE
```php
const MAX_UPLOAD_SIZE = 2048;
const DEFAULT_CURRENCY = 'EGP';
```

### Best Practices

**1. Use Type Hints**
```php
public function createOrder(array $data): Order {
    // Implementation
}
```

**2. Use Dependency Injection**
```php
public function __construct(
    private OrderService $orderService,
    private ProductRepository $productRepository
) {}
```

**3. Use Form Requests**
```php
public function store(CreateProductRequest $request) {
    // Validation already done
    $validated = $request->validated();
}
```

**4. Use Resource Classes**
```php
return ProductResource::collection($products);
```

**5. Use Transactions**
```php
DB::transaction(function () use ($data) {
    $order = Order::create($data);
    $order->products()->attach($data['products']);
    $this->stockService->bookStock($order);
});
```

**6. Log Important Actions**
```php
Log::info('Order created', [
    'order_id' => $order->id,
    'customer_id' => $customer->id,
    'total' => $order->total
]);
```

**7. Use Queues for Heavy Tasks**
```php
SendOrderConfirmationEmail::dispatch($order);
```

**8. Handle Exceptions**
```php
try {
    $order = $this->orderService->create($data);
} catch (InsufficientStockException $e) {
    return back()->with('error', 'Insufficient stock');
} catch (\Exception $e) {
    Log::error('Order creation failed', ['error' => $e->getMessage()]);
    return back()->with('error', 'Something went wrong');
}
```

### Testing

**Unit Tests:**
```php
class ProductServiceTest extends TestCase {
    public function test_can_create_product() {
        $data = [
            'title' => 'Test Product',
            'price' => 100,
        ];
        
        $product = $this->productService->create($data);
        
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->title);
    }
}
```

**Feature Tests:**
```php
class ProductControllerTest extends TestCase {
    public function test_can_view_products() {
        $response = $this->get('/products');
        
        $response->assertStatus(200);
        $response->assertViewIs('products.index');
    }
}
```

### Git Workflow

**Branch Naming:**
```
feature/product-import
bugfix/order-calculation
hotfix/payment-gateway
```

**Commit Messages:**
```
feat: Add product import functionality
fix: Fix order total calculation
refactor: Refactor product service
docs: Update API documentation
```

### Environment Configuration

**Development:**
```env
APP_ENV=local
APP_DEBUG=true
LOG_LEVEL=debug
```

**Production:**
```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## 🚀 Deployment

### Server Requirements

- PHP >= 8.2
- MySQL >= 8.0
- Redis
- Composer
- Node.js & NPM
- Supervisor (for queue workers)

### Deployment Steps

**1. Clone Repository**
```bash
git clone https://github.com/your-repo/bnaia.git
cd bnaia
```

**2. Install Dependencies**
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

**3. Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Database Setup**
```bash
php artisan migrate --force
php artisan db:seed --force
```

**5. Storage Setup**
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

**6. Cache Optimization**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**7. Queue Worker Setup**
```bash
# Supervisor configuration
[program:bnaia-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/bnaia/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/bnaia/storage/logs/worker.log
```

**8. Cron Setup**
```bash
* * * * * cd /path/to/bnaia && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 Database Schema Overview

### Core Tables

```sql
users (id, email, password, user_type_id, active)
roles (id, name, type)
permissions (id, name, resource, action)
languages (id, code, name, direction)
translations (id, translatable_type, translatable_id, lang_id, lang_key, lang_value)
attachments (id, attachable_type, attachable_id, type, path)
```

### Geographic Tables

```sql
countries (id, code, name, active)
regions (id, country_id, name, active)
cities (id, region_id, name, active)
```

### Category Tables

```sql
departments (id, name, commission, active, sort_order)
categories (id, department_id, name, active, sort_order)
sub_categories (id, category_id, name, active, sort_order)
```

### Product Tables

```sql
products (id, sku, department_id, category_id, sub_category_id, brand_id)
vendor_products (id, vendor_id, product_id, price, commission, is_able_to_refund, refund_days, active)
vendor_product_variants (id, vendor_product_id, sku, price, weight)
vendor_product_variant_stocks (id, vendor_product_variant_id, region_id, total_stock, booked_stock, allocated_stock, fulfilled_stock)
variants_configurations (id, product_id, key)
variant_configuration_keys (id, variant_configuration_id, key, value)
brands (id, name, logo, active)
taxes (id, name, percentage, active)
```

### Order Tables

```sql
orders (id, customer_id, order_number, total, shipping_cost, tax_amount, discount_amount, points_used, points_cost, refunded_amount, status)
order_products (id, order_id, vendor_id, vendor_product_variant_id, quantity, price, commission_percentage, commission_amount, shipping_cost, is_refunded, refunded_amount)
order_stages (id, name, slug, color, icon, sort_order)
vendor_order_stages (id, order_id, vendor_id, stage_id, changed_at)
order_fulfillments (id, order_id, driver_id, delivery_notes, delivered_at)
order_status_histories (id, order_id, from_status, to_status, changed_by, notes, changed_at)
```

### Refund Tables

```sql
refund_settings (id, refund_enabled, customer_pays_return_shipping, refund_original_shipping, refund_processing_days)
refund_requests (id, order_id, customer_id, vendor_id, refund_number, status, total_refund_amount, points_used, points_to_deduct, reason)
refund_request_items (id, refund_request_id, order_product_id, quantity, unit_price, total_price, tax_amount, shipping_amount, refund_amount)
```

### Customer Tables

```sql
customers (id, user_id, name, phone, points, active)
addresses (id, customer_id, country_id, region_id, city_id, address_line, is_default)
```

### Vendor Tables

```sql
vendors (id, user_id, name, phone, bank_account, active)
vendor_requests (id, name, email, phone, status, company_logo)
```

### Financial Tables

```sql
transactions (id, transactionable_type, transactionable_id, type, amount, currency)
withdraw_requests (id, vendor_id, amount, status, bank_details, approved_at, completed_at)
```

---

## 📝 Conclusion

This document provides a comprehensive overview of the Bnaia Multi-Vendor E-Commerce Platform architecture, strategy, and implementation details. It covers:

- Complete system architecture and design patterns
- Detailed module structure and responsibilities
- Core concepts (multi-language, attachments, bank products, variants, stock management)
- Complete data flow cycles (order, refund, withdrawal, points)
- Integration points between modules
- Security and authorization mechanisms
- Performance optimization strategies
- API documentation
- Development guidelines and best practices
- Deployment procedures
- Database schema overview

**For Developers:**
- Follow the established patterns and conventions
- Use the service-repository pattern for business logic
- Implement proper caching strategies
- Write tests for critical functionality
- Document your code and API endpoints

**For Maintainers:**
- Keep this documentation updated
- Review and approve architectural changes
- Monitor performance and optimize as needed
- Ensure security best practices are followed

**Last Updated:** January 29, 2026  
**Version:** 1.0  
**Maintained By:** Development Team

---

**Need Help?**
- Check module-specific documentation in `Modules/*/README.md`
- Review implementation guides in `.agent/` directory
- Contact the development team for clarification


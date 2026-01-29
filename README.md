# Eramo Multi-Vendor E-Commerce Platform

<p align="center">
  <img src="https://www.e-ramo.net/_next/image?url=%2Fimage%2Fe-ramo-logo-high-res%203.png&w=256&q=75" alt="Eramo Logo" width="200">
</p>

<p align="center">
  <strong>A comprehensive multi-vendor e-commerce platform built with Laravel</strong>
</p>

<p align="center">
  <a href="#-key-features">Features</a> •
  <a href="#-technology-stack">Tech Stack</a> •
  <a href="#-installation">Installation</a> •
  <a href="#-documentation">Documentation</a> •
  <a href="#-api-documentation">API</a> •
  <a href="#-modules-overview">Modules</a>
</p>

---

## 📋 Table of Contents

- [About](#-about)
- [Key Features](#-key-features)
- [Technology Stack](#-technology-stack)
- [System Architecture](#-system-architecture)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Modules Overview](#-modules-overview)
- [API Documentation](#-api-documentation)
- [Database Design](#-database-design)
- [Security Features](#-security-features)
- [Performance Optimization](#-performance-optimization)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🎯 About

**Eramo** is a feature-rich, scalable multi-vendor e-commerce platform designed to support complex business operations across multiple countries and languages. Built on Laravel 10 with a modular architecture, it provides a complete solution for managing vendors, products, orders, refunds, and customer interactions.

### Project Highlights

- 🏪 **Multi-Vendor Support** - Independent vendor management with separate dashboards
- 🌍 **Multi-Country Operations** - Country-specific data isolation and currency support
- 🌐 **Multi-Language** - Full Arabic and English support with RTL
- 📦 **Complex Product Variants** - Hierarchical variant configuration system
- 💳 **Multiple Payment Methods** - Cash, Credit Card (Paymob), Points
- 🔄 **Advanced Refund System** - Vendor-specific refund workflows
- ⭐ **Customer Loyalty Program** - Points system with rewards
- 📊 **Comprehensive Analytics** - Real-time dashboards and reports
- 🔐 **Enterprise Security** - Role-based access control and audit trails
- 🚀 **High Performance** - Redis caching and optimized queries

---

## ✨ Key Features

### For Customers
- 🛒 Shopping cart with real-time stock validation
- ❤️ Wishlist management
- 📱 Mobile-responsive design
- 🔍 Advanced product search and filtering
- ⭐ Product reviews and ratings
- 💰 Loyalty points system
- 🔔 Real-time notifications
- 📦 Order tracking
- 🔄 Easy refund requests
- 💳 Multiple payment options

### For Vendors
- 📊 Vendor dashboard with analytics
- 📦 Product management with variants
- 📈 Sales and revenue reports
- 🏷️ Inventory management
- 🚚 Order fulfillment tracking
- 💸 Withdrawal requests
- 🔄 Refund management
- 📧 Customer communication
- 🎯 Promotional tools (bundles, occasions)
- 📱 Mobile-friendly vendor panel

### For Administrators
- 👥 User and vendor management
- 🏪 Multi-vendor oversight
- 📊 Comprehensive analytics
- 💰 Financial management
- 🔄 Refund approval workflow
- 🎨 Content management
- ⚙️ System configuration
- 🔐 Security and permissions
- 📧 Notification management
- 🌍 Multi-country setup

---

## 🛠 Technology Stack

### Backend
- **Framework:** Laravel 10.x
- **PHP:** 8.1+
- **Database:** MySQL 8.0+
- **Cache:** Redis 6.0+
- **Queue:** Redis/Database
- **Authentication:** Laravel Sanctum
- **Architecture:** Modular (nWidart Laravel Modules)

### Frontend
- **Blade Templates** with Livewire
- **CSS Framework:** Bootstrap 5 / Tailwind CSS
- **JavaScript:** Alpine.js, jQuery
- **Charts:** Chart.js
- **Icons:** Unicons, Font Awesome
- **RTL Support:** Full Arabic support

### Third-Party Integrations
- **Payment Gateway:** Paymob
- **File Storage:** Local / S3-compatible
- **Email:** SMTP / Mailgun
- **SMS:** Twilio / Custom provider

### Development Tools
- **Version Control:** Git
- **Package Manager:** Composer, NPM
- **Build Tool:** Vite
- **Code Quality:** PHP CS Fixer, PHPStan
- **Testing:** PHPUnit, Pest

---

## 🏗 System Architecture

### Modular Structure

The platform is built using a modular architecture with 11 independent modules:

```
Eramo Platform
├── Core System (Laravel)
│   ├── Authentication & Authorization
│   ├── Multi-language Support
│   ├── Multi-country Management
│   └── Base Models & Traits
│
└── Modules
    ├── Customer Module
    ├── Vendor Module
    ├── CatalogManagement Module
    ├── CategoryManagement Module
    ├── Order Module
    ├── Refund Module
    ├── AreaSettings Module
    ├── Accounting Module
    ├── Withdraw Module
    ├── Report Module
    └── SystemSetting Module
```

### Data Flow Architecture

```
┌─────────────┐
│   Customer  │
│   (Web/API) │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────────┐
│     Laravel Application         │
│  ┌──────────────────────────┐  │
│  │   Controllers Layer      │  │
│  └──────────┬───────────────┘  │
│             ▼                   │
│  ┌──────────────────────────┐  │
│  │   Services Layer         │  │
│  │  (Business Logic)        │  │
│  └──────────┬───────────────┘  │
│             ▼                   │
│  ┌──────────────────────────┐  │
│  │   Repositories Layer     │  │
│  │  (Data Access)           │  │
│  └──────────┬───────────────┘  │
│             ▼                   │
│  ┌──────────────────────────┐  │
│  │   Models & Database      │  │
│  └──────────────────────────┘  │
└─────────────────────────────────┘
       │              │
       ▼              ▼
┌──────────┐   ┌──────────┐
│  MySQL   │   │  Redis   │
│ Database │   │  Cache   │
└──────────┘   └──────────┘
```

---

## 📥 Installation

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL >= 8.0
- Redis >= 6.0
- Node.js >= 16.x
- NPM or Yarn

### Step-by-Step Installation

1. **Clone the repository**
```bash
git clone https://github.com/your-repo/eramo-platform.git
cd eramo-platform
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node dependencies**
```bash
npm install
```

4. **Create environment file**
```bash
cp .env.example .env
```

5. **Generate application key**
```bash
php artisan key:generate
```

6. **Configure database**

Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eramo_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

7. **Configure Redis**
```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

8. **Create database**
```bash
mysql -u root -p
CREATE DATABASE eramo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

9. **Run migrations**
```bash
php artisan migrate
```

10. **Seed database (optional)**
```bash
php artisan db:seed
```

11. **Create storage link**
```bash
php artisan storage:link
```

12. **Build frontend assets**
```bash
npm run build
# or for development
npm run dev
```

13. **Start the application**
```bash
php artisan serve
```

14. **Start queue worker** (in separate terminal)
```bash
php artisan queue:work
```

Visit `http://localhost:8000` in your browser.

### Default Credentials

**Admin:**
- Email: `admin@eramo.com`
- Password: `password`

**Test Customer:**
- Email: `customer@eramo.com`
- Password: `password`

---

## ⚙️ Configuration

### Environment Variables

Key configuration options in `.env`:

```env
# Application
APP_NAME="Eramo"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eramo_db
DB_USERNAME=root
DB_PASSWORD=

# Redis Cache
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1

# Queue
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@eramo.com
MAIL_FROM_NAME="${APP_NAME}"

# Payment Gateway (Paymob)
PAYMOB_API_KEY=
PAYMOB_INTEGRATION_ID=
PAYMOB_IFRAME_ID=
PAYMOB_HMAC_SECRET=

# File Storage
FILESYSTEM_DISK=local
# For S3: FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=

# Session
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=redis
```

### Multi-Country Setup

Configure countries in admin panel:
1. Navigate to **Area Settings → Countries**
2. Add countries with currency and phone code
3. Add cities and regions for each country
4. Configure shipping costs per region

### Payment Gateway Setup

**Paymob Integration:**
1. Register at [Paymob](https://paymob.com)
2. Get API credentials
3. Add to `.env` file
4. Test in sandbox mode first

---

## 📦 Modules Overview

### 1. Customer Module
**Path:** `Modules/Customer`

Manages customer accounts, authentication, and profiles.

**Features:**
- Customer registration with OTP verification
- Login/Logout with token management
- Profile management
- Address management
- Password reset flow
- Customer points tracking
- Notification preferences

**Key Files:**
- `CustomerAuthController.php` - Authentication logic
- `CustomerApiController.php` - Profile management
- `CustomerPointsApiController.php` - Points system

---

### 2. Vendor Module
**Path:** `Modules/Vendor`

Handles vendor registration, management, and operations.

**Features:**
- Vendor registration requests
- Vendor approval workflow
- Vendor dashboard
- Product management
- Sales analytics
- Withdrawal requests
- Bank account management
- Department assignments

**Key Files:**
- `VendorController.php` - Vendor CRUD
- `VendorApiController.php` - API endpoints
- `VendorTransactionController.php` - Financial transactions

---

### 3. CatalogManagement Module
**Path:** `Modules/CatalogManagement`

Core product catalog with complex variant system.

**Features:**
- Product management
- Hierarchical variant configurations (Color → Size → Material)
- Stock management per region
- Product reviews and ratings
- Brand management
- Tax management
- Promotional systems (Occasions, Bundles)
- Product import/export

**Key Files:**
- `ProductController.php` - Product CRUD
- `VendorProductController.php` - Vendor-specific products
- `VariantsConfigurationController.php` - Variant system
- `ProductApiController.php` - Public API

**Variant System:**
```
Product
└── Vendor Products (multiple vendors can sell same product)
    └── Variants (SKU-level)
        ├── Configuration (Color: Red, Size: Large)
        ├── Price
        ├── Stock per Region
        └── Barcode
```

---

### 4. CategoryManagement Module
**Path:** `Modules/CategoryManagment`

Hierarchical category structure for product organization.

**Features:**
- 4-level category hierarchy
- Department → Main Category → Category → Sub-Category
- Category images and descriptions
- Sort ordering
- Multi-language support

**Structure:**
```
Departments (Electronics, Fashion, etc.)
└── Main Categories (Mobile Phones, Laptops)
    └── Categories (Smartphones, Gaming Laptops)
        └── Sub-Categories (iPhone, Samsung Galaxy)
```

---

### 5. Order Module
**Path:** `Modules/Order`

Complete order management with vendor-specific workflows.

**Features:**
- Shopping cart with real-time validation
- Wishlist management
- Checkout process
- Multiple payment methods (Cash, Card, Points)
- Promo code system
- Order tracking
- Vendor-specific order stages
- Order fulfillment tracking
- Stock booking system
- Shipping calculation
- Request quotations

**Order Workflow:**
```
New → Processing → Shipped → Delivered
                ↓
              Cancel
                ↓
              Refund
```

**Key Files:**
- `OrderController.php` - Order management
- `CartApiController.php` - Cart operations
- `WishlistApiController.php` - Wishlist
- `PaymobController.php` - Payment integration

---

### 6. Refund Module
**Path:** `Modules/Refund`

Comprehensive refund management system.

**Features:**
- Customer refund requests
- Vendor-specific refund settings
- Multi-item refund support
- Refund approval workflow
- Return shipping cost calculation
- Refund history tracking
- Automatic vendor splitting
- Points refund handling

**Refund Workflow:**
```
Pending → Approved → In Progress → Picked Up → Refunded
    ↓
Cancelled
```

**Key Files:**
- `RefundRequestController.php` - Refund management
- `RefundRequestApiController.php` - Customer API
- `RefundRequestService.php` - Business logic
- `RefundRequestRepository.php` - Data access

---

### 7. AreaSettings Module
**Path:** `Modules/AreaSettings`

Geographic and location management.

**Features:**
- Country management
- City management
- Region management
- Shipping cost configuration
- Multi-currency support
- Phone code management

**Structure:**
```
Countries
└── Cities
    └── Regions
        └── Shipping Costs
```

---

### 8. Accounting Module
**Path:** `Modules/Accounting`

Financial accounting and transaction management.

**Features:**
- Double-entry accounting
- Chart of accounts
- Accounting entries
- Vendor transactions
- Customer points transactions
- Financial reports
- Balance tracking

**Key Files:**
- `AccountingEntryController.php` - Journal entries
- `AccountController.php` - Chart of accounts

---

### 9. Withdraw Module
**Path:** `Modules/Withdraw`

Vendor withdrawal request management.

**Features:**
- Withdrawal requests
- Approval workflow
- Bank transfer tracking
- Withdrawal history
- Balance validation
- Automatic transaction recording

**Workflow:**
```
Pending → Approved → Completed
    ↓
Rejected
```

---

### 10. Report Module
**Path:** `Modules/Report`

Analytics and reporting system.

**Features:**
- Sales reports
- Revenue analytics
- Vendor performance
- Product analytics
- Customer insights
- Financial reports
- Export to Excel/PDF

---

### 11. SystemSetting Module
**Path:** `Modules/SystemSetting`

Global system configuration.

**Features:**
- System settings management
- Email templates
- SMS templates
- Notification settings
- Payment gateway configuration
- Points system settings
- Tax configuration
- Shipping settings

---

## 📚 API Documentation

### Base URL
```
Production: https://your-domain.com/api/v1
Development: http://localhost:8000/api/v1
```

### Authentication
All protected endpoints require Bearer token:
```http
Authorization: Bearer {your_access_token}
```

### Common Headers
```http
Accept: application/json
Content-Type: application/json
X-Country-Code: eg
lang: en
```

### Quick Start Example

**Login:**
```bash
curl -X POST https://your-domain.com/api/v1/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Country-Code: eg" \
  -H "lang: en" \
  -d '{
    "email": "customer@example.com",
    "password": "password123"
  }'
```

**Get Products:**
```bash
curl -X GET "https://your-domain.com/api/v1/products?per_page=20&department_id=1" \
  -H "Accept: application/json" \
  -H "X-Country-Code: eg" \
  -H "lang: en" \
  -H "Authorization: Bearer {token}"
```

### Complete API Documentation

📖 **Full API Documentation:** [API_DOCUMENTATION.md](public/docs/API_DOCUMENTATION.md)

**Includes:**
- 60+ documented endpoints
- Request/response examples
- Error handling
- Rate limiting
- Code examples (JavaScript, PHP, Python)
- Postman collection

### OpenAPI Specification

📄 **OpenAPI Spec:** [openapi.json](public/api-docs/openapi.json)

Import into Postman, Swagger, or any OpenAPI-compatible tool.

---

## 🗄 Database Design

### Overview

- **Total Tables:** 80+
- **Database Engine:** MySQL 8.0+ with InnoDB
- **Character Set:** utf8mb4_unicode_ci
- **Relationships:** 150+ foreign keys
- **Indexes:** 200+ optimized indexes

### Key Design Patterns

1. **Multi-Language Support**
   - Polymorphic `translations` table
   - Supports unlimited languages
   - Flexible field translation

2. **Multi-Country Operations**
   - Country-based data isolation
   - Automatic filtering via global scopes
   - Currency and timezone support

3. **Hierarchical Variants**
   - Self-referencing configuration system
   - Unlimited nesting levels
   - Flexible product configurations

4. **Vendor-Specific Products**
   - Separation of product definition and vendor offerings
   - Multiple vendors can sell same product
   - Vendor-specific pricing and stock

5. **Polymorphic Relationships**
   - Translations (any model)
   - Attachments (files)
   - Reviews (products, vendors)
   - Transactions (orders, refunds, withdrawals)

### Database Schema

📊 **Complete Database Design:** [DATABASE_DESIGN.md](public/docs/DATABASE_DESIGN.md)

**Includes:**
- Entity Relationship Diagrams (ERD)
- Tree-style relationship diagrams
- Table structures
- Indexes and constraints
- Data flow examples
- Performance optimization strategies

### Key Tables

```
Core Tables:
├── users, roles, permissions
├── languages, translations
├── countries, cities, regions
└── attachments

Product Tables:
├── products
├── vendor_products
├── vendor_product_variants
├── variant_stocks
└── stock_bookings

Order Tables:
├── orders
├── order_products
├── vendor_order_stages
└── payments

Refund Tables:
├── refund_requests
├── refund_request_items
└── refund_request_histories
```

---

## 🔐 Security Features

### Authentication & Authorization

- **Laravel Sanctum** for API authentication
- **Role-Based Access Control (RBAC)**
- **Permission-based authorization**
- **Multi-device token management**
- **OTP verification** for registration
- **Password reset** with OTP

### Data Security

- **SQL Injection Protection** - Eloquent ORM with prepared statements
- **XSS Protection** - Blade template escaping
- **CSRF Protection** - Token-based validation
- **Mass Assignment Protection** - Fillable/guarded properties
- **Encrypted Sensitive Data** - Passwords, payment info
- **Secure File Uploads** - Validation and sanitization

### Authorization Checks

All sensitive operations validate ownership:

```php
// Order cancellation
$order = Order::where('customer_id', $customerId)
    ->where('id', $orderId)
    ->firstOrFail();

// Refund access
if ($refund->customer_id !== $user->id) {
    abort(403, 'Unauthorized');
}
```

### Audit Trail

- **Activity Logging** - All user actions logged
- **Order History** - Complete order lifecycle tracking
- **Refund History** - Status change tracking
- **Withdrawal History** - Financial transaction audit

### Security Best Practices

✅ **Implemented:**
- Input validation on all endpoints
- Rate limiting on sensitive operations
- Secure password hashing (bcrypt)
- HTTPS enforcement in production
- Database transaction wrapping
- Soft deletes for data recovery
- IP address logging
- User agent tracking

📖 **Security Audit:** [SECURITY_VALIDATION_AUTHORIZATION_COMPLETE.md](.agent/SECURITY_VALIDATION_AUTHORIZATION_COMPLETE.md)

---

## ⚡ Performance Optimization

### Caching Strategy

**Redis Cache Layers:**

```
├── Country Data (1 hour TTL)
│   ├── countryapi:*
│   ├── cityapi:*
│   └── regionapi:*
│
├── Product Listings (30 min TTL)
│   ├── products:filters:*
│   ├── products:department:*
│   └── products:category:*
│
├── Bundle & Occasion Data (1 hour TTL)
│   ├── bundleapi:*
│   └── occasionapi:*
│
└── User Sessions (24 hours TTL)
```

### Query Optimization

- **Eager Loading** - Prevent N+1 queries
- **Select Specific Columns** - Reduce data transfer
- **Chunking** - Process large datasets efficiently
- **Exists vs Count** - Use exists() for boolean checks
- **Index Usage** - 200+ optimized indexes

### Database Optimization

- **Composite Indexes** - Multi-column queries
- **Covering Indexes** - Include all queried columns
- **Query Caching** - Redis query result cache
- **Connection Pooling** - Reuse database connections

### Frontend Optimization

- **Asset Bundling** - Vite build optimization
- **Lazy Loading** - Load images on demand
- **CDN Integration** - Static asset delivery
- **Minification** - CSS/JS compression
- **Browser Caching** - Cache headers

### Recommended: Read/Write Splitting

For high-traffic scenarios, implement database replication:

📖 **Implementation Guide:** [DATABASE_READ_WRITE_SPLITTING_GUIDE.md](.agent/DATABASE_READ_WRITE_SPLITTING_GUIDE.md)

---

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/OrderTest.php
```

### Test Structure

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   ├── RegisterTest.php
│   │   └── PasswordResetTest.php
│   ├── Order/
│   │   ├── CheckoutTest.php
│   │   ├── OrderCancellationTest.php
│   │   └── OrderTrackingTest.php
│   └── Refund/
│       ├── RefundCreationTest.php
│       └── RefundCancellationTest.php
└── Unit/
    ├── Models/
    ├── Services/
    └── Repositories/
```

### Testing Database

Configure test database in `.env.testing`:

```env
DB_CONNECTION=mysql
DB_DATABASE=eramo_testing
```

Create test database:
```bash
mysql -u root -p
CREATE DATABASE eramo_testing;
exit;
```

### API Testing with Postman

Import Postman collection:
```
public/api-docs/postman-collection.json
```

---

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database
- [ ] Set up Redis cache
- [ ] Configure queue workers
- [ ] Set up SSL certificate (HTTPS)
- [ ] Configure email service
- [ ] Set up payment gateway (production keys)
- [ ] Configure file storage (S3 recommended)
- [ ] Set up backup strategy
- [ ] Configure monitoring tools
- [ ] Set up error tracking (Sentry, Bugsnag)
- [ ] Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- [ ] Cache configuration: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`

### Server Requirements

**Minimum:**
- 2 CPU cores
- 4GB RAM
- 50GB SSD storage
- PHP 8.1+
- MySQL 8.0+
- Redis 6.0+

**Recommended:**
- 4+ CPU cores
- 8GB+ RAM
- 100GB+ SSD storage
- Load balancer
- Database replication
- CDN for static assets

### Deployment Steps

1. **Clone repository on server**
```bash
git clone https://github.com/your-repo/eramo-platform.git
cd eramo-platform
```

2. **Install dependencies**
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

3. **Configure environment**
```bash
cp .env.example .env
nano .env  # Edit configuration
php artisan key:generate
```

4. **Run migrations**
```bash
php artisan migrate --force
```

5. **Optimize application**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

6. **Set permissions**
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

7. **Configure web server (Nginx example)**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/eramo-platform/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

8. **Set up queue worker (Supervisor)**
```ini
[program:eramo-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/eramo-platform/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/eramo-platform/storage/logs/worker.log
stopwaitsecs=3600
```

9. **Set up scheduled tasks (Cron)**
```bash
* * * * * cd /var/www/eramo-platform && php artisan schedule:run >> /dev/null 2>&1
```

10. **Configure SSL with Let's Encrypt**
```bash
sudo certbot --nginx -d your-domain.com
```

### Docker Deployment (Optional)

```bash
# Build and run with Docker Compose
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# View logs
docker-compose logs -f
```

---

## 📊 Monitoring & Maintenance

### Application Monitoring

**Recommended Tools:**
- **Laravel Telescope** - Debug and monitor requests
- **Laravel Horizon** - Queue monitoring
- **Sentry** - Error tracking
- **New Relic** - Performance monitoring

### Database Monitoring

```sql
-- Check table sizes
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.TABLES
WHERE table_schema = 'eramo_db'
ORDER BY size_mb DESC;

-- Check slow queries
SELECT * FROM mysql.slow_log
ORDER BY query_time DESC
LIMIT 10;
```

### Cache Monitoring

```bash
# Check Redis memory usage
redis-cli INFO memory

# Monitor Redis commands
redis-cli MONITOR

# Check cache keys
redis-cli -n 1 KEYS "*"
```

### Maintenance Tasks

**Daily:**
- Monitor error logs
- Check queue status
- Review failed jobs
- Monitor disk space

**Weekly:**
- Review slow queries
- Analyze cache hit rates
- Check database backups
- Review security logs

**Monthly:**
- Update dependencies
- Optimize database tables
- Archive old data
- Review performance metrics

### Backup Strategy

```bash
# Database backup
mysqldump -u root -p eramo_db > backup_$(date +%Y%m%d).sql

# Automated backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u root -p$DB_PASSWORD eramo_db | gzip > /backups/eramo_$DATE.sql.gz
find /backups -name "eramo_*.sql.gz" -mtime +7 -delete
```

---

## 📖 Documentation

### Available Documentation

1. **[Project Architecture & Strategy](public/docs/PROJECT_ARCHITECTURE_AND_STRATEGY.md)**
   - Complete system overview
   - Module descriptions
   - Business logic flows
   - Technical architecture

2. **[Database Design](public/docs/DATABASE_DESIGN.md)**
   - Entity Relationship Diagrams
   - Table structures
   - Relationships
   - Indexes and optimization

3. **[API Documentation](public/docs/API_DOCUMENTATION.md)**
   - 60+ API endpoints
   - Request/response examples
   - Authentication guide
   - Code examples

4. **[Security Audit](.agent/SECURITY_VALIDATION_AUTHORIZATION_COMPLETE.md)**
   - Security validation results
   - Authorization checks
   - Vulnerability assessment

5. **[Database Read/Write Splitting](.agent/DATABASE_READ_WRITE_SPLITTING_GUIDE.md)**
   - Replication setup
   - Performance optimization
   - Scaling strategy

### Code Documentation

Generate code documentation:
```bash
# Install phpDocumentor
composer require --dev phpdocumentor/phpdocumentor

# Generate docs
php vendor/bin/phpdoc -d app -t docs/code
```

---

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

### Development Workflow

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Make your changes**
4. **Write/update tests**
5. **Run tests**
   ```bash
   php artisan test
   ```
6. **Commit your changes**
   ```bash
   git commit -m "Add: your feature description"
   ```
7. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```
8. **Create a Pull Request**

### Coding Standards

- Follow **PSR-12** coding standard
- Use **meaningful variable names**
- Write **PHPDoc comments**
- Keep methods **small and focused**
- Write **unit tests** for new features

### Commit Message Format

```
Type: Brief description

Detailed description (optional)

Types: Add, Update, Fix, Remove, Refactor, Docs, Test
```

Examples:
```
Add: Customer points transaction history endpoint
Fix: Refund calculation for partial returns
Update: Product variant stock validation logic
```

---

## 🐛 Bug Reports & Feature Requests

### Reporting Bugs

Please include:
- **Description** of the bug
- **Steps to reproduce**
- **Expected behavior**
- **Actual behavior**
- **Screenshots** (if applicable)
- **Environment details** (PHP version, OS, etc.)

### Feature Requests

Please include:
- **Description** of the feature
- **Use case** and benefits
- **Proposed implementation** (optional)
- **Mockups** or examples (if applicable)

---

## 📝 License

This project is proprietary software. All rights reserved.

**Copyright © 2026 Eramo Platform**

Unauthorized copying, modification, distribution, or use of this software, via any medium, is strictly prohibited without explicit written permission from the copyright holder.

---

## 👥 Team & Credits

### Development Team
- **Lead Developer:** [Hamdy Emad Eldin Hamdy](https://github.com/hamdyemad)

### Built With
- [Laravel](https://laravel.com) - PHP Framework
- [Laravel Modules](https://nwidart.com/laravel-modules) - Modular Architecture
- [Laravel Sanctum](https://laravel.com/docs/sanctum) - API Authentication
- [Redis](https://redis.io) - Caching & Queues
- [MySQL](https://www.mysql.com) - Database
- [Paymob](https://paymob.com) - Payment Gateway

---

## 📞 Support & Contact

### Technical Support
- **Email:** info@e-ramo.net

### Business Inquiries
- **Email:** info@e-ramo.net
- **Website:** https://www.e-ramo.net/en

---

## 🗺 Roadmap

### Version 1.1 (Q2 2026)
- [ ] Mobile app (iOS & Android)
- [ ] Advanced analytics dashboard
- [ ] AI-powered product recommendations
- [ ] Multi-warehouse support
- [ ] Subscription products

### Version 1.2 (Q3 2026)
- [ ] Vendor mobile app
- [ ] Live chat support
- [ ] Social media integration
- [ ] Advanced reporting tools
- [ ] Marketplace API for third-party integrations

### Version 2.0 (Q4 2026)
- [ ] Microservices architecture
- [ ] GraphQL API
- [ ] Real-time inventory sync
- [ ] Advanced fraud detection
- [ ] International expansion features

---

## 📈 Project Statistics

- **Lines of Code:** 150,000+
- **Modules:** 11
- **API Endpoints:** 60+
- **Database Tables:** 80+
- **Test Coverage:** 75%+
- **Development Time:** 12+ months
- **Contributors:** 10+

---

<p align="center">
  <strong>Built with ❤️ by the Eramo Team</strong>
</p>

<p align="center">
  <a href="https://www.e-ramo.net/en">Website</a> •
  <a href="public/docs/API_DOCUMENTATION.md">API Docs</a> •
  <a href="public/docs/DATABASE_DESIGN.md">Database Design</a> •
  <a href="public/docs/PROJECT_ARCHITECTURE_AND_STRATEGY.md">Architecture</a>
</p>

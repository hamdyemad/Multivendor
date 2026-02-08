# Request Quotation - Multi-Vendor Offers System

## السيناريو الجديد

### المراحل:
1. **العميل**: يعمل Request Quotation
2. **الأدمن**: يستلم الطلب ويختار تجار معينين لإرسال الطلب لهم
3. **التجار**: يستلموا Notification ويعملوا Offers (كل تاجر يعمل Offer منفصل)
4. **العميل**: يشوف كل الـ Offers ويختار واحد أو أكتر
5. **النظام**: يتحول كل Offer مقبول لـ Order منفصل

## التغييرات المطلوبة

### 1. جدول جديد: `request_quotation_vendors`
جدول وسيط بين Request Quotation والتجار المختارين

```sql
CREATE TABLE request_quotation_vendors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_quotation_id BIGINT UNSIGNED NOT NULL,
    vendor_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pending', 'offer_sent', 'offer_accepted', 'offer_rejected', 'order_created') DEFAULT 'pending',
    offer_price DECIMAL(10,2) NULL,
    offer_notes TEXT NULL,
    offer_sent_at TIMESTAMP NULL,
    offer_responded_at TIMESTAMP NULL,
    order_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (request_quotation_id) REFERENCES request_quotations(id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_quotation_vendor (request_quotation_id, vendor_id)
);
```

### 2. تعديل جدول `request_quotations`
إضافة حالات جديدة:

```php
const STATUS_PENDING = 'pending';                    // العميل عمل الطلب
const STATUS_SENT_TO_VENDORS = 'sent_to_vendors';   // الأدمن اختار التجار
const STATUS_OFFERS_RECEIVED = 'offers_received';   // التجار عملوا Offers
const STATUS_PARTIALLY_ACCEPTED = 'partially_accepted'; // العميل قبل بعض الـ Offers
const STATUS_FULLY_ACCEPTED = 'fully_accepted';     // العميل قبل كل الـ Offers
const STATUS_REJECTED = 'rejected';                  // العميل رفض كل الـ Offers
const STATUS_ORDERS_CREATED = 'orders_created';     // تم إنشاء الأوردرات
const STATUS_ARCHIVED = 'archived';
```

### 3. Models الجديدة

#### RequestQuotationVendor Model
```php
class RequestQuotationVendor extends Model
{
    protected $fillable = [
        'request_quotation_id',
        'vendor_id',
        'status',
        'offer_price',
        'offer_notes',
        'offer_sent_at',
        'offer_responded_at',
        'order_id',
    ];
    
    // Relations
    public function requestQuotation()
    public function vendor()
    public function order()
    
    // Methods
    public function canSendOffer(): bool
    public function canRespondToOffer(): bool
    public function sendOffer(float $price, ?string $notes): void
    public function acceptOffer(): void
    public function rejectOffer(): void
}
```

### 4. Controllers الجديدة

#### Admin Side: `RequestQuotationController`
```php
// إرسال الطلب لتجار معينين
public function sendToVendors(Request $request, $id)
{
    // Validate vendor_ids array
    // Create RequestQuotationVendor records
    // Send notifications to vendors
    // Update request_quotation status to 'sent_to_vendors'
}

// عرض التجار المختارين والـ Offers
public function vendors($id)
{
    // Show list of vendors and their offers
}
```

#### Vendor Side: `VendorRequestQuotationController` (جديد)
```php
// عرض الطلبات المرسلة للتاجر
public function index()
{
    // Show request quotations sent to this vendor
}

// عرض تفاصيل طلب معين
public function show($id)
{
    // Show request quotation details
}

// إرسال Offer
public function sendOffer(Request $request, $id)
{
    // Validate offer_price and offer_notes
    // Update RequestQuotationVendor
    // Send notification to customer
    // Update request_quotation status if needed
}
```

#### Customer API: `RequestQuotationApiController`
```php
// عرض الـ Offers المتاحة
public function offers($id)
{
    // Show all offers from vendors
}

// قبول Offer معين
public function acceptOffer(Request $request, $quotationId, $vendorId)
{
    // Accept vendor offer
    // Create order
    // Send notifications
}

// رفض Offer معين
public function rejectOffer(Request $request, $quotationId, $vendorId)
{
    // Reject vendor offer
    // Send notification
}
```

### 5. Notifications الجديدة

#### VendorQuotationRequestNotification
- يتبعت للتاجر لما الأدمن يختاره
- يحتوي على: رقم الطلب، تفاصيل العميل، الملاحظات، الملف

#### CustomerOfferReceivedNotification
- يتبعت للعميل لما تاجر يعمل Offer
- يحتوي على: اسم التاجر، السعر المقترح، الملاحظات

#### VendorOfferAcceptedNotification
- يتبعت للتاجر لما العميل يقبل الـ Offer
- يحتوي على: رقم الأوردر الجديد

#### VendorOfferRejectedNotification
- يتبعت للتاجر لما العميل يرفض الـ Offer

### 6. Views الجديدة

#### Admin Panel
- `request-quotations/select-vendors.blade.php` - اختيار التجار
- `request-quotations/vendors-offers.blade.php` - عرض الـ Offers من التجار

#### Vendor Panel
- `vendor/request-quotations/index.blade.php` - قائمة الطلبات
- `vendor/request-quotations/show.blade.php` - تفاصيل الطلب
- `vendor/request-quotations/send-offer.blade.php` - إرسال Offer

#### Customer App (API)
- API endpoints لعرض الـ Offers وقبولها/رفضها

### 7. Permissions الجديدة
```php
'request-quotations.send-to-vendors'
'request-quotations.view-offers'
'vendor-quotations.index'
'vendor-quotations.show'
'vendor-quotations.send-offer'
```

## Flow Chart

```
Customer Creates Request
         ↓
    [PENDING]
         ↓
Admin Selects Vendors → Send Notifications
         ↓
  [SENT_TO_VENDORS]
         ↓
Vendors Send Offers → Notify Customer
         ↓
  [OFFERS_RECEIVED]
         ↓
Customer Reviews Offers
         ↓
    ┌────┴────┐
    ↓         ↓
Accept    Reject
    ↓         ↓
[ACCEPTED] [REJECTED]
    ↓
Create Orders (one per accepted vendor)
    ↓
[ORDERS_CREATED]
```

## Migration Order

1. Create `request_quotation_vendors` table
2. Add new statuses to `request_quotations` table
3. Create models and relationships
4. Create controllers
5. Create notifications
6. Create views
7. Add routes
8. Add permissions

## API Endpoints

### Admin
- `POST /admin/request-quotations/{id}/send-to-vendors` - إرسال لتجار
- `GET /admin/request-quotations/{id}/vendors` - عرض التجار والـ Offers

### Vendor
- `GET /vendor/request-quotations` - قائمة الطلبات
- `GET /vendor/request-quotations/{id}` - تفاصيل طلب
- `POST /vendor/request-quotations/{id}/send-offer` - إرسال Offer

### Customer API
- `GET /api/v1/request-quotations/{id}/offers` - عرض الـ Offers
- `POST /api/v1/request-quotations/{id}/vendors/{vendorId}/accept` - قبول Offer
- `POST /api/v1/request-quotations/{id}/vendors/{vendorId}/reject` - رفض Offer

هل تريد أن أبدأ في التنفيذ؟

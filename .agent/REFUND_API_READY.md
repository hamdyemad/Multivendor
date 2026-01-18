# Refund API - Ready for Use

## Status: ✅ COMPLETE

All refund API endpoints are now fully functional with clean architecture implementation.

---

## API Endpoints

### Base URL
```
/api/refunds
```

### Authentication
All endpoints require `auth:sanctum` middleware.

### Available Endpoints

#### 1. List Refund Requests
```
GET /api/refunds
```

**Query Parameters:**
- `status` - Filter by status (pending, approved, in_progress, picked_up, refunded, rejected, cancelled)
- `vendor_status` - Filter by vendor status
- `customer_id` - Filter by customer
- `vendor_id` - Filter by vendor
- `date_from` - Filter from date (YYYY-MM-DD)
- `date_to` - Filter to date (YYYY-MM-DD)
- `search` - Search by refund number, order number, customer name, or vendor name
- `per_page` - Items per page (default: 15)

**Response:**
```json
{
  "data": [...],
  "meta": {
    "total": 100,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7,
    "from": 1,
    "to": 15
  }
}
```

---

#### 2. Get Refund Statistics
```
GET /api/refunds/statistics
```

**Query Parameters:**
- `customer_id` - Filter by customer
- `vendor_id` - Filter by vendor

**Response:**
```json
{
  "status": true,
  "message": "Statistics retrieved successfully",
  "data": {
    "total": 150,
    "pending": 20,
    "approved": 30,
    "in_progress": 15,
    "picked_up": 10,
    "refunded": 60,
    "rejected": 10,
    "cancelled": 5,
    "total_amount": 15000.50
  }
}
```

---

#### 3. Show Single Refund Request
```
GET /api/refunds/{id}
```

**Authorization:**
- Admin: Can view all refunds
- Vendor: Can view their own refunds
- Customer: Can view their own refunds

**Response:**
```json
{
  "status": true,
  "message": "Refund request retrieved successfully",
  "data": {
    "id": 1,
    "refund_number": "REF-20260118-0001",
    "order_id": 123,
    "customer_id": 45,
    "vendor_id": 12,
    "status": "pending",
    "status_label": "Pending",
    "vendor_status": "pending",
    "vendor_status_label": "Pending",
    "total_refund_amount": 250.00,
    "total_products_amount": 200.00,
    "total_shipping_amount": 50.00,
    "total_tax_amount": 20.00,
    "total_discount_amount": 20.00,
    "return_shipping_cost": 0.00,
    "reason": "Product damaged",
    "customer_notes": "Item arrived broken",
    "vendor_notes": null,
    "admin_notes": null,
    "created_at": "2026-01-18T10:30:00.000000Z",
    "approved_at": null,
    "refunded_at": null,
    "order": {...},
    "customer": {...},
    "vendor": {...},
    "items": [...]
  }
}
```

---

#### 4. Create Refund Request
```
POST /api/refunds
```

**Request Body:**
```json
{
  "order_id": 123,
  "reason": "Product damaged",
  "customer_notes": "Item arrived broken",
  "items": [
    {
      "order_product_id": 456,
      "quantity": 1,
      "reason": "Damaged on arrival"
    }
  ]
}
```

**Validation Rules:**
- `order_id`: required, must exist in orders table
- `reason`: required, max 500 characters
- `customer_notes`: optional, max 1000 characters
- `items`: required array, minimum 1 item
- `items.*.order_product_id`: required, must exist
- `items.*.quantity`: required, integer, minimum 1
- `items.*.reason`: optional, max 500 characters

**Authorization:**
- Customer must own the order

**Response:**
```json
{
  "status": true,
  "message": "Refund request created successfully",
  "data": {...}
}
```

---

#### 5. Update Refund Status
```
POST /api/refunds/{id}/status
```

**Request Body:**
```json
{
  "status": "approved",
  "notes": "Approved by admin"
}
```

**Validation Rules:**
- `status`: required, must be one of: pending, approved, in_progress, picked_up, refunded, rejected, cancelled
- `notes`: optional, max 1000 characters

**Authorization:**
- Admin: Can update any refund
- Vendor: Can update their own refunds

**Response:**
```json
{
  "status": true,
  "message": "Refund status updated successfully",
  "data": {...}
}
```

---

#### 6. Cancel Refund Request
```
POST /api/refunds/{id}/cancel
```

**Authorization:**
- Customer: Can only cancel their own pending refunds

**Response:**
```json
{
  "status": true,
  "message": "Refund request cancelled successfully",
  "data": {...}
}
```

---

## Architecture Components

### ✅ Clean Architecture Implementation

#### Controller Layer
- `RefundRequestApiController.php`
- Handles HTTP requests/responses
- Uses Res trait for standardized API responses
- No business logic or direct model access

#### Service Layer
- `RefundRequestService.php`
- Contains all business logic
- Handles refund creation, status updates, cancellations
- Manages database transactions
- Calculates totals and validates business rules

#### Repository Layer
- `RefundRequestRepository.php`
- Implements `RefundRequestRepositoryInterface`
- Handles all database queries
- Manages data access and filtering
- Provides statistics and authorization checks

#### Validation Layer
- `StoreRefundRequestRequest.php` - Create refund validation
- `UpdateRefundStatusRequest.php` - Status update validation
- API-specific error format (422 JSON responses)

#### Resource Layer
- `RefundRequestResource.php` - Single refund transformation
- `RefundRequestItemResource.php` - Refund item transformation
- `RefundRequestCollection.php` - Paginated collection with meta

#### Model Layer
- `RefundRequest.php` - Main refund model
  - Auto-generates refund numbers (REF-YYYYMMDD-XXXX)
  - `scopeFilter()` for advanced filtering
  - `calculateTotals()` for amount calculations
  - Relationships: order, customer, vendor, items
- `RefundRequestItem.php` - Refund items model
  - Tracks product variants
  - Relationships: refundRequest, orderProduct, productVariant, vendor

---

## Key Features

### ✅ Variant Tracking
- Each refund item tracks the specific product variant (`product_variant_id`)
- Full variant details included in API responses
- Supports products with multiple variants

### ✅ Two-Level Status System
- **Single Status System**: One unified status for the entire refund request
- Status applies to both admin and vendor views
- Statuses: pending, approved, in_progress, picked_up, refunded, rejected, cancelled
- Both customer and vendor receive notifications when status changes

### ✅ Dynamic Filtering
- All filters handled by `scopeFilter()` in model
- No inline filtering in controllers
- Supports complex searches across relationships

### ✅ Authorization
- Role-based access control
- Admin: Full access to all refunds
- Vendor: Access to their own refunds only
- Customer: Access to their own refunds only

### ✅ Automatic Calculations
- `calculateTotals()` method computes all amounts
- Includes products, tax, shipping, discounts
- Deducts return shipping cost if applicable

### ✅ Observer Pattern
- `RefundRequestObserver` handles all refund lifecycle events
- **Status Change Notifications**: 
  - Notifies customers when status changes
  - Notifies vendors when status changes
  - Both receive Firebase push notifications + Laravel notifications
- **Refund Completion**: When status becomes 'refunded':
  - Updates customer points (deducts/refunds as needed)
  - Marks order products as refunded
  - Updates order's total refunded amount
  - Reverses stock bookings
  - Logs all activities with commission reversal tracking
- All status logic centralized in main RefundRequest observer (not item-level)

### ✅ Notification System
- **RefundNotificationService**: Centralized notification handling
- **Dual Notification Channels**:
  - Laravel Notifications (database/email)
  - Firebase Push Notifications (mobile apps)
- **Automatic Notifications**:
  - Customer: Notified when refund created or status changes
  - Vendor: Notified when new refund request or status changes
- **FCM Token Management**: Automatically cleans up invalid tokens
- **Notification Types**:
  - `refund_created` - Customer receives confirmation
  - `new_refund_request` - Vendor receives alert
  - `refund_status_changed` - Both customer and vendor notified

---

## Response Format

All API responses use the Res trait format:

### Success Response
```json
{
  "status": true,
  "message": "Success message",
  "data": {...},
  "errors": []
}
```

### Error Response
```json
{
  "status": false,
  "message": "Error message",
  "data": [],
  "errors": []
}
```

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

---

## Testing the API

### Using Postman/Insomnia

1. **Authentication**: Include Bearer token in headers
   ```
   Authorization: Bearer {your_sanctum_token}
   ```

2. **Create Refund Request**
   ```
   POST /api/refunds
   Content-Type: application/json
   
   {
     "order_id": 123,
     "reason": "Product damaged",
     "items": [
       {
         "order_product_id": 456,
         "quantity": 1
       }
     ]
   }
   ```

3. **List Refunds with Filters**
   ```
   GET /api/refunds?status=pending&per_page=20
   ```

4. **Update Status**
   ```
   POST /api/refunds/1/status
   Content-Type: application/json
   
   {
     "status": "approved",
     "notes": "Approved by admin"
   }
   ```

---

## Database Schema

### refund_requests
- `id`, `order_id`, `customer_id`, `vendor_id`
- `refund_number` (auto-generated)
- `status`, `vendor_status`
- Amount fields: `total_refund_amount`, `total_products_amount`, etc.
- `reason`, `customer_notes`, `vendor_notes`, `admin_notes`
- Timestamps: `created_at`, `approved_at`, `refunded_at`

### refund_request_items
- `id`, `refund_request_id`, `order_product_id`
- `vendor_id`, `product_variant_id` (tracks specific variant)
- `quantity`, `unit_price`, `total_price`
- Amount fields: `tax_amount`, `discount_amount`, `shipping_amount`

---

## Next Steps

The API is fully functional and ready for:
1. Frontend integration
2. Mobile app integration
3. Third-party integrations
4. Testing and QA

All endpoints follow REST conventions and return consistent JSON responses.

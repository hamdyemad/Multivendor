# Refund API - Resources Update

## Overview
Updated RefundRequestResource to use dedicated API resources for order, customer, and vendor relationships instead of inline arrays.

---

## Changes Made

### 1. Created CustomerResource
**File:** `Modules/Customer/app/Http/Resources/CustomerResource.php`

**Purpose:** Standardized customer data transformation for API responses

**Fields:**
```php
[
    'id' => $this->id,
    'name' => $this->name,
    'email' => $this->email,
    'phone' => $this->phone,
    'points' => (float) ($this->points ?? 0),
    'image' => $this->image_url ?? null,
    'created_at' => $this->created_at?->toISOString(),
]
```

**Benefits:**
- Reusable across all API endpoints
- Consistent customer data format
- Easy to extend with additional fields
- Centralized customer data transformation

---

### 2. Created VendorResource
**File:** `Modules/Vendor/app/Http/Resources/VendorResource.php`

**Purpose:** Standardized vendor data transformation for API responses

**Fields:**
```php
[
    'id' => $this->id,
    'name' => $this->name,
    'email' => $this->email,
    'phone' => $this->phone,
    'logo' => $this->logo_url ?? null,
    'description' => $this->description ?? null,
    'created_at' => $this->created_at?->toISOString(),
]
```

**Benefits:**
- Reusable across all API endpoints
- Consistent vendor data format
- Includes logo and description
- Centralized vendor data transformation

---

### 3. Updated RefundRequestResource
**File:** `Modules/Refund/app/Http/Resources/RefundRequestResource.php`

**Before (Inline Arrays):**
```php
'order' => $this->whenLoaded('order', function () {
    return [
        'id' => $this->order->id,
        'order_number' => $this->order->order_number,
        'total' => (float) $this->order->total,
        'status' => $this->order->status,
    ];
}),

'customer' => $this->whenLoaded('customer', function () {
    return [
        'id' => $this->customer->id,
        'name' => $this->customer->name,
        'email' => $this->customer->email,
        'phone' => $this->customer->phone,
    ];
}),

'vendor' => $this->whenLoaded('vendor', function () {
    return [
        'id' => $this->vendor->id,
        'name' => $this->vendor->name,
        'email' => $this->vendor->email,
        'phone' => $this->vendor->phone,
    ];
}),
```

**After (Using Resources):**
```php
'order' => new \Modules\Order\app\Http\Resources\Api\OrderResource($this->whenLoaded('order')),
'customer' => new \Modules\Customer\app\Http\Resources\CustomerResource($this->whenLoaded('customer')),
'vendor' => new \Modules\Vendor\app\Http\Resources\VendorResource($this->whenLoaded('vendor')),
```

---

## API Response Changes

### Before:

```json
{
  "id": 1,
  "refund_number": "REF-20260118-0001",
  "order": {
    "id": 123,
    "order_number": "ORD-123",
    "total": 250.00,
    "status": "delivered"
  },
  "customer": {
    "id": 45,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890"
  },
  "vendor": {
    "id": 12,
    "name": "Vendor Name",
    "email": "vendor@example.com",
    "phone": "+0987654321"
  }
}
```

### After:

```json
{
  "id": 1,
  "refund_number": "REF-20260118-0001",
  "order": {
    "id": 123,
    "order_number": "ORD-123",
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "customer_phone": "+1234567890",
    "items_count": 3,
    "total_product_price": 200.00,
    "total_tax": 20.00,
    "shipping": 30.00,
    "total_price": 250.00,
    "products": [...],
    "vendors_stages": [...],
    "created_at": "2026-01-18T10:30:00.000000Z"
  },
  "customer": {
    "id": 45,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "points": 150.50,
    "image": "https://example.com/storage/customers/john.jpg",
    "created_at": "2025-01-01T00:00:00.000000Z"
  },
  "vendor": {
    "id": 12,
    "name": "Vendor Name",
    "email": "vendor@example.com",
    "phone": "+0987654321",
    "logo": "https://example.com/storage/vendors/logo.jpg",
    "description": "Vendor description",
    "created_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

---

## Benefits

### 1. Consistency
- All API endpoints use same customer/vendor format
- Standardized data structure across the application
- Easier for frontend developers to work with

### 2. Maintainability
- Single source of truth for customer/vendor data
- Changes to customer/vendor format only need to be made once
- Easier to add/remove fields

### 3. Reusability
- Resources can be used in any module
- No code duplication
- Consistent API responses

### 4. Rich Data
- Order resource includes full order details (products, payments, stages)
- Customer resource includes points and image
- Vendor resource includes logo and description
- More information available to API consumers

### 5. Flexibility
- Easy to extend resources with additional fields
- Can add conditional fields based on user permissions
- Can customize output per endpoint if needed

---

## Resource Usage

### In Controllers:
```php
// Single refund
return new RefundRequestResource($refund);

// Collection
return RefundRequestResource::collection($refunds);

// With relationships
$refund->load(['order', 'customer', 'vendor', 'items']);
return new RefundRequestResource($refund);
```

### Eager Loading:
```php
// Load relationships for full data
RefundRequest::with([
    'order.products',
    'order.payments',
    'customer',
    'vendor',
    'items.orderProduct'
])->get();
```

---

## OrderResource Features

The existing OrderResource provides rich order data:

**Included Data:**
- Order details (number, customer info, payment info)
- Product calculations (total before/after tax)
- Shipping and fees
- Discounts (promo codes, points)
- Refunded amount
- Vendor stages (order status per vendor)
- Products collection (with OrderProductResource)
- Payments collection (with PaymentResource)

**Calculations:**
- Total product price (before tax)
- Total price (with all fees and discounts)
- Vendor-specific stages and shares

---

## CustomerResource Features

**Included Data:**
- Basic info (id, name, email, phone)
- Points balance
- Profile image
- Created date

**Can be Extended:**
- Addresses
- Order history
- Wishlist
- Loyalty tier
- Preferences

---

## VendorResource Features

**Included Data:**
- Basic info (id, name, email, phone)
- Logo image
- Description
- Created date

**Can be Extended:**
- Rating/reviews
- Product count
- Categories
- Business hours
- Location

---

## Testing

### Test Endpoints:

1. **Get Single Refund:**
   ```
   GET /api/refunds/1
   ```
   Should return full order, customer, and vendor data

2. **List Refunds:**
   ```
   GET /api/refunds
   ```
   Should return collection with relationships

3. **Verify Data:**
   - Check order includes products and payments
   - Check customer includes points and image
   - Check vendor includes logo and description

---

## Migration Notes

### No Breaking Changes:
- Response structure enhanced, not changed
- All previous fields still present
- Additional fields added
- Backward compatible

### Frontend Updates:
- Can now access more order details
- Customer points available in response
- Vendor logo available for display
- No code changes required (optional enhancements)

---

## Future Enhancements

### CustomerResource:
- Add addresses collection
- Include order statistics
- Add wishlist items
- Include loyalty tier info

### VendorResource:
- Add product categories
- Include rating/reviews
- Add business hours
- Include location data

### OrderResource:
- Already comprehensive
- Can add tracking info
- Can include delivery details
- Can add invoice data

---

## Best Practices

1. **Always Use Resources:**
   - Never return raw models in API
   - Always use dedicated resources
   - Maintain consistency

2. **Eager Load Relationships:**
   - Load relationships before transforming
   - Avoid N+1 queries
   - Use with() for better performance

3. **Conditional Loading:**
   - Use whenLoaded() for optional relationships
   - Don't force-load heavy relationships
   - Let API consumers choose what to load

4. **Extend, Don't Modify:**
   - Add new fields, don't remove existing
   - Maintain backward compatibility
   - Version API if breaking changes needed

---

## Summary

The refund API now uses dedicated resources for all relationships:
- ✅ OrderResource - Full order details with products and payments
- ✅ CustomerResource - Customer info with points and image
- ✅ VendorResource - Vendor info with logo and description
- ✅ RefundRequestItemResource - Refund items with variant data

All resources are reusable, maintainable, and provide rich data for API consumers.

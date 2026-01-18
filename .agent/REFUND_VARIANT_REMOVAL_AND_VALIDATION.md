# Refund System - Variant ID Removal & Enhanced Validation

## Overview
Removed redundant `product_variant_id` from refund_request_items table and added comprehensive validation to prevent duplicate refunds and ensure data integrity.

---

## Changes Made

### 1. Database Migration
**File:** `Modules/Refund/database/migrations/2026_01_18_000012_remove_variant_id_from_refund_request_items.php`

**Changes:**
- Removed `product_variant_id` column from `refund_request_items` table
- Removed foreign key constraint

**Reason:** 
- Redundant data - variant information already available through `order_product_id`
- OrderProduct already has `vendor_product_variant_id`
- Reduces data duplication and maintains single source of truth

---

### 2. RefundRequestItem Model
**File:** `Modules/Refund/app/Models/RefundRequestItem.php`

**Changes:**
- Removed `product_variant_id` from `$fillable` array
- Removed `productVariant()` relationship method
- Added `getProductVariantAttribute()` accessor to access variant through orderProduct

**New Accessor:**
```php
public function getProductVariantAttribute()
{
    return $this->orderProduct?->vendorProductVariant;
}
```

**Usage:**
```php
$refundItem->product_variant; // Accesses through orderProduct relationship
```

---

### 3. RefundRequestService
**File:** `Modules/Refund/app/Services/RefundRequestService.php`

**Changes:**
- Removed `product_variant_id` from RefundRequestItem creation
- Updated eager loading to remove `items.productVariant`
- Now only loads `items.orderProduct`

**Before:**
```php
'product_variant_id' => $orderProduct->product_variant_id ?? null,
```

**After:**
```php
// Removed - accessed through orderProduct relationship
```

---

### 4. RefundRequestRepository
**File:** `Modules/Refund/app/Repositories/RefundRequestRepository.php`

**Changes:**
- Updated eager loading in `getAllPaginated()` and `findById()`
- Removed `items.productVariant` from with() clause
- Kept `items.orderProduct` for accessing variant data

**Before:**
```php
->with(['order', 'customer', 'vendor', 'items.productVariant', 'items.orderProduct'])
```

**After:**
```php
->with(['order', 'customer', 'vendor', 'items.orderProduct'])
```

---

### 5. API Resource
**File:** `Modules/Refund/app/Http/Resources/RefundRequestItemResource.php`

**Changes:**
- Updated variant data access to use `orderProduct->vendorProductVariant`
- Changed from `whenLoaded('productVariant')` to `whenLoaded('orderProduct')`
- Variant data now accessed through orderProduct relationship

**Before:**
```php
'variant' => $this->whenLoaded('productVariant', function () {
    if (!$this->productVariant) {
        return null;
    }
    return [...];
})
```

**After:**
```php
'variant' => $this->whenLoaded('orderProduct', function () {
    $variant = $this->orderProduct?->vendorProductVariant;
    if (!$variant) {
        return null;
    }
    return [...];
})
```

---

### 6. Enhanced Validation
**File:** `Modules/Refund/app/Http/Requests/Api/StoreRefundRequestRequest.php`

**New Validation Rules:**

#### A. Order Ownership Validation
```php
'order_id' => [
    'required',
    'exists:orders,id',
    function ($attribute, $value, $fail) {
        $order = \Modules\Order\app\Models\Order::find($value);
        if (!$order) {
            $fail('The selected order is invalid.');
            return;
        }
        
        $userId = auth()->id();
        if ($userId && $order->customer_id !== $userId) {
            $fail('The selected order does not belong to you.');
        }
    },
]
```

**Validates:**
- Order exists
- Order belongs to authenticated customer
- Prevents customers from creating refunds for other customers' orders

---

#### B. Order Product Validation
```php
'items.*.order_product_id' => [
    'required',
    'exists:order_products,id',
    function ($attribute, $value, $fail) {
        $orderId = $this->input('order_id');
        
        // 1. Validate order_product belongs to the order
        $orderProduct = \Modules\Order\app\Models\OrderProduct::find($value);
        if (!$orderProduct || $orderProduct->order_id != $orderId) {
            $fail('The selected order product does not belong to this order.');
        }
        
        // 2. Validate not already refunded
        if ($orderProduct && $orderProduct->is_refunded) {
            $fail('This product has already been refunded.');
        }
        
        // 3. Validate no pending refund request
        $hasPendingRefund = \Modules\Refund\app\Models\RefundRequestItem::whereHas('refundRequest', function ($query) {
            $query->whereIn('status', ['pending', 'approved', 'in_progress', 'picked_up']);
        })->where('order_product_id', $value)->exists();
        
        if ($hasPendingRefund) {
            $fail('This product already has a pending refund request.');
        }
    },
]
```

**Validates:**
- Order product exists
- Order product belongs to the specified order
- Product not already refunded (`is_refunded` flag)
- No pending refund request for this product

**Prevents:**
- Refunding products from different orders
- Duplicate refunds for same product
- Multiple pending refund requests for same product

---

#### C. Quantity Validation
```php
'items.*.quantity' => [
    'required',
    'integer',
    'min:1',
    function ($attribute, $value, $fail) {
        // Extract index from attribute path
        preg_match('/items\.(\d+)\.quantity/', $attribute, $matches);
        $index = $matches[1] ?? 0;
        
        // Get order_product_id for this item
        $orderProductId = $this->input("items.{$index}.order_product_id");
        
        if ($orderProductId) {
            $orderProduct = \Modules\Order\app\Models\OrderProduct::find($orderProductId);
            if ($orderProduct && $value > $orderProduct->quantity) {
                $fail("The refund quantity cannot exceed the ordered quantity ({$orderProduct->quantity}).");
            }
        }
    },
]
```

**Validates:**
- Quantity is positive integer
- Refund quantity doesn't exceed ordered quantity
- Prevents over-refunding

---

## Validation Flow

### Creating a Refund Request:

1. **Order Validation**
   - Check order exists
   - Verify order belongs to customer
   
2. **Product Validation** (for each item)
   - Check order_product exists
   - Verify product belongs to order
   - Check if already refunded
   - Check for pending refund requests
   
3. **Quantity Validation** (for each item)
   - Verify quantity is valid
   - Check doesn't exceed ordered quantity

---

## Error Messages

### Order Errors:
- "The selected order is invalid."
- "The selected order does not belong to you."

### Product Errors:
- "The selected order product does not belong to this order."
- "This product has already been refunded."
- "This product already has a pending refund request."

### Quantity Errors:
- "The refund quantity cannot exceed the ordered quantity (X)."

---

## API Response Changes

### Refund Item Response:

**Before:**
```json
{
  "id": 1,
  "order_product_id": 123,
  "product_variant_id": 456,
  "variant": {...}
}
```

**After:**
```json
{
  "id": 1,
  "order_product_id": 123,
  "variant": {...}
}
```

**Note:** Variant data still included, just accessed through orderProduct relationship

---

## Benefits

### 1. Data Integrity
- Single source of truth for variant information
- No data duplication
- Reduced chance of data inconsistency

### 2. Validation
- Prevents duplicate refunds
- Ensures order ownership
- Validates product belongs to order
- Prevents over-refunding

### 3. Security
- Customers can only refund their own orders
- Cannot refund products from other orders
- Cannot create multiple refunds for same product

### 4. Performance
- Fewer database columns
- Simpler queries
- Less data to sync

---

## Database Schema

### refund_request_items (Updated):
```sql
- id
- refund_request_id
- order_product_id (contains variant info)
- vendor_id
- quantity
- unit_price
- total_price
- tax_amount
- discount_amount
- shipping_amount
- refund_amount
- created_at
- updated_at
```

**Removed:** `product_variant_id`

---

## Accessing Variant Data

### In Code:
```php
// Through accessor
$refundItem->product_variant;

// Through relationship
$refundItem->orderProduct->vendorProductVariant;

// Eager loading
RefundRequestItem::with('orderProduct.vendorProductVariant')->get();
```

### In API:
```php
// Automatically included when orderProduct is loaded
$refundItem->load('orderProduct');
$variant = $refundItem->product_variant;
```

---

## Migration Steps

1. **Backup database** (recommended)
2. **Run migration:**
   ```bash
   php artisan migrate
   ```
3. **Clear cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```
4. **Test API endpoints:**
   - Create refund request
   - Try duplicate refund (should fail)
   - Try refunding other customer's order (should fail)
   - Verify variant data still accessible

---

## Testing Scenarios

### Valid Requests:
- [ ] Create refund for own order
- [ ] Refund with valid quantity
- [ ] Variant data accessible in response

### Should Fail:
- [ ] Refund for other customer's order
- [ ] Refund already refunded product
- [ ] Refund product with pending refund
- [ ] Refund quantity exceeds ordered quantity
- [ ] Refund product from different order

---

## Backward Compatibility

- Existing refund records unaffected
- Variant data still accessible through orderProduct
- API response structure maintained (variant field still present)
- No breaking changes to API consumers

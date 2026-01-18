# Refund Variant Tracking Summary

## 🎯 Changes Made

### 1. Variant Tracking in Refund Items

The refund system now properly tracks the specific product variant being refunded, not just the product ID.

**RefundRequestItem Model:**
- ✅ `product_variant_id` - Tracks the specific variant being refunded
- ✅ `vendor_id` - Tracks which vendor the item belongs to
- ✅ Relationship to `VendorProductVariant` model
- ✅ Relationship to `Vendor` model

### 2. Moved Vendor Fields to Refund Request

Vendor-specific fields have been moved from individual items to the refund request level, as they apply to the entire refund, not individual items.

**Moved from `refund_request_items` to `refund_requests`:**
- ✅ `vendor_status` - Vendor's tracking status (pending, approved, in_progress, picked_up, refunded, rejected)
- ✅ `vendor_notes` - Vendor's notes about the refund
- ✅ Removed `approved_at` and `refunded_at` from items (already on request level)

## 📊 Database Structure

### refund_requests Table
```sql
- id
- order_id
- customer_id
- vendor_id                    ← Added
- refund_number
- status                       ← Admin/System status
- vendor_status                ← Added: Vendor's tracking status
- total_products_amount
- total_shipping_amount
- total_tax_amount
- return_shipping_cost
- points_to_deduct
- total_refund_amount
- reason
- customer_notes
- vendor_notes                 ← Added
- admin_notes
- approved_at
- refunded_at
- created_at
- updated_at
- deleted_at
```

### refund_request_items Table
```sql
- id
- refund_request_id
- order_product_id
- vendor_id                    ← Tracks item's vendor
- product_variant_id           ← Tracks specific variant
- quantity
- unit_price
- total_price
- tax_amount
- discount_amount
- refund_amount
- created_at
- updated_at
```

## 🔄 Status Tracking

### Two-Level Status System

**1. Admin/System Status (`status`):**
- Controls the overall refund workflow
- Used by admin and system
- Values: pending, approved, in_progress, picked_up, refunded, rejected, cancelled

**2. Vendor Status (`vendor_status`):**
- Vendor's internal tracking
- Independent from admin status
- Values: pending, approved, in_progress, picked_up, refunded, rejected
- Allows vendor to track their own process

### Example Flow:
```
Admin Status:     pending → approved → in_progress → refunded
Vendor Status:    pending → approved → picked_up → refunded
```

## 📝 Updated Models

### RefundRequest Model
```php
protected $fillable = [
    'order_id',
    'customer_id',
    'vendor_id',              // Added
    'refund_number',
    'status',
    'vendor_status',          // Added
    'total_products_amount',
    // ... other amounts
    'reason',
    'customer_notes',
    'vendor_notes',           // Added
    'admin_notes',
    'approved_at',
    'refunded_at',
];
```

### RefundRequestItem Model
```php
protected $fillable = [
    'refund_request_id',
    'order_product_id',
    'vendor_id',              // Tracks item's vendor
    'product_variant_id',     // Tracks specific variant
    'quantity',
    'unit_price',
    'total_price',
    'tax_amount',
    'discount_amount',
    'refund_amount',
];

// Relationships
public function productVariant(): BelongsTo
{
    return $this->belongsTo(VendorProductVariant::class);
}

public function vendor(): BelongsTo
{
    return $this->belongsTo(Vendor::class);
}

public function orderProduct(): BelongsTo
{
    return $this->belongsTo(OrderProduct::class);
}
```

## 🎨 API Response Structure

### Refund Request with Variant Information

```json
{
    "id": 1,
    "refund_number": "REF-20260118-0001",
    "order_id": 123,
    "customer_id": 45,
    "vendor_id": 10,
    "status": "pending",
    "status_label": "Pending",
    "vendor_status": "pending",
    "vendor_status_label": "Pending",
    
    "total_refund_amount": 150.00,
    
    "reason": "Product damaged",
    "customer_notes": "Item arrived broken",
    "vendor_notes": "Will process return",
    "admin_notes": null,
    
    "items": [
        {
            "id": 1,
            "refund_request_id": 1,
            "order_product_id": 456,
            "vendor_id": 10,
            "product_variant_id": 789,
            "quantity": 2,
            "unit_price": 70.00,
            "total_price": 140.00,
            
            "variant": {
                "id": 789,
                "sku": "VAR-SKU-123",
                "price": 70.00,
                "stock": 50,
                "variant_values": {
                    "color": "Red",
                    "size": "Large"
                },
                "image": "https://example.com/variant.jpg"
            },
            
            "order_product": {
                "id": 456,
                "product_id": 100,
                "product_name": "T-Shirt",
                "product_sku": "PROD-SKU-100",
                "variant_details": "Color: Red, Size: Large",
                "price": 70.00,
                "quantity": 2
            }
        }
    ]
}
```

## 🔧 Service Layer Updates

### RefundRequestService::createRefund()

```php
// Create refund items with variant tracking
foreach ($data['items'] as $item) {
    $orderProduct = OrderProduct::findOrFail($item['order_product_id']);

    RefundRequestItem::create([
        'refund_request_id' => $refund->id,
        'order_product_id' => $orderProduct->id,
        'vendor_id' => $orderProduct->vendor_id ?? $order->vendor_id,
        'product_variant_id' => $orderProduct->product_variant_id ?? null,  // Variant tracking
        'quantity' => $item['quantity'],
        'unit_price' => $orderProduct->price,
        'total_price' => $orderProduct->price * $item['quantity'],
    ]);
}
```

## 📦 Repository Updates

### Eager Loading Relationships

```php
public function getAllPaginated(array $filters = [], int $perPage = 15)
{
    return $this->model
        ->with([
            'order',
            'customer',
            'vendor',
            'items.productVariant',    // Load variant info
            'items.orderProduct'       // Load order product info
        ])
        ->scopeFilters($filters)
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
}
```

## ✅ Benefits

### 1. Accurate Variant Tracking
- Know exactly which variant is being refunded
- Track variant-specific stock adjustments
- Handle variant-specific pricing

### 2. Proper Vendor Management
- Vendor status at request level (not item level)
- Vendor notes for entire refund
- Clear vendor ownership per item

### 3. Better Inventory Management
- Return stock to correct variant
- Track variant-specific refund rates
- Accurate variant availability

### 4. Enhanced Reporting
- Refund statistics by variant
- Identify problematic variants
- Better inventory forecasting

## 🎯 Use Cases

### Example 1: Multi-Variant Refund
```
Order contains:
- T-Shirt Red/Large (Variant ID: 789) - Qty: 2
- T-Shirt Blue/Medium (Variant ID: 790) - Qty: 1

Customer refunds:
- T-Shirt Red/Large - Qty: 1 (damaged)

System tracks:
- Refund for variant 789 specifically
- Stock returns to variant 789
- Variant 790 not affected
```

### Example 2: Vendor Status Tracking
```
Admin Status:     pending → approved → in_progress → refunded
Vendor Status:    pending → approved → picked_up → refunded

Vendor can:
- Mark as "approved" when they accept return
- Mark as "picked_up" when courier collects
- Mark as "refunded" when they process refund
- Add notes at each step
```

## 📋 Migration Details

**Migration File:** `2026_01_18_000010_move_vendor_fields_to_refund_requests.php`

**Changes:**
1. Added to `refund_requests`:
   - `vendor_id` (foreign key)
   - `vendor_status` (enum)
   - `vendor_notes` (text)

2. Removed from `refund_request_items`:
   - `vendor_status`
   - `vendor_notes`
   - `approved_at`
   - `refunded_at`

**Rollback:** Migration includes proper down() method to reverse changes if needed.

## 🔍 Key Points

1. ✅ **Variant Tracking**: Each refund item tracks the specific variant
2. ✅ **Vendor Fields**: Moved to request level (applies to entire refund)
3. ✅ **Two Status Systems**: Admin status + Vendor status
4. ✅ **Proper Relationships**: Variant, OrderProduct, Vendor
5. ✅ **API Resources**: Include full variant information
6. ✅ **Stock Management**: Return to correct variant
7. ✅ **Backward Compatible**: Handles products without variants (null variant_id)

## 📝 Summary

The refund system now properly tracks:
- **Which specific variant** is being refunded (color, size, etc.)
- **Vendor status** at the refund request level
- **Vendor notes** for the entire refund
- **Complete variant information** in API responses

This enables accurate inventory management, better reporting, and proper vendor workflow tracking.

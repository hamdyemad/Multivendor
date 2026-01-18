# Refund API Resources & Validation Summary

## 📁 File Structure

```
Modules/Refund/app/Http/
├── Controllers/
│   └── Api/
│       └── RefundRequestApiController.php       # API Controller (uses API resources)
├── Requests/
│   └── Api/                                     # API-specific validation requests
│       ├── StoreRefundRequestRequest.php        # Validation for creating refunds (API)
│       └── UpdateRefundStatusRequest.php        # Validation for status updates (API)
└── Resources/                                   # API Resources for response formatting
    ├── RefundRequestResource.php                # Single refund resource
    ├── RefundRequestItemResource.php            # Refund item resource
    └── RefundRequestCollection.php              # Paginated collection resource
```

## 🎯 Separation of Concerns

### API Requests (Modules/Refund/app/Http/Requests/Api/)
- **Purpose**: Validate API requests
- **Format**: JSON error responses
- **Location**: `Api/` subfolder
- **Usage**: API endpoints only

### Dashboard Requests (Future)
- **Purpose**: Validate web form submissions
- **Format**: Redirect with errors
- **Location**: `Modules/Refund/app/Http/Requests/`
- **Usage**: Dashboard controllers only

## 📝 API Form Requests

### 1. StoreRefundRequestRequest

**Purpose**: Validate refund creation requests from API

**Rules:**
```php
'order_id' => 'required|exists:orders,id',
'reason' => 'required|string|max:500',
'customer_notes' => 'nullable|string|max:1000',
'items' => 'required|array|min:1',
'items.*.order_product_id' => 'required|exists:order_products,id',
'items.*.quantity' => 'required|integer|min:1',
'items.*.reason' => 'nullable|string|max:500',
```

**Error Response Format:**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "order_id": ["The order field is required."],
        "items": ["At least one item is required."]
    }
}
```

### 2. UpdateRefundStatusRequest

**Purpose**: Validate status update requests from API

**Rules:**
```php
'status' => 'required|in:pending,approved,in_progress,picked_up,refunded,rejected,cancelled',
'notes' => 'nullable|string|max:1000',
```

**Error Response Format:**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "status": ["The selected status is invalid."]
    }
}
```

## 🎨 API Resources

### 1. RefundRequestResource

**Purpose**: Transform single refund request for API response

**Output Structure:**
```json
{
    "id": 1,
    "refund_number": "REF-20260118-0001",
    "order_id": 123,
    "customer_id": 45,
    "vendor_id": 10,
    "status": "pending",
    "status_label": "Pending",
    
    "total_refund_amount": 150.00,
    "total_products_amount": 140.00,
    "total_shipping_amount": 10.00,
    "total_tax_amount": 0.00,
    "return_shipping_cost": 0.00,
    
    "reason": "Product damaged",
    "customer_notes": "Item arrived broken",
    "vendor_notes": null,
    "admin_notes": null,
    
    "created_at": "2026-01-18T10:30:00.000000Z",
    "updated_at": "2026-01-18T10:30:00.000000Z",
    "approved_at": null,
    "refunded_at": null,
    
    "order": {
        "id": 123,
        "order_number": "ORD-20260115-0123",
        "total": 150.00,
        "status": "delivered"
    },
    
    "customer": {
        "id": 45,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+1234567890"
    },
    
    "vendor": {
        "id": 10,
        "name": "Tech Store",
        "email": "vendor@example.com",
        "phone": "+0987654321"
    },
    
    "items": [
        {
            "id": 1,
            "quantity": 2,
            "unit_price": 70.00,
            "total_price": 140.00,
            "reason": "Damaged",
            "product": {
                "id": 789,
                "name": "Product Name",
                "sku": "SKU-123",
                "image": "https://..."
            }
        }
    ]
}
```

**Features:**
- ✅ Converts decimals to floats
- ✅ Formats dates to ISO 8601
- ✅ Includes status label translation
- ✅ Conditional relationships (whenLoaded)
- ✅ Nested resources for items

### 2. RefundRequestItemResource

**Purpose**: Transform refund item for API response

**Output Structure:**
```json
{
    "id": 1,
    "refund_request_id": 1,
    "order_product_id": 456,
    "product_id": 789,
    "quantity": 2,
    "unit_price": 70.00,
    "total_price": 140.00,
    "reason": "Damaged on arrival",
    "product": {
        "id": 789,
        "name": "Product Name",
        "sku": "SKU-123",
        "image": "https://..."
    },
    "created_at": "2026-01-18T10:30:00.000000Z",
    "updated_at": "2026-01-18T10:30:00.000000Z"
}
```

### 3. RefundRequestCollection

**Purpose**: Transform paginated collection of refunds

**Output Structure:**
```json
{
    "data": [
        { /* RefundRequestResource */ },
        { /* RefundRequestResource */ }
    ],
    "meta": {
        "total": 50,
        "per_page": 15,
        "current_page": 1,
        "last_page": 4,
        "from": 1,
        "to": 15
    }
}
```

## 🔄 Controller Usage

### Before (Without Resources):
```php
public function index(Request $request)
{
    $refunds = $this->refundService->getAllRefunds($filters, $perPage);
    
    return response()->json([
        'success' => true,
        'data' => $refunds->items(),
        'pagination' => [
            'total' => $refunds->total(),
            // ... manual pagination
        ],
    ]);
}
```

### After (With Resources):
```php
public function index(Request $request)
{
    $refunds = $this->refundService->getAllRefunds($filters, $perPage);
    
    return new RefundRequestCollection(
        RefundRequestResource::collection($refunds)
    );
}
```

## ✅ Benefits

### 1. Separation of Concerns
- API validation separate from dashboard validation
- Different error formats for different contexts
- Easy to maintain and update independently

### 2. Consistent API Responses
- All refund responses have same structure
- Automatic data transformation
- Type casting (decimals to floats)
- Date formatting (ISO 8601)

### 3. Clean Controller Code
- Controllers are thin and focused
- No manual response formatting
- Reusable resource classes

### 4. Easy to Extend
- Add new fields in one place (resource)
- Change response format globally
- Add computed fields easily

### 5. Better Documentation
- Resources serve as API documentation
- Clear structure for frontend developers
- Type-safe responses

## 🎯 Key Differences: API vs Dashboard

| Feature | API Requests | Dashboard Requests (Future) |
|---------|-------------|---------------------------|
| **Location** | `Requests/Api/` | `Requests/` |
| **Error Format** | JSON | Redirect with errors |
| **Response** | `HttpResponseException` | Default Laravel behavior |
| **Usage** | API endpoints | Web forms |
| **Validation** | Same rules, different format | Same rules, different format |

## 📚 Usage Examples

### Creating a Refund (API)
```php
POST /api/v1/refunds
Content-Type: application/json

{
    "order_id": 123,
    "reason": "Product damaged",
    "customer_notes": "Item arrived broken",
    "items": [
        {
            "order_product_id": 456,
            "quantity": 2,
            "reason": "Damaged"
        }
    ]
}

// Response uses RefundRequestResource
{
    "success": true,
    "message": "Refund request created successfully",
    "data": { /* RefundRequestResource */ }
}
```

### Validation Error (API)
```php
POST /api/v1/refunds
Content-Type: application/json

{
    "order_id": 999999,  // Invalid
    "items": []          // Empty
}

// Response from StoreRefundRequestRequest
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "order_id": ["The selected order is invalid."],
        "items": ["At least one item is required."]
    }
}
```

## 🔧 Future: Dashboard Requests

When you need dashboard validation, create:

```
Modules/Refund/app/Http/Requests/
├── StoreRefundRequest.php           # For dashboard create form
└── UpdateRefundRequest.php          # For dashboard update form
```

These will:
- Use same validation rules
- Return redirect with errors (not JSON)
- Work with web forms
- Be separate from API requests

## 📝 Summary

✅ **Created:**
- 2 API-specific Form Requests with JSON error responses
- 3 API Resources for consistent response formatting
- Proper separation between API and dashboard validation

✅ **Benefits:**
- Clean, maintainable code
- Consistent API responses
- Type-safe data transformation
- Easy to extend and modify
- Clear separation of concerns

✅ **Ready for:**
- API consumption by mobile apps
- Frontend frameworks (React, Vue, etc.)
- Third-party integrations
- Future dashboard validation requests

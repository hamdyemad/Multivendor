# Refund API Documentation

## Base URL
```
/api/v1/refunds
```

## Authentication
All endpoints require authentication using Sanctum token:
```
Authorization: Bearer {token}
```

---

## Endpoints

### 1. List Refund Requests

**GET** `/api/v1/refunds`

Get a paginated list of refund requests.

**Query Parameters:**
- `status` (optional) - Filter by status: pending, approved, in_progress, picked_up, refunded, rejected, cancelled
- `customer_id` (optional) - Filter by customer ID
- `vendor_id` (optional) - Filter by vendor ID
- `per_page` (optional) - Items per page (default: 15)
- `page` (optional) - Page number

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "refund_number": "REF-20260118-0001",
            "order_id": 123,
            "customer_id": 45,
            "vendor_id": 10,
            "status": "pending",
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
            "order": { ... },
            "customer": { ... },
            "vendor": { ... },
            "items": [ ... ]
        }
    ],
    "pagination": {
        "total": 50,
        "per_page": 15,
        "current_page": 1,
        "last_page": 4,
        "from": 1,
        "to": 15
    }
}
```

---

### 2. Get Refund Request Details

**GET** `/api/v1/refunds/{id}`

Get details of a specific refund request.

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "refund_number": "REF-20260118-0001",
        "order_id": 123,
        "customer_id": 45,
        "vendor_id": 10,
        "status": "pending",
        "total_refund_amount": 150.00,
        "reason": "Product damaged",
        "customer_notes": "Item arrived broken",
        "order": {
            "id": 123,
            "order_number": "ORD-20260115-0123",
            ...
        },
        "customer": {
            "id": 45,
            "name": "John Doe",
            ...
        },
        "vendor": {
            "id": 10,
            "name": "Tech Store",
            ...
        },
        "items": [
            {
                "id": 1,
                "refund_request_id": 1,
                "order_product_id": 456,
                "product_id": 789,
                "quantity": 2,
                "unit_price": 70.00,
                "total_price": 140.00,
                "reason": "Damaged",
                "product": {
                    "id": 789,
                    "name": "Product Name",
                    ...
                }
            }
        ]
    }
}
```

---

### 3. Create Refund Request

**POST** `/api/v1/refunds`

Create a new refund request (Customer only).

**Request Body:**
```json
{
    "order_id": 123,
    "reason": "Product damaged",
    "customer_notes": "Item arrived broken",
    "items": [
        {
            "order_product_id": 456,
            "quantity": 2,
            "reason": "Damaged on arrival"
        }
    ]
}
```

**Validation Rules:**
- `order_id` - required, must exist in orders table
- `reason` - required, string, max 500 characters
- `customer_notes` - optional, string, max 1000 characters
- `items` - required, array, minimum 1 item
- `items.*.order_product_id` - required, must exist in order_products table
- `items.*.quantity` - required, integer, minimum 1
- `items.*.reason` - optional, string, max 500 characters

**Response:**
```json
{
    "success": true,
    "message": "Refund request created successfully",
    "data": {
        "id": 1,
        "refund_number": "REF-20260118-0001",
        "order_id": 123,
        "status": "pending",
        ...
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Unauthorized access to this order"
}
```

---

### 4. Update Refund Status

**POST** `/api/v1/refunds/{id}/status`

Update the status of a refund request (Admin/Vendor only).

**Request Body:**
```json
{
    "status": "approved",
    "notes": "Approved for refund processing"
}
```

**Validation Rules:**
- `status` - required, must be one of: pending, approved, in_progress, picked_up, refunded, rejected, cancelled
- `notes` - optional, string, max 1000 characters

**Response:**
```json
{
    "success": true,
    "message": "Refund status updated successfully",
    "data": {
        "id": 1,
        "status": "approved",
        "vendor_notes": "Approved for refund processing",
        ...
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

---

### 5. Cancel Refund Request

**POST** `/api/v1/refunds/{id}/cancel`

Cancel a refund request (Customer only, pending status only).

**Response:**
```json
{
    "success": true,
    "message": "Refund request cancelled successfully",
    "data": {
        "id": 1,
        "status": "cancelled",
        ...
    }
}
```

**Error Response (400):**
```json
{
    "success": false,
    "message": "Cannot cancel refund request in current status"
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

---

### 6. Get Refund Statistics

**GET** `/api/v1/refunds/statistics`

Get refund statistics (counts by status and total amount).

**Query Parameters:**
- `customer_id` (optional) - Filter by customer ID

**Response:**
```json
{
    "success": true,
    "data": {
        "total": 150,
        "pending": 25,
        "approved": 30,
        "in_progress": 20,
        "picked_up": 15,
        "refunded": 50,
        "rejected": 8,
        "cancelled": 2,
        "total_amount": 15000.00
    }
}
```

---

## Status Flow

```
pending → approved → in_progress → picked_up → refunded
   ↓
rejected
   ↓
cancelled (customer only, from pending)
```

### Status Descriptions:

- **pending** - Initial status when refund request is created
- **approved** - Admin/Vendor approved the refund request
- **in_progress** - Refund processing has started
- **picked_up** - Product has been picked up from customer
- **refunded** - Refund amount has been processed (triggers observer)
- **rejected** - Admin/Vendor rejected the refund request
- **cancelled** - Customer cancelled the request (only from pending)

---

## Authorization Rules

### Customer:
- Can create refund requests for their own orders
- Can view their own refund requests
- Can cancel their own pending refund requests
- Cannot update status or add vendor/admin notes

### Vendor:
- Can view refund requests for their products
- Can update status of refund requests
- Can add vendor notes
- Cannot create refund requests

### Admin:
- Can view all refund requests
- Can update status of any refund request
- Can add admin notes
- Full access to all operations

---

## Error Responses

### Validation Error (422):
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "order_id": [
            "The order id field is required."
        ],
        "items": [
            "The items field is required."
        ]
    }
}
```

### Not Found (404):
```json
{
    "message": "No query results for model [Modules\\Refund\\app\\Models\\RefundRequest] {id}"
}
```

### Unauthorized (401):
```json
{
    "message": "Unauthenticated."
}
```

### Forbidden (403):
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

---

## Example Usage

### Using cURL:

```bash
# List refund requests
curl -X GET "https://api.example.com/api/v1/refunds?status=pending&per_page=10" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Create refund request
curl -X POST "https://api.example.com/api/v1/refunds" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
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
  }'

# Update status
curl -X POST "https://api.example.com/api/v1/refunds/1/status" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "approved",
    "notes": "Approved for processing"
  }'

# Get statistics
curl -X GET "https://api.example.com/api/v1/refunds/statistics" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Using JavaScript (Fetch):

```javascript
// List refund requests
const response = await fetch('/api/v1/refunds?status=pending', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});
const data = await response.json();

// Create refund request
const response = await fetch('/api/v1/refunds', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    order_id: 123,
    reason: 'Product damaged',
    items: [
      {
        order_product_id: 456,
        quantity: 2,
        reason: 'Damaged'
      }
    ]
  })
});
const data = await response.json();
```

---

## Notes

1. All endpoints require authentication via Sanctum token
2. Dates are returned in ISO 8601 format (UTC)
3. Amounts are in decimal format with 2 decimal places
4. The refund observer automatically triggers when status changes to "refunded"
5. Vendor-specific filtering is automatically applied for non-admin users
6. Customer can only access their own refund requests

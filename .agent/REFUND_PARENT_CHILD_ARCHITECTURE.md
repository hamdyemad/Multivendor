# Refund System - Parent/Child Architecture

## Overview
The refund system now supports a parent/child architecture where:
- **Customer creates ONE refund request** (parent) for an order
- **System automatically creates separate refund requests per vendor** (children)
- **Dashboard shows vendor-specific refunds** for processing
- **Customer sees their single refund request** with all items

---

## Architecture

### Parent Refund (Customer View)
- Created when customer submits refund request
- `is_parent = true`
- `parent_refund_id = null`
- `vendor_id = null` (not specific to any vendor)
- Contains ALL items from the order (across all vendors)
- Customer sees this single refund

### Child Refunds (Vendor View)
- Automatically created for each vendor
- `is_parent = false`
- `parent_refund_id = {parent_id}`
- `vendor_id = {specific_vendor}`
- Contains only items for that specific vendor
- Dashboard/vendors see these refunds

---

## Database Schema

### Migration Added
**File:** `2026_01_18_000013_add_parent_refund_to_refund_requests.php`

**New Fields:**
```sql
parent_refund_id (nullable, foreign key to refund_requests)
is_parent (boolean, default false)
```

**Indexes:**
- `parent_refund_id` - For quick lookup of vendor refunds
- `is_parent` - For filtering parent vs vendor refunds

---

## Example Scenario

### Customer Order:
```
Order #123
- Product A (Vendor 1) - $50
- Product B (Vendor 1) - $30
- Product C (Vendor 2) - $40
- Product D (Vendor 2) - $60
- Product E (Vendor 3) - $25
- Product F (Vendor 3) - $35
```

### Customer Creates Refund:
Customer wants to refund products A, C, D, and E

### System Creates:

#### 1. Parent Refund (Customer sees this)
```json
{
  "id": 100,
  "refund_number": "REF-20260118-0100",
  "is_parent": true,
  "parent_refund_id": null,
  "vendor_id": null,
  "customer_id": 45,
  "order_id": 123,
  "status": "pending",
  "total_refund_amount": 175.00,
  "items": [
    {"product": "A", "vendor_id": 1, "price": 50},
    {"product": "C", "vendor_id": 2, "price": 40},
    {"product": "D", "vendor_id": 2, "price": 60},
    {"product": "E", "vendor_id": 3, "price": 25}
  ],
  "vendor_refunds": [
    {"id": 101, "vendor_id": 1, ...},
    {"id": 102, "vendor_id": 2, ...},
    {"id": 103, "vendor_id": 3, ...}
  ]
}
```

#### 2. Vendor 1 Refund (Dashboard sees this)
```json
{
  "id": 101,
  "refund_number": "REF-20260118-0101",
  "is_parent": false,
  "parent_refund_id": 100,
  "vendor_id": 1,
  "customer_id": 45,
  "order_id": 123,
  "status": "pending",
  "total_refund_amount": 50.00,
  "items": [
    {"product": "A", "vendor_id": 1, "price": 50}
  ]
}
```

#### 3. Vendor 2 Refund (Dashboard sees this)
```json
{
  "id": 102,
  "refund_number": "REF-20260118-0102",
  "is_parent": false,
  "parent_refund_id": 100,
  "vendor_id": 2,
  "customer_id": 45,
  "order_id": 123,
  "status": "pending",
  "total_refund_amount": 100.00,
  "items": [
    {"product": "C", "vendor_id": 2, "price": 40},
    {"product": "D", "vendor_id": 2, "price": 60}
  ]
}
```

#### 4. Vendor 3 Refund (Dashboard sees this)
```json
{
  "id": 103,
  "refund_number": "REF-20260118-0103",
  "is_parent": false,
  "parent_refund_id": 100,
  "vendor_id": 3,
  "customer_id": 45,
  "order_id": 123,
  "status": "pending",
  "total_refund_amount": 25.00,
  "items": [
    {"product": "E", "vendor_id": 3, "price": 25}
  ]
}
```

---

## Model Relationships

### RefundRequest Model

**New Relationships:**
```php
// Get parent refund (for vendor refunds)
public function parentRefund(): BelongsTo

// Get child vendor refunds (for customer refunds)
public function vendorRefunds(): HasMany

// Scope to get only parent refunds
public function scopeParentOnly($query)

// Scope to get only vendor refunds
public function scopeVendorOnly($query)
```

---

## Service Layer

### RefundRequestService

**New Methods:**

#### `groupItemsByVendor(array $items)`
Groups refund items by their vendor ID

#### `createVendorRefund($parentRefund, $order, $vendorId, $items, $originalData)`
Creates a vendor-specific refund request linked to parent

**Flow:**
1. Customer submits refund request
2. Service groups items by vendor
3. Creates parent refund (is_parent=true)
4. Creates vendor refund for each vendor (is_parent=false)
5. Each vendor refund gets only their items
6. Parent refund gets all items
7. Calculates totals for each refund
8. Sends notifications

---

## API Endpoints

### Customer View (Mobile App)

**Get My Refunds:**
```
GET /api/refunds?show_parent=true&customer_id={id}
```

Returns parent refunds only (customer's view)

**Response:**
```json
{
  "data": [
    {
      "id": 100,
      "refund_number": "REF-20260118-0100",
      "is_parent": true,
      "total_refund_amount": 175.00,
      "vendor_refunds": [
        {"id": 101, "vendor_id": 1, "status": "pending"},
        {"id": 102, "vendor_id": 2, "status": "approved"},
        {"id": 103, "vendor_id": 3, "status": "pending"}
      ]
    }
  ]
}
```

### Dashboard View (Admin/Vendor)

**Get Refunds for Processing:**
```
GET /api/refunds
```

Returns vendor refunds only (dashboard view) - default behavior

**Response:**
```json
{
  "data": [
    {
      "id": 101,
      "refund_number": "REF-20260118-0101",
      "is_parent": false,
      "parent_refund_id": 100,
      "vendor_id": 1,
      "total_refund_amount": 50.00,
      "parent_refund": {
        "id": 100,
        "refund_number": "REF-20260118-0100"
      }
    },
    {
      "id": 102,
      "refund_number": "REF-20260118-0102",
      "is_parent": false,
      "parent_refund_id": 100,
      "vendor_id": 2,
      "total_refund_amount": 100.00
    }
  ]
}
```

### Vendor View

**Get My Vendor's Refunds:**
```
GET /api/refunds?vendor_id={vendor_id}
```

Returns only refunds for specific vendor

---

## Status Management

### Independent Status per Vendor

Each vendor refund has its own status:
- Vendor 1 can approve their refund
- Vendor 2 can reject their refund
- Vendor 3 can process their refund

### Parent Status Calculation

Parent refund status is calculated based on vendor refunds:
- **All pending** → Parent: pending
- **Any approved** → Parent: approved
- **All refunded** → Parent: refunded
- **Any rejected** → Parent: partially_rejected (new status?)
- **Mixed statuses** → Parent: in_progress

---

## Notifications

### Customer Notifications
- Notified when parent refund is created
- Notified when ANY vendor refund status changes
- Sees overall refund progress

### Vendor Notifications
- Each vendor notified about their specific refund
- Only sees their own refund items
- Can manage their refund independently

---

## Benefits

### 1. Vendor Independence
- Each vendor processes their own refunds
- No dependency on other vendors
- Faster processing

### 2. Customer Simplicity
- Customer sees one refund request
- Don't need to track multiple refunds
- Clear overview of all items

### 3. Dashboard Clarity
- Admin sees all vendor refunds separately
- Can track each vendor's performance
- Better reporting and analytics

### 4. Flexible Processing
- Vendors can approve/reject independently
- Partial refunds possible
- Better inventory management

---

## Query Examples

### Get Customer's Refunds (Parent Only)
```php
RefundRequest::parentOnly()
    ->where('customer_id', $customerId)
    ->with('vendorRefunds')
    ->get();
```

### Get Vendor's Refunds (Vendor Only)
```php
RefundRequest::vendorOnly()
    ->where('vendor_id', $vendorId)
    ->with('parentRefund')
    ->get();
```

### Get All Refunds for Dashboard
```php
RefundRequest::vendorOnly()
    ->with(['vendor', 'customer', 'parentRefund'])
    ->paginate(15);
```

### Get Parent with All Vendor Refunds
```php
RefundRequest::parentOnly()
    ->with(['vendorRefunds.vendor', 'vendorRefunds.items'])
    ->find($id);
```

---

## Data Flow

### Creating Refund:
```
Customer submits refund
    ↓
API receives request
    ↓
Service validates order ownership
    ↓
Service groups items by vendor
    ↓
Create parent refund (is_parent=true)
    ↓
For each vendor:
    ↓
    Create vendor refund (is_parent=false)
    ↓
    Add vendor's items
    ↓
    Calculate vendor totals
    ↓
Calculate parent totals
    ↓
Send customer notification
    ↓
Send vendor notifications (each vendor)
```

### Processing Refund:
```
Vendor updates their refund status
    ↓
Observer detects status change
    ↓
Notify customer about vendor refund update
    ↓
Check if all vendor refunds complete
    ↓
Update parent refund status
    ↓
If all refunded:
    ↓
    Process points/stock for all items
    ↓
    Mark parent as refunded
```

---

## Migration Steps

1. **Run migration:**
   ```bash
   php artisan migrate
   ```

2. **Existing refunds:**
   - Old refunds remain as-is
   - Can be marked as parent refunds if needed
   - Or left as legacy single-vendor refunds

3. **Test scenarios:**
   - Single vendor order
   - Multi-vendor order
   - Partial refunds
   - Status updates

---

## API Response Structure

### Parent Refund Response:
```json
{
  "id": 100,
  "refund_number": "REF-20260118-0100",
  "is_parent": true,
  "parent_refund_id": null,
  "vendor_id": null,
  "customer_id": 45,
  "order_id": 123,
  "status": "pending",
  "total_refund_amount": 175.00,
  "items": [...],
  "vendor_refunds": [
    {
      "id": 101,
      "vendor_id": 1,
      "status": "pending",
      "total_refund_amount": 50.00
    },
    {
      "id": 102,
      "vendor_id": 2,
      "status": "approved",
      "total_refund_amount": 100.00
    }
  ]
}
```

### Vendor Refund Response:
```json
{
  "id": 101,
  "refund_number": "REF-20260118-0101",
  "is_parent": false,
  "parent_refund_id": 100,
  "vendor_id": 1,
  "customer_id": 45,
  "order_id": 123,
  "status": "pending",
  "total_refund_amount": 50.00,
  "items": [...],
  "parent_refund": {
    "id": 100,
    "refund_number": "REF-20260118-0100",
    "total_refund_amount": 175.00
  }
}
```

---

## Summary

The refund system now supports:
- ✅ One customer refund request (parent)
- ✅ Multiple vendor refund requests (children)
- ✅ Automatic splitting by vendor
- ✅ Independent vendor processing
- ✅ Customer sees single refund
- ✅ Dashboard sees vendor refunds
- ✅ Proper relationships and filtering
- ✅ Comprehensive notifications

This architecture provides flexibility for multi-vendor orders while maintaining simplicity for customers.

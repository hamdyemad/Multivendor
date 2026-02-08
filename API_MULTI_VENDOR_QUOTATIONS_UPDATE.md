# API Multi-Vendor Quotations Update - Complete

## Overview
Updated the Request Quotations API to properly support multi-vendor workflow with individual vendor statuses and multiple orders.

## Changes Made

### 1. RequestQuotationResource Updated
**File**: `Modules/Order/app/Http/Resources/RequestQuotationResource.php`

**Changes**:
- Added `quotation_number` field
- Added `vendors` array containing all assigned vendors with their individual data
- Updated status labels to include new multi-vendor statuses
- Kept legacy fields for backward compatibility

**New Response Structure**:
```json
{
  "id": 1,
  "quotation_number": "RQ-000001",
  "notes": "Customer notes",
  "file": "https://...",
  "status": "offers_received",
  "status_label": "Offers Received",
  "created_at": "2026-02-08T...",
  "address": {...},
  "customer": {...},
  "vendors": [
    {
      "id": 1,
      "vendor": {
        "id": 5,
        "name": "Vendor Name",
        "logo": "https://..."
      },
      "status": "offer_sent",
      "status_label": "Offer Sent",
      "offer_price": 1500.00,
      "offer_notes": "Special discount",
      "offer_sent_at": "2026-02-08T...",
      "order_id": 123,
      "order": {
        "id": 123,
        "order_number": "ORD-000123",
        "total_price": 1500.00,
        "status": "pending"
      },
      "can_respond": true,
      "created_at": "2026-02-08T..."
    },
    {
      "id": 2,
      "vendor": {...},
      "status": "pending",
      "status_label": "Pending",
      "offer_price": null,
      "order_id": null,
      "order": null,
      ...
    }
  ]
}
```

### 2. RequestQuotationVendorResource
**File**: `Modules/Order/app/Http/Resources/RequestQuotationVendorResource.php`

**Already includes**:
- Vendor information (id, name, logo)
- Individual vendor status
- Offer details (price, notes, dates)
- Order information (if order created)
- Can respond flag

### 3. Repository Updates
**File**: `Modules/Order/app/Repositories/Api/RequestQuotationApiRepository.php`

**Changes**:
- Updated `getCustomerQuotations()` to load `vendors.vendor` and `vendors.order` relationships
- Updated `findForCustomer()` to load vendor relationships
- Updated search to include `quotation_number` and search in vendor orders

### 4. API Controller Updates
**File**: `Modules/Order/app/Http/Controllers/Api/RequestQuotationApiController.php`

**Changes**:
- Updated `offers()` method to load vendors with their orders
- Changed from `$quotation->offers` to `$quotation->vendors`

## API Endpoints

### GET /api/v1/request-quotations
**Description**: List all customer quotations with vendor information

**Response**: Paginated list of quotations with vendors array

### GET /api/v1/request-quotations/{id}
**Description**: Get single quotation details with all vendors

**Response**: Single quotation with vendors array

### GET /api/v1/request-quotations/{id}/offers
**Description**: Get all vendor offers for a quotation

**Response**: Array of vendor offers with order information

### POST /api/v1/request-quotations/{quotationId}/vendors/{vendorId}/accept
**Description**: Accept a specific vendor's offer

**Response**: Updated vendor quotation with created order

### POST /api/v1/request-quotations/{quotationId}/vendors/{vendorId}/reject
**Description**: Reject a specific vendor's offer

**Response**: Updated vendor quotation

## Key Features

### Multi-Vendor Support
- Each quotation can have multiple vendors
- Each vendor has their own status (pending, offer_sent, offer_accepted, offer_rejected, order_created)
- Each vendor can have their own order

### Multiple Orders
- A quotation can have multiple orders (one per vendor)
- Orders are accessible through `vendors[].order`
- Each order includes: id, order_number, total_price, status

### Status Hierarchy
**Quotation Level Statuses**:
- `pending` - Initial state
- `sent_to_vendors` - Sent to one or more vendors
- `offers_received` - At least one vendor sent an offer
- `partially_accepted` - Some offers accepted
- `fully_accepted` - All offers accepted
- `rejected` - All offers rejected
- `orders_created` - At least one order created
- `archived` - Archived

**Vendor Level Statuses**:
- `pending` - Waiting for vendor to send offer
- `offer_sent` - Vendor sent an offer
- `offer_accepted` - Customer accepted the offer
- `offer_rejected` - Customer rejected the offer
- `order_created` - Order created from this vendor

### Backward Compatibility
- Legacy fields maintained (`offer_sent_at`, `offer_responded_at`, `order`)
- Old status labels still work
- Existing API clients won't break

## Mobile App Integration

### Display Quotations List
```javascript
// Show quotation with vendor count
quotation.vendors.length // Number of vendors
quotation.status_label // Overall status
```

### Display Vendor Offers
```javascript
quotation.vendors.forEach(vendor => {
  // Show vendor card
  vendor.vendor.name
  vendor.vendor.logo
  vendor.status_label
  vendor.offer_price
  vendor.offer_notes
  
  // Show order if created
  if (vendor.order) {
    vendor.order.order_number
    vendor.order.total_price
  }
  
  // Show accept/reject buttons
  if (vendor.can_respond) {
    // Show buttons
  }
})
```

### Accept/Reject Offer
```javascript
// Accept specific vendor's offer
POST /request-quotations/{quotationId}/vendors/{vendorId}/accept

// Reject specific vendor's offer
POST /request-quotations/{quotationId}/vendors/{vendorId}/reject
```

## Testing Checklist
- [ ] GET /request-quotations returns vendors array
- [ ] Each vendor has correct status
- [ ] Vendor orders are included when created
- [ ] GET /request-quotations/{id}/offers returns all vendors
- [ ] Accept offer creates order and updates vendor status
- [ ] Reject offer updates vendor status
- [ ] Multiple vendors can have different statuses
- [ ] Multiple orders can exist for one quotation
- [ ] Search works with quotation_number
- [ ] Pagination works correctly
- [ ] Legacy fields still present for compatibility

## Notes
- The API now properly supports the multi-vendor workflow
- Customers can see all vendors and their individual statuses
- Customers can accept/reject offers from individual vendors
- Multiple orders can be created from a single quotation (one per vendor)
- The quotation status reflects the overall state based on all vendors

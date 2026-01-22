# Refund Request: One Per Vendor Per Order Validation

## Summary
Added validation to ensure that customers can only create one refund request per vendor per order. If a customer tries to create a second refund request for the same vendor in the same order, they will receive an error message.

## Changes Made

### 1. Repository Validation
**File**: `Modules/Refund/app/Repositories/RefundRequestRepository.php`

Added validation in `createRefundWithVendorSplit()` method:
- Before creating refund requests, check if a refund already exists for each vendor in the order
- Query excludes cancelled refunds (customers can create a new refund if they cancelled the previous one)
- Throws exception with translated error message if duplicate found

```php
// Check if refund request already exists for any vendor in this order
foreach ($itemsByVendor as $vendorId => $vendorItems) {
    $existingRefund = $this->model
        ->where('order_id', $order->id)
        ->where('vendor_id', $vendorId)
        ->whereNotIn('status', ['cancelled']) // Exclude cancelled refunds
        ->first();
    
    if ($existingRefund) {
        throw new \Exception(trans('refund::refund.validation.vendor_already_has_refund'));
    }
}
```

### 2. Translation Keys Added

**English** (`Modules/Refund/lang/en/refund.php`):
```php
'vendor_already_has_refund' => 'A refund request already exists for this vendor in this order. Only one refund request per vendor per order is allowed.',
```

**Arabic** (`Modules/Refund/lang/ar/refund.php`):
```php
'vendor_already_has_refund' => 'يوجد بالفعل طلب استرجاع لهذا المورد في هذا الطلب. يُسمح بطلب استرجاع واحد فقط لكل مورد في كل طلب.',
```

## Business Logic

### Scenario 1: Order with 2 products from same vendor
- Customer creates refund for Product A ✅
- Customer tries to create refund for Product B ❌ (Error: vendor already has refund)
- **Solution**: Customer must include both products in the same refund request

### Scenario 2: Order with products from different vendors
- Customer creates refund for Vendor A's product ✅
- Customer creates refund for Vendor B's product ✅ (Different vendor, allowed)

### Scenario 3: Cancelled refund
- Customer creates refund for Vendor A ✅
- Customer cancels the refund ✅
- Customer creates new refund for Vendor A ✅ (Previous was cancelled, allowed)

### Scenario 4: Completed/Pending refund
- Customer creates refund for Vendor A (status: pending) ✅
- Customer tries to create another refund for Vendor A ❌ (Error)
- Customer creates refund for Vendor A (status: refunded) ✅
- Customer tries to create another refund for Vendor A ❌ (Error)

## API Endpoint
**POST** `/v1/refunds`

**Error Response** (when duplicate detected):
```json
{
    "message": "A refund request already exists for this vendor in this order. Only one refund request per vendor per order is allowed.",
    "success": false,
    "data": [],
    "errors": [],
    "status": 500
}
```

## Testing Recommendations

1. **Test Case 1**: Create refund with 1 product, try to create another with different product from same vendor
   - Expected: Error message

2. **Test Case 2**: Create refund with 1 product, cancel it, then create new refund
   - Expected: Success

3. **Test Case 3**: Create refund for vendor A, then create refund for vendor B
   - Expected: Both succeed

4. **Test Case 4**: Create refund with multiple products from same vendor in one request
   - Expected: Success (all products in one refund)

## Notes
- Validation happens at the repository level before any database transactions
- The check is performed for each vendor in the grouped items
- Cancelled refunds are excluded from the check (customers can retry after cancellation)
- Error message is properly translated for both English and Arabic

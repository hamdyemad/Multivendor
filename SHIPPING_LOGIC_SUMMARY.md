# Return Shipping Logic - Final Implementation

## Overview
The refund system now correctly handles shipping costs based on who pays for return shipping.

## Logic

### Case 1: Vendor Pays Return Shipping (`customer_pays_return_shipping = false`)
- **Original Shipping**: Customer does NOT get original shipping cost back
- **Return Shipping**: Vendor pays (cost = 0 for customer)
- **Calculation**:
  ```
  total_shipping_amount = 0
  return_shipping_cost = 0
  Refund = products + tax
  ```
- **Example**:
  - Product: 27.87 EGP
  - Original shipping paid: 50.00 EGP
  - **Refund: 27.87 EGP** (customer loses the 50 EGP shipping)

### Case 2: Customer Pays Return Shipping (`customer_pays_return_shipping = true`)
- **Original Shipping**: Customer DOES get original shipping cost back
- **Return Shipping**: Customer pays (deducted from refund)
- **Calculation**:
  ```
  total_shipping_amount = original shipping cost
  return_shipping_cost = actual return shipping cost
  Refund = products + tax + original_shipping - return_shipping_cost
  ```
- **Example**:
  - Product: 27.87 EGP
  - Original shipping paid: 50.00 EGP
  - Return shipping cost: 30.00 EGP
  - **Refund: 47.87 EGP** (27.87 + 50.00 - 30.00)

## Implementation Details

### 1. Repository (`RefundRequestRepository.php`)
When creating refund items:
```php
// If vendor pays return shipping, don't refund original shipping cost
$shippingPerUnit = $customerPaysReturnShipping && $orderProduct->quantity > 0 
    ? ($orderProduct->shipping_cost ?? 0) / $orderProduct->quantity 
    : 0;
```

### 2. Model (`RefundRequest.php`)
The `calculateTotals()` method:
```php
$subtotal = $this->total_products_amount 
    + $this->total_shipping_amount  // 0 if vendor pays, original amount if customer pays
    + $this->total_tax_amount
    - $this->total_discount_amount
    + $this->vendor_fees_amount
    - $this->vendor_discounts_amount
    - $this->promo_code_amount
    - $this->points_used;

$this->total_refund_amount = $subtotal - ($this->return_shipping_cost ?? 0);
```

### 3. View (`show.blade.php`)
Shows clear indication of who pays and what it means:
- **Vendor pays**: "Customer does not get original shipping cost back"
- **Customer pays**: "Customer gets original shipping cost back"

## Database Fields

### `refund_requests` table:
- `total_shipping_amount`: Original order shipping (0 if vendor pays, actual amount if customer pays)
- `return_shipping_cost`: Cost to return items (0 if vendor pays, actual cost if customer pays)
- `customer_pays_return_shipping`: Boolean flag (snapshot from vendor settings at creation time)

### `refund_request_items` table:
- `shipping_amount`: Per-item shipping (0 if vendor pays, proportional amount if customer pays)

## Testing

Run the test script to verify:
```bash
php test_final_shipping_logic.php
```

## Migration Path

For existing refunds where vendor pays but shipping was included:
```bash
php fix_refund_27_shipping_logic.php
```

This script:
1. Checks if vendor pays return shipping
2. Sets all item `shipping_amount` to 0
3. Recalculates totals
4. Results in correct refund amount (products + tax only)

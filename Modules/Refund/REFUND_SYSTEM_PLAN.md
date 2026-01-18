# Refund System Implementation Plan

## Overview
Complete refund management system for handling product returns from delivered orders.

## Database Structure

### Summary of Tables:
1. **refund_settings** - Global refund system settings (1 row)
2. **refund_requests** - Refund requests (one per vendor per order)
3. **refund_request_items** - Items being refunded in each request

### Modified Existing Tables:
1. **vendor_products** - Added: `is_able_to_refund`, `refund_days`
2. **order_products** - Added: `is_refunded`, `refunded_amount`, `refunded_at`
3. **orders** - Uses existing: `refunded_amount`

### Product-Level Refund Control
**Added to `vendor_products` table:**
- `is_able_to_refund` (boolean, default: true) - Enable/disable refunds for this specific product
- `refund_days` (integer, default: 7) - Days after delivery customer can request refund for this product

These fields are managed in the vendor product form, giving you control over refund eligibility per product.

### 1. refund_settings table
- id
- refund_enabled (boolean, default: true) - Enable/disable refund system globally
- customer_pays_return_shipping (boolean, default: false) - If true, customer pays for return shipping (uses existing shipping system rates)
- refund_original_shipping (boolean, default: false) - If true, refund the original shipping cost paid by customer
- refund_processing_days (integer, default: 7) - **Default** days to process refund (used as fallback when product has `is_able_to_refund=true` but `refund_days` is null/empty)
- created_at
- updated_at

**Note:** Return shipping costs are calculated using the existing shipping system. The `customer_pays_return_shipping` flag determines if this cost is deducted from the refund amount.

**Refund Days Priority Logic:**
1. If product has `is_able_to_refund = false` → Refund NOT allowed
2. If product has `is_able_to_refund = true` AND `refund_days` is set → Use product's `refund_days`
3. If product has `is_able_to_refund = true` AND `refund_days` is null/empty → Use global `refund_processing_days` as default

**Note:** Refund eligibility per product is controlled at the product level via `vendor_products` table:
- `is_able_to_refund` (boolean, default: true) - Product can be refunded
- `refund_days` (integer, default: 7) - Days after delivery to request refund for this product

## Refund Eligibility Logic

### Checking if Product is Refundable:
```php
function isProductRefundable($vendorProduct, $orderDeliveryDate) {
    // Check global setting
    if (!refund_settings.refund_enabled) {
        return false;
    }
    
    // Check product-level setting
    if (!$vendorProduct->is_able_to_refund) {
        return false;
    }
    
    // Determine refund days (product-specific or global default)
    $refundDays = $vendorProduct->refund_days ?? refund_settings.refund_processing_days;
    
    // Calculate deadline
    $refundDeadline = $orderDeliveryDate->addDays($refundDays);
    
    // Check if still within refund period
    if (now() > $refundDeadline) {
        return false;
    }
    
    return true;
}
```

### Example Scenarios:
1. **Product A:** `is_able_to_refund = true`, `refund_days = 14`
   - Customer has **14 days** after delivery to request refund

2. **Product B:** `is_able_to_refund = true`, `refund_days = null`
   - Customer has **7 days** (global default) after delivery to request refund

3. **Product C:** `is_able_to_refund = false`, `refund_days = 30`
   - Refund **NOT allowed** regardless of refund_days value

4. **Global Setting:** `refund_enabled = false`
   - **No refunds allowed** for any product regardless of product settings

### 2. refund_requests table
- id
- order_id (foreign key to orders)
- customer_id (foreign key to customers)
- vendor_id (foreign key to vendors) - **The vendor who owns the products being refunded**
- refund_number (unique, auto-generated: REF-YYYYMMDD-XXXX)
- status (enum: pending, approved, in_progress, picked_up, refunded, rejected, cancelled) - **Vendor's refund status**
- total_products_amount (decimal) - Sum of refunded products price (unit_price * quantity)
- total_shipping_amount (decimal) - Sum of shipping costs for refunded products
- total_tax_amount (decimal) - Sum of tax for refunded products
- total_discount_amount (decimal) - Sum of product-level discounts
- vendor_fees_amount (decimal, default: 0) - Proportional vendor-specific fees (from order_extra_fees_discounts)
- vendor_discounts_amount (decimal, default: 0) - Proportional vendor-specific discounts (from order_extra_fees_discounts)
- promo_code_amount (decimal, default: 0) - Proportional promo code discount
- return_shipping_cost (decimal, default: 0) - Cost customer pays for return shipping
- points_used (decimal, default: 0) - Points used to pay for this vendor's products
- points_to_deduct (integer, default: 0) - Points earned from this vendor's products (to be deducted)
- total_refund_amount (decimal) - Final amount to refund to customer
- reason (text) - Customer's reason for refund
- customer_notes (text, nullable) - Additional notes from customer
- vendor_notes (text, nullable) - Vendor's notes on this refund request
- admin_notes (text, nullable) - Admin's notes
- approved_at (timestamp, nullable) - When vendor approved
- refunded_at (timestamp, nullable) - When refund was completed
- created_at
- updated_at
- deleted_at (soft deletes)

**Note:** Each refund request is for products from a single vendor. If a customer wants to refund products from multiple vendors in the same order, separate refund requests will be created for each vendor.

### 3. refund_request_items table
- id
- refund_request_id (foreign key to refund_requests)
- order_product_id (foreign key to order_products)
- product_variant_id (foreign key to vendor_product_variants, nullable)
- quantity (integer) - Quantity being refunded
- unit_price (decimal) - Original unit price per item
- total_price (decimal) - quantity * unit_price
- tax_amount (decimal) - Tax for this item
- discount_amount (decimal) - Product-level discount for this item
- shipping_amount (decimal) - Original shipping cost for this item
- refund_amount (decimal) - Final refund for this item (total_price + tax + shipping - discount)
- created_at
- updated_at

**Important Notes:** 
- The `shipping_amount` is copied from `order_products.shipping_cost` when creating the refund request
- The `refund_amount` is calculated based on refund settings (whether to include shipping or not)
- All amounts are stored for audit trail and reporting purposes

**Prerequisites Before Implementation:**
1. ✅ `orders` table must have `refunded_amount` column (already exists in Order model)
2. ⚠️ `order_products` table must have `shipping_cost` column (check if exists, add if not)
3. ⚠️ `order_products` table needs `commission` column (for commission calculation)
4. ✅ `order_extra_fees_discounts` table exists (for vendor fees/discounts)
5. ✅ `vendor_order_stages` table exists (for checking delivered status)
6. ✅ `stock_bookings` table exists (for reversing bookings)
7. ✅ `user_points` and `user_points_transactions` tables exist (for points handling)

**Prerequisites:** 
- The `order_products` table must have a `shipping_cost` column that stores the shipping cost per product
- When creating an order, the shipping cost for each product should be calculated and stored in `order_products.shipping_cost`
- The total order shipping cost (`orders.shipping_cost`) is the sum of all `order_products.shipping_cost`

## Refund Calculation Logic

### Step 1: Calculate Product Refund
For each refunded item:
```
// Base product refund
item_refund = (unit_price * quantity) + tax_amount + shipping_amount - discount_amount
```

### Step 2: Calculate Vendor Fees & Discounts
```
// Get vendor-specific fees and discounts from order_extra_fees_discounts table
vendor_fees = order_extra_fees_discounts
    .where('order_id', order.id)
    .where('vendor_id', vendor.id)
    .where('type', 'fee')
    .sum('cost');

vendor_discounts = order_extra_fees_discounts
    .where('order_id', order.id)
    .where('vendor_id', vendor.id)
    .where('type', 'discount')
    .sum('cost');

// Calculate proportional fees/discounts based on refunded amount
refund_percentage = sum(refunded_items.total_price) / sum(all_vendor_items.total_price);
proportional_fees = vendor_fees * refund_percentage;
proportional_discounts = vendor_discounts * refund_percentage;
```

### Step 3: Calculate Promo Code Discount
```
// If order has promo code, calculate proportional discount for this vendor
if (order.customer_promo_code_amount > 0) {
    // Calculate this vendor's share of total order
    vendor_total = sum(all_vendor_items.total_price);
    order_total = order.total_product_price;
    vendor_percentage = vendor_total / order_total;
    
    // Calculate proportional promo code discount for this vendor
    vendor_promo_discount = order.customer_promo_code_amount * vendor_percentage;
    
    // Calculate proportional promo discount for refunded items
    refund_percentage = sum(refunded_items.total_price) / vendor_total;
    promo_code_amount = vendor_promo_discount * refund_percentage;
} else {
    promo_code_amount = 0;
}
```

### Step 4: Calculate Shipping Refund
```
// Since shipping is calculated per product in your system
if (refund_settings.refund_original_shipping) {
    shipping_refund = sum(refunded_items.shipping_amount);
} else {
    shipping_refund = 0;
}
```

### Step 5: Calculate Return Shipping Cost
```
if (refund_settings.customer_pays_return_shipping) {
    // Use existing shipping system to calculate return shipping cost
    // Based on customer address, product weight/dimensions, and shipping method
    return_shipping = calculate_shipping_cost(customer_address, refund_items);
} else {
    return_shipping = 0;
}
```

### Step 6: Calculate Points
```
// Points used to pay for this vendor's products
if (order.points_used > 0) {
    // Calculate this vendor's share of points used
    vendor_total = sum(all_vendor_items.total_price);
    order_total = order.total_product_price;
    vendor_points_used = order.points_used * (vendor_total / order_total);
    
    // Calculate proportional points for refunded items
    refund_percentage = sum(refunded_items.total_price) / vendor_total;
    points_used_for_refund = vendor_points_used * refund_percentage;
    
    // Convert points to money value
    points_cost_for_refund = order.points_cost * (points_used_for_refund / order.points_used);
} else {
    points_used_for_refund = 0;
    points_cost_for_refund = 0;
}

// Points earned from this vendor's products (to be deducted from customer)
// Assuming points are earned based on product price
points_earned_per_egp = 1; // Configure this based on your system
vendor_items_total = sum(all_vendor_items.total_price);
points_earned_from_vendor = vendor_items_total * points_earned_per_egp;

refund_percentage = sum(refunded_items.total_price) / vendor_items_total;
points_to_deduct = points_earned_from_vendor * refund_percentage;
```

### Step 7: Calculate Final Refund Amount
```
// Sum all refundable amounts
total_refund = sum(refunded_items.refund_amount); // Products + tax + shipping - product discounts

// Add vendor-specific discounts (they were deducted from order, so we refund them)
total_refund += proportional_discounts;

// Add promo code discount (it was deducted from order, so we refund it)
total_refund += promo_code_amount;

// Subtract vendor-specific fees (they were added to order, so we deduct them from refund)
total_refund -= proportional_fees;

// Subtract return shipping if customer pays
total_refund -= return_shipping;

// Subtract points value that was used to pay
total_refund -= points_cost_for_refund;

// Final refund amount
refund_request.total_refund_amount = total_refund;
refund_request.points_used = points_used_for_refund;
refund_request.points_to_deduct = points_to_deduct;
```

### Step 8: Update Customer Points
```
// Deduct points that were earned from these products
customer.points -= points_to_deduct;

// Return points that were used to pay (if any)
customer.points += points_used_for_refund;
```

### Step 9: Update Vendor Balance
```
// Deduct refund amount from vendor balance
vendor.balance -= total_refund;

// Adjust pending withdraws if applicable
if (vendor.pending_withdraws > 0) {
    vendor.pending_withdraws -= min(total_refund, vendor.pending_withdraws);
}
```

## Comprehensive Refund Examples

### Example 1: Simple Refund (No Points, No Promo, No Extra Fees)

**Order Details:**
- Vendor A Products: 1000 EGP
- Tax: 100 EGP
- Shipping: 50 EGP
- Product Discount: 50 EGP
- **Total Paid: 1100 EGP**

**Customer refunds all products:**

**Settings:** `refund_original_shipping = true`, `customer_pays_return_shipping = false`

```
Products: 1000 EGP
Tax: 100 EGP
Shipping: 50 EGP
Product Discount: -50 EGP
Return Shipping: 0 EGP
---
Total Refund: 1100 EGP ✓
```

---

### Example 2: Refund with Promo Code

**Order Details:**
- Vendor A Products: 1000 EGP
- Tax: 100 EGP
- Shipping: 50 EGP
- Promo Code Discount: 100 EGP (10% on entire order)
- **Total Paid: 1050 EGP**

**Customer refunds all products:**

```
Products: 1000 EGP
Tax: 100 EGP
Shipping: 50 EGP
Promo Code (refunded): +100 EGP
---
Total Refund: 1250 EGP
Wait! Customer only paid 1050 EGP

Correct Calculation:
Products + Tax + Shipping: 1150 EGP
Promo was already deducted: -100 EGP
Customer paid: 1050 EGP
So refund: 1050 EGP ✓
```

**Correct Formula:**
```
Base Amount = Products + Tax + Shipping = 1150 EGP
Promo Discount = 100 EGP (already deducted from payment)
Customer Paid = 1050 EGP
Refund = Customer Paid = 1050 EGP
```

---

### Example 3: Refund with Points Payment

**Order Details:**
- Vendor A Products: 1000 EGP
- Tax: 100 EGP
- Shipping: 50 EGP
- Points Used: 200 points = 200 EGP
- Cash Paid: 950 EGP
- **Total: 1150 EGP (200 points + 950 cash)**

**Customer refunds all products:**

```
Products + Tax + Shipping: 1150 EGP
Points Used Value: -200 EGP
---
Cash Refund: 950 EGP
Points Returned: 200 points

Customer also earned 100 points from this order
Points to Deduct: -100 points
---
Final Points Balance Change: +200 - 100 = +100 points
```

---

### Example 4: Refund with Vendor Fees

**Order Details:**
- Vendor A Products: 1000 EGP
- Tax: 100 EGP
- Shipping: 50 EGP
- Vendor Extra Fee (packaging): 30 EGP
- **Total Paid: 1180 EGP**

**Customer refunds all products:**

```
Products: 1000 EGP
Tax: 100 EGP
Shipping: 50 EGP
Vendor Fee (reversed): -30 EGP
---
Total Refund: 1120 EGP

Wait! Customer paid 1180 EGP
The fee was added to the order, so:
Refund = 1150 - 30 = 1120 EGP ✗

Correct: Customer paid 1180, so refund should be 1180
But the fee was for packaging/service, so it's NOT refundable
Final Refund: 1150 EGP (without fee)
```

**Note:** Vendor fees are typically NOT refundable as they represent services already provided.

---

### Example 5: Partial Refund from Multi-Vendor Order

**Order Details:**
- Vendor A Products: 1000 EGP (Tax: 100, Shipping: 50)
- Vendor B Products: 500 EGP (Tax: 50, Shipping: 30)
- Promo Code: 150 EGP (10% on entire order)
- Points Used: 300 points = 300 EGP
- Cash Paid: 1280 EGP
- **Total: 1580 EGP (300 points + 1280 cash)**

**Customer refunds Vendor A products only:**

```
Step 1: Calculate Vendor A's share
Vendor A Total: 1150 EGP (1000 + 100 + 50)
Order Total: 1730 EGP (before promo)
Vendor A %: 66.47%

Step 2: Calculate Vendor A's promo discount
Promo for Vendor A: 150 * 66.47% = 99.7 EGP

Step 3: Calculate Vendor A's points used
Points for Vendor A: 300 * 66.47% = 199.4 points = 199.4 EGP

Step 4: Calculate refund
Products + Tax + Shipping: 1150 EGP
Promo (already deducted): 0 (we refund what customer paid)
Points Used: -199.4 EGP
---
Cash Refund: 950.6 EGP
Points Returned: 199.4 points

Step 5: Points earned from Vendor A
Points Earned: 1000 * 1 = 1000 points
Points to Deduct: 1000 points
---
Final Points: +199.4 - 1000 = -800.6 points
```

---

### Example 6: Refund with Customer Pays Return Shipping

**Order Details:**
- Vendor A Products: 1000 EGP
- Tax: 100 EGP
- Original Shipping: 50 EGP
- **Total Paid: 1150 EGP**

**Settings:** `refund_original_shipping = true`, `customer_pays_return_shipping = true`

**Customer refunds all products:**

```
Products + Tax + Shipping: 1150 EGP
Return Shipping Cost: -50 EGP (calculated by shipping system)
---
Total Refund: 1100 EGP

Customer paid 1150 EGP
Customer receives 1100 EGP
Net cost to customer: 50 EGP (return shipping)
```

---

### Summary Formula:

```
total_refund = 
    sum(products + tax + [shipping if enabled] - product_discounts)
    + [proportional_vendor_discounts]
    + [proportional_promo_code_discount]
    - [proportional_vendor_fees]
    - [return_shipping if customer pays]
    - [points_value_used]

points_returned = points_used_for_vendor_products
points_deducted = points_earned_from_vendor_products
```

## Workflow

### Customer Side (API):
1. Customer views delivered orders
2. Customer selects products to refund (can select products from one or multiple vendors)
3. Customer submits refund request with reason
   - **Important:** If products from multiple vendors are selected, separate refund requests are created for each vendor
   - Each vendor gets their own `refund_request` with their products
4. Customer receives notifications on status changes for each vendor's refund request

### Vendor Side (Dashboard):
1. Vendor receives notification of refund request for their products
2. Vendor views refund request details (only their products)
3. Vendor can:
   - **Approve/Reject request** (required - no auto-approval)
   - Add vendor notes
   - Change status to "in_progress" (driver going to customer)
   - Change status to "picked_up" (driver collected items)
   - Change status to "refunded" (process complete)
4. Vendor sees refund impact on their balance/withdraws

### Admin Side (Dashboard):
1. Admin can view all refund requests from all vendors
2. Admin can override vendor decisions
3. Admin can track refund statistics (per vendor, per product, etc.)
4. **Admin manages refund settings:**
   - Enable/disable refund system globally
   - Set return shipping policy (customer pays or free)
   - Enable/disable original shipping refund
   - Set default refund days (fallback for products without custom days)

## Refund Settings Management

### Settings Page Features:
1. **General Settings:**
   - Enable/Disable Refunds (toggle) - Master switch for entire refund system
   - Default Refund Days (number input) - Fallback value when product has refunds enabled but no custom days specified

2. **Shipping Settings:**
   - Customer Pays Return Shipping (toggle)
     - If enabled: Return shipping cost calculated using existing shipping system and deducted from refund
     - If disabled: Vendor/platform covers return shipping cost
   - Refund Original Shipping Cost (toggle)
     - If enabled: Original shipping cost paid by customer is refunded (only if all items are refunded)
     - If disabled: Original shipping cost is not refunded

**Note:** Product-level refund control is managed in the product form:
- `is_able_to_refund` checkbox - Enable/disable refunds for specific product
- `refund_days` input field - Custom refund period for this product (if empty, uses global default)

3. **Financial Settings:**
   - Deduct Points on Refund (toggle)
   - Proportional Discount Reversal (toggle)
   - Tax Refund Policy (toggle)

## Notifications

### To Customer:
- Refund request received
- Vendor approved/rejected
- Status changed to in_progress
- Status changed to picked_up
- Refund completed

### To Vendor:
- New refund request for their products
- Customer cancelled refund

## API Endpoints

### Customer API:
- GET /api/refund-settings (check if refunds are enabled)
- GET /api/orders/{order}/refundable-products (with refund calculation preview)
- POST /api/refund-requests (create with automatic calculation)
- GET /api/refund-requests
- GET /api/refund-requests/{id}
- DELETE /api/refund-requests/{id} (cancel - only if status is pending)

### Vendor Dashboard:
- GET /vendor/refund-requests (list all refund requests for this vendor)
- GET /vendor/refund-requests/{id} (view refund request details with items)
- POST /vendor/refund-requests/{id}/approve (approve entire refund request)
- POST /vendor/refund-requests/{id}/reject (reject entire refund request with reason)
- POST /vendor/refund-requests/{id}/change-status (change status: in_progress, picked_up, refunded)
- PUT /vendor/refund-requests/{id}/notes (add/update vendor notes)

### Admin Dashboard:
- GET /admin/refund-settings (get current refund settings)
- PUT /admin/refund-settings (update refund settings)
- GET /admin/refund-requests (list all refund requests from all vendors)
- GET /admin/refund-requests/{id} (view refund request details)
- POST /admin/refund-requests/{id}/approve (override vendor - approve refund)
- POST /admin/refund-requests/{id}/reject (override vendor - reject refund)
- POST /admin/refund-requests/{id}/change-status (override vendor - change status)
- GET /admin/refund-requests/statistics (refund statistics and reports)

## Integration Points

1. **Order Module**: 
   - Check if order is delivered and get order products
   - Access order fees/discounts from `order_extra_fees_discounts` table
   - Access promo code information from order
   - Access points information (points_used, points_cost)
   - Mark order products as refunded (`is_refunded`, `refunded_amount`)
   - Update order's total refunded amount

2. **Vendor Module**: 
   - Get vendor information for refund request
   - Calculate vendor-specific fees and discounts
   - **Recalculate vendor balance** (orders_price - bnaia_commission)
   - **Recalculate bnaia_commission** (excluding refunded products)
   - Access commission percentages (product or department level)

3. **Accounting Module**: 
   - Update vendor balance when refund is completed
   - Record refund transaction
   - Track commission reversals

4. **Withdraw Module**: 
   - **No changes needed** - vendor balance is calculated dynamically
   - When products are marked as refunded, balance automatically decreases
   - Vendor's `total_remaining` reflects the new balance minus any sent withdrawals

5. **Customer/Points Module**:
   - Deduct points earned from refunded products
   - Return points used to pay for refunded products
   - Update customer points balance

6. **Notification System**: 
   - Send FCM notifications to customer and vendor
   - Notify about refund status changes
   - **Notify admin** about withdrawals requiring review

7. **Observer Pattern**: 
   - Auto-notify on status changes (RefundRequestObserver)
   - **Trigger accounting updates** on refund completion (status = 'refunded')
   - **Update vendor financial data** (commission, balance)
   - **Adjust withdrawals** when refund is completed

8. **Shipping Module**: 
   - Calculate return shipping cost using existing shipping system
   - Access original shipping costs per product

9. **Promo Code Module**:
   - Calculate proportional promo code discount for refunded items
   - Handle promo code reversal logic

10. **Commission System**:
    - Calculate commission reversal for refunded products
    - Update bnaia_commission calculation to exclude refunded products
    - Ensure vendor balance reflects commission changes

## Important Business Rules

### 1. Vendor Fees Policy
- **Packaging Fees**: NOT refundable (service already provided)
- **Handling Fees**: NOT refundable (service already provided)
- **Other Service Fees**: Evaluate case-by-case

### 2. Vendor Discounts Policy
- **Vendor-specific discounts**: REFUNDABLE (customer didn't receive full value)
- **Promo code discounts**: REFUNDABLE (proportionally)

### 3. Points Policy
- **Points Used**: Returned to customer (they paid with points)
- **Points Earned**: Deducted from customer (they're returning the products)
- **Net Effect**: Customer may lose or gain points depending on the scenario

### 4. Partial Refund Rules
- When refunding partial items from multi-vendor order:
  - Calculate each vendor's share of promo code discount
  - Calculate each vendor's share of points used
  - Each vendor's refund is independent

### 5. Refund Amount Validation
```php
// Always validate that refund doesn't exceed what customer paid
$customerPaidForVendor = $vendorProductsTotal + $vendorTax + $vendorShipping 
                        - $vendorPromoDiscount - $vendorPointsValue;

if ($calculatedRefund > $customerPaidForVendor) {
    throw new Exception('Refund amount cannot exceed amount paid');
}
```

### 6. Commission & Balance Recalculation
**CRITICAL:** The Vendor model calculates `bnaia_commission` and `orders_price` dynamically from delivered orders. When a refund is completed:

1. **Mark products as refunded** in `order_products` table:
   - Set `is_refunded = true`
   - Set `refunded_amount = [calculated refund]`

2. **Update Vendor model queries** to exclude refunded products:
```php
// In Vendor.php - getBnaiaCommissionAttribute()
$orderProducts = DB::table('order_products as op')
    // ... existing joins ...
    ->where('op.vendor_id', $this->id)
    ->where('vos.stage_id', $deliverStageId)
    ->where('op.is_refunded', false) // ADD THIS LINE
    ->select(/* ... */)
    ->get();

// In Vendor.php - getOrdersPriceAttribute()
$result = DB::table('order_products as op')
    // ... existing joins ...
    ->where('op.vendor_id', $this->id)
    ->where('vos.stage_id', $deliverStageId)
    ->where('op.is_refunded', false) // ADD THIS LINE
    ->sum(/* ... */);
```

3. **Vendor balance will automatically reflect refunds** because:
   - `orders_price` excludes refunded products
   - `bnaia_commission` excludes refunded products
   - `total_balance = orders_price - bnaia_commission`
   - `total_remaining = total_balance - total_sent`

### 7. Withdrawal & Balance Handling
**How Vendor Balance Works:**

When order is delivered:
```
- VendorOrderStage changes to 'deliver'
- Stock bookings change to 'fulfilled'
- Customer earns points
- Vendor balance is calculated dynamically:
  orders_price = sum(order_products.price + shipping_cost) 
                 WHERE vendor_order_stages.stage = 'deliver'
                 AND is_refunded = false
  
  bnaia_commission = sum(commission_calculation) 
                     WHERE vendor_order_stages.stage = 'deliver'
                     AND is_refunded = false
  
  total_balance = orders_price - bnaia_commission
  total_remaining = total_balance - total_sent
```

When refund is completed (status = 'refunded'):
```
- Mark order_products.is_refunded = true
- Vendor balance is AUTOMATICALLY recalculated:
  orders_price = REDUCED (excludes refunded products)
  bnaia_commission = REDUCED (excludes refunded products)
  total_balance = REDUCED automatically
  total_remaining = REDUCED automatically
  
- No withdrawal records are created
- No manual balance adjustments needed
```

**Example:**
```
Before Refund:
- Delivered products: 10,000 EGP
- Commission (10%): 1,000 EGP
- total_balance: 9,000 EGP
- total_sent: 2,000 EGP (previous withdrawals)
- total_remaining: 7,000 EGP

After Refund (1,500 EGP product + 150 commission):
- Delivered products: 8,500 EGP (excludes refunded)
- Commission (10%): 850 EGP (excludes refunded)
- total_balance: 7,650 EGP
- total_sent: 2,000 EGP (unchanged)
- total_remaining: 5,650 EGP
```

**Benefits:**
1. ✅ No manual balance adjustments
2. ✅ No withdrawal records for refunds
3. ✅ Balance always reflects current reality
4. ✅ Simple and accurate
5. ✅ Audit trail through order_products.is_refunded

## Files to Create

### Models:
- RefundRequest.php
- RefundRequestItem.php

### Migrations:

**1. create_refund_settings_table.php**
```php
Schema::create('refund_settings', function (Blueprint $table) {
    $table->id();
    $table->boolean('refund_enabled')->default(true);
    $table->boolean('customer_pays_return_shipping')->default(false);
    $table->boolean('refund_original_shipping')->default(false);
    $table->integer('refund_processing_days')->default(7);
    $table->timestamps();
});
```

**2. create_refund_requests_table.php**
```php
Schema::create('refund_requests', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
    $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
    $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
    $table->string('refund_number')->unique();
    $table->enum('status', ['pending', 'approved', 'in_progress', 'picked_up', 'refunded', 'rejected', 'cancelled'])->default('pending');
    $table->decimal('total_products_amount', 10, 2)->default(0);
    $table->decimal('total_shipping_amount', 10, 2)->default(0);
    $table->decimal('total_tax_amount', 10, 2)->default(0);
    $table->decimal('total_discount_amount', 10, 2)->default(0);
    $table->decimal('vendor_fees_amount', 10, 2)->default(0);
    $table->decimal('vendor_discounts_amount', 10, 2)->default(0);
    $table->decimal('promo_code_amount', 10, 2)->default(0);
    $table->decimal('return_shipping_cost', 10, 2)->default(0);
    $table->decimal('points_used', 10, 2)->default(0);
    $table->integer('points_to_deduct')->default(0);
    $table->decimal('total_refund_amount', 10, 2)->default(0);
    $table->text('reason');
    $table->text('customer_notes')->nullable();
    $table->text('vendor_notes')->nullable();
    $table->text('admin_notes')->nullable();
    $table->timestamp('approved_at')->nullable();
    $table->timestamp('refunded_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['order_id', 'vendor_id']);
    $table->index('status');
    $table->index('refund_number');
});
```

**3. create_refund_request_items_table.php**
```php
Schema::create('refund_request_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('refund_request_id')->constrained('refund_requests')->onDelete('cascade');
    $table->foreignId('order_product_id')->constrained('order_products')->onDelete('cascade');
    $table->foreignId('product_variant_id')->nullable()->constrained('vendor_product_variants')->onDelete('set null');
    $table->integer('quantity');
    $table->decimal('unit_price', 10, 2);
    $table->decimal('total_price', 10, 2);
    $table->decimal('tax_amount', 10, 2)->default(0);
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('shipping_amount', 10, 2)->default(0);
    $table->decimal('refund_amount', 10, 2);
    $table->timestamps();
    
    $table->index('refund_request_id');
    $table->index('order_product_id');
});
```

**4. add_refund_fields_to_vendor_products_table.php**
```php
Schema::table('vendor_products', function (Blueprint $table) {
    $table->boolean('is_able_to_refund')->default(true)->after('is_active');
    $table->integer('refund_days')->default(7)->after('is_able_to_refund');
});
```

**5. add_refund_fields_to_order_products_table.php**
```php
Schema::table('order_products', function (Blueprint $table) {
    $table->boolean('is_refunded')->default(false)->after('quantity');
    $table->decimal('refunded_amount', 10, 2)->nullable()->after('is_refunded');
    $table->timestamp('refunded_at')->nullable()->after('refunded_amount');
    
    $table->index('is_refunded');
});
```

**6. add_shipping_cost_to_order_products_table.php** (if not exists)
```php
// Check if column exists first
if (!Schema::hasColumn('order_products', 'shipping_cost')) {
    Schema::table('order_products', function (Blueprint $table) {
        $table->decimal('shipping_cost', 10, 2)->default(0)->after('price');
    });
}
```

**7. add_refunded_amount_to_orders_table.php** (if not exists)
```php
// Check if column exists first (already exists in Order model fillable)
if (!Schema::hasColumn('orders', 'refunded_amount')) {
    Schema::table('orders', function (Blueprint $table) {
        $table->decimal('refunded_amount', 10, 2)->default(0)->after('total_price');
    });
}
```

### Controllers:
- API/RefundRequestApiController.php (Customer API)
- Vendor/VendorRefundController.php (Vendor Dashboard)
- Admin/RefundRequestController.php (Admin Dashboard)
- Admin/RefundSettingsController.php (Admin Settings)

### Repositories:
- RefundRequestRepository.php
- RefundRequestRepositoryInterface.php
- RefundSettingsRepository.php
- RefundSettingsRepositoryInterface.php

### Services:
- RefundRequestService.php (handles refund logic, calculations, and status changes)
- RefundCalculationService.php (handles all refund amount calculations)

### Observers:
- RefundRequestObserver.php (handles notifications on status changes)

**RefundRequestObserver Events:**

1. **`updating` Event** - When status changes to 'refunded':
```php
public function updating(RefundRequest $refundRequest)
{
    // Check if status is changing to 'refunded'
    if ($refundRequest->isDirty('status') && $refundRequest->status === 'refunded') {
        // This will be handled in 'updated' event after save
    }
}

public function updated(RefundRequest $refundRequest)
{
    // Handle status change to 'refunded'
    if ($refundRequest->wasChanged('status') && $refundRequest->status === 'refunded') {
        $this->handleRefundCompletion($refundRequest);
    }
    
    // Send notifications for any status change
    $this->sendStatusChangeNotification($refundRequest);
}

protected function handleRefundCompletion(RefundRequest $refundRequest)
{
    DB::transaction(function () use ($refundRequest) {
        $vendor = $refundRequest->vendor;
        $order = $refundRequest->order;
        $customer = $refundRequest->customer;
        
        // 1. Update Customer Points
        // Deduct points earned from refunded products
        $customer->points -= $refundRequest->points_to_deduct;
        
        // Return points used to pay for refunded products
        $customer->points += $refundRequest->points_used;
        
        $customer->save();
        
        // Create points transactions for tracking
        if ($refundRequest->points_to_deduct > 0) {
            UserPointsTransaction::create([
                'user_id' => $customer->id,
                'points' => -$refundRequest->points_to_deduct,
                'type' => 'deducted',
                'transactionable_type' => RefundRequest::class,
                'transactionable_id' => $refundRequest->id,
                'description' => "Points deducted for refund: {$refundRequest->refund_number}",
            ]);
        }
        
        if ($refundRequest->points_used > 0) {
            UserPointsTransaction::create([
                'user_id' => $customer->id,
                'points' => $refundRequest->points_used,
                'type' => 'refunded',
                'transactionable_type' => RefundRequest::class,
                'transactionable_id' => $refundRequest->id,
                'description' => "Points refunded for refund: {$refundRequest->refund_number}",
            ]);
        }
        
        // 2. Mark Order Products as Refunded
        // This is the KEY step - marking products as refunded will automatically:
        // - Exclude them from vendor's orders_price calculation
        // - Exclude them from vendor's bnaia_commission calculation
        // - Reduce vendor's total_balance automatically
        foreach ($refundRequest->items as $item) {
            $orderProduct = $item->orderProduct;
            $orderProduct->is_refunded = true;
            $orderProduct->refunded_amount = $item->refund_amount;
            $orderProduct->refunded_at = now();
            $orderProduct->save();
        }
        
        // 3. Update Order - Track Total Refunded Amount
        $order->refunded_amount = ($order->refunded_amount ?? 0) + $refundRequest->total_refund_amount;
        $order->save();
        
        // 4. Reverse Stock Bookings
        // Change fulfilled bookings back to released for refunded products
        $orderProductIds = $refundRequest->items->pluck('order_product_id');
        
        StockBooking::where('order_id', $order->id)
            ->whereIn('order_product_id', $orderProductIds)
            ->where('status', StockBooking::STATUS_FULFILLED)
            ->update([
                'status' => StockBooking::STATUS_RELEASED,
                'released_at' => now(),
                'notes' => 'Released due to refund: ' . $refundRequest->refund_number,
            ]);
        
        // 5. Log the refund completion
        $commissionReversed = $this->calculateCommissionReversal($refundRequest);
        
        activity()
            ->performedOn($refundRequest)
            ->causedBy(auth()->user() ?? $customer)
            ->withProperties([
                'refund_number' => $refundRequest->refund_number,
                'vendor_id' => $vendor->id,
                'total_refund' => $refundRequest->total_refund_amount,
                'commission_reversed' => $commissionReversed,
                'points_deducted' => $refundRequest->points_to_deduct,
                'points_returned' => $refundRequest->points_used,
            ])
            ->log('Refund completed');
    });
}

protected function calculateCommissionReversal(RefundRequest $refundRequest): float
{
    $totalCommission = 0;
    
    foreach ($refundRequest->items as $item) {
        $orderProduct = $item->orderProduct;
        
        // Get commission percentage (product or department)
        $commissionPercent = $orderProduct->commission > 0 
            ? $orderProduct->commission 
            : ($orderProduct->vendorProduct->product->department->commission ?? 0);
        
        // Calculate commission on refunded amount (price + shipping)
        $refundableAmount = $item->total_price + $item->shipping_amount;
        $commission = ($refundableAmount * $commissionPercent) / 100;
        
        $totalCommission += $commission;
    }
    
    return $totalCommission;
}

protected function sendStatusChangeNotification(RefundRequest $refundRequest)
{
    $customer = $refundRequest->customer;
    $vendor = $refundRequest->vendor;
    
    // Notify customer about status change
    switch ($refundRequest->status) {
        case 'approved':
            $customer->notify(new RefundApprovedNotification($refundRequest));
            break;
        case 'rejected':
            $customer->notify(new RefundRejectedNotification($refundRequest));
            break;
        case 'in_progress':
        case 'picked_up':
        case 'refunded':
            $customer->notify(new RefundStatusChangedNotification($refundRequest));
            break;
    }
}
```

**Important Notes:**
- When refund is completed, order products are marked as `is_refunded = true`
- Vendor balance is **automatically recalculated** because:
  - `orders_price` query excludes refunded products
  - `bnaia_commission` query excludes refunded products
  - `total_balance = orders_price - bnaia_commission` (automatically reduced)
- Stock bookings are reversed (fulfilled → released)
- Customer points are adjusted (deduct earned, return used)
- **No withdrawal records are created** - the balance just reflects the new reality
- All operations are wrapped in a database transaction for data integrity

### Notifications:
- RefundRequestCreatedNotification.php (to vendor when customer creates refund)
- RefundStatusChangedNotification.php (to customer when vendor changes status)
- RefundApprovedNotification.php (to customer when vendor approves)
- RefundRejectedNotification.php (to customer when vendor rejects)

### Resources (API):
- RefundRequestResource.php
- RefundRequestItemResource.php
- RefundSettingsResource.php

### Views (Vendor/Admin):
- vendor/refund-requests/index.blade.php
- vendor/refund-requests/show.blade.php
- admin/refund-requests/index.blade.php
- admin/refund-requests/show.blade.php
- admin/refund-settings/index.blade.php

### Routes:
- api.php (customer endpoints)
- web.php (vendor/admin endpoints)

## Status Flow

```
Customer submits → pending (refund_requests.status = 'pending')
                ↓
Vendor reviews → approved/rejected (refund_requests.status = 'approved' or 'rejected')
                ↓
Vendor starts process → in_progress (refund_requests.status = 'in_progress')
                ↓
Driver collects → picked_up (refund_requests.status = 'picked_up')
                ↓
Process complete → refunded (refund_requests.status = 'refunded')
                ↓
Update accounting → vendor balance adjusted
```

**Important Notes:**
- Each `refund_request` has its own status managed by the vendor
- If customer refunds products from multiple vendors, each vendor manages their own refund request independently
- Status is stored in `refund_requests.status` (not in items table)
- Vendor can add notes in `refund_requests.vendor_notes`

## Next Steps
1. Create migrations (including add `is_refunded` and `refunded_amount` to order_products)
2. Create models with relationships
3. **Update Vendor model** to exclude refunded products from commission and balance calculations
4. Create RefundRequestObserver for notifications and financial updates
5. Create repositories and services (including RefundCalculationService)
6. Create API controllers (Customer, Vendor, Admin)
7. Create dashboard controllers and views
8. Create notification classes
9. Add routes (API and web)
10. **Test refund flow** including commission recalculation and withdrawal adjustments
11. **Test edge cases**: partial refunds, multi-vendor orders, points, promo codes
7. Create views
8. Add routes
9. Test workflow

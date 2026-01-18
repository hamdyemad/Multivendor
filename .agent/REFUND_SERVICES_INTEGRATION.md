# Refund System - Services Integration

## Overview
Refactored RefundRequestObserver to use dedicated services instead of directly accessing models, following clean architecture principles.

---

## Services Created/Updated

### 1. UserPointsService
**File:** `Modules/SystemSetting/app/Services/UserPointsService.php`

**Purpose:** Centralized service for managing user points transactions

**Methods:**
- `addPoints()` - Add points to user (type: earned)
- `deductPoints()` - Deduct points from user (type: adjusted)
- `redeemPoints()` - Redeem points for purchase (type: redeemed)
- `expirePoints()` - Expire points (type: expired)
- `getUserPoints()` - Get user's total points
- `getUserPointsHistory()` - Get user's points transaction history
- `getUserPointsBalance()` - Get points balance breakdown by type

**Features:**
- Automatic user points update
- Transaction logging
- Database transactions for consistency
- Comprehensive logging

**Usage in Refund:**
```php
// Deduct points
$this->userPointsService->deductPoints(
    userId: $customer->id,
    points: $refundRequest->points_to_deduct,
    transactionableType: RefundRequest::class,
    transactionableId: $refundRequest->id,
    description: "Points deducted for refund: {$refundRequest->refund_number}"
);

// Add points back
$this->userPointsService->addPoints(
    userId: $customer->id,
    points: $refundRequest->points_used,
    transactionableType: RefundRequest::class,
    transactionableId: $refundRequest->id,
    description: "Points refunded for refund: {$refundRequest->refund_number}"
);
```

---

### 2. StockBookingService (Updated)
**File:** `Modules/CatalogManagement/app/Services/StockBookingService.php`

**New Method Added:** `releaseRefundedStock()`

**Purpose:** Release fulfilled stock bookings when products are refunded

**Parameters:**
- `orderId` - The order ID
- `orderProductIds` - Array of order product IDs being refunded
- `refundNumber` - Refund number for tracking

**Features:**
- Updates stock booking status from FULFILLED to RELEASED
- Sets released_at timestamp
- Adds notes with refund number
- Database transaction for consistency
- Comprehensive logging

**Usage in Refund:**
```php
$orderProductIds = $refundRequest->items->pluck('order_product_id')->toArray();
$this->stockBookingService->releaseRefundedStock(
    orderId: $order->id,
    orderProductIds: $orderProductIds,
    refundNumber: $refundRequest->refund_number
);
```

---

## RefundRequestObserver Updates

### Before (Direct Model Access):
```php
// Direct UserPointsTransaction creation
UserPointsTransaction::create([...]);

// Direct StockBooking update
StockBooking::where('order_id', $order->id)
    ->whereIn('order_product_id', $orderProductIds)
    ->update([...]);
```

### After (Service Layer):
```php
// Use UserPointsService
$this->userPointsService->deductPoints(...);
$this->userPointsService->addPoints(...);

// Use StockBookingService
$this->stockBookingService->releaseRefundedStock(...);
```

---

## Benefits

### 1. Separation of Concerns
- Observer focuses on event handling
- Services handle business logic
- Models remain clean data structures

### 2. Reusability
- Services can be used across multiple modules
- Consistent points/stock management everywhere
- Single source of truth for business rules

### 3. Testability
- Services can be mocked in tests
- Observer logic easier to test
- Unit tests for each service method

### 4. Maintainability
- Changes to points logic only in UserPointsService
- Changes to stock logic only in StockBookingService
- Observer remains stable

### 5. Logging & Monitoring
- Centralized logging in services
- Easier to track points/stock operations
- Better debugging capabilities

---

## Architecture Flow

```
RefundRequest Status Changed
        ↓
RefundRequestObserver::updated()
        ↓
    [Status = 'refunded']
        ↓
handleRefundCompletion()
        ↓
    ┌───────────────────────────────────┐
    │                                   │
    ↓                                   ↓
UserPointsService              StockBookingService
    ↓                                   ↓
- deductPoints()               - releaseRefundedStock()
- addPoints()                          ↓
    ↓                          Update StockBooking
Update User Points                     ↓
    ↓                          Log stock release
Create Transaction
    ↓
Log points update
```

---

## Transaction Types

### UserPointsTransaction Types:
1. **earned** - Points added (rewards, refunds, bonuses)
2. **redeemed** - Points used for purchases
3. **adjusted** - Manual adjustments (admin corrections, deductions)
4. **expired** - Points that have expired

### Refund Uses:
- **adjusted** - When deducting points earned from original order
- **earned** - When returning points customer used during checkout

---

## Stock Booking Statuses

1. **booked** - Stock reserved for order
2. **allocated** - Stock allocated to specific warehouse
3. **fulfilled** - Stock delivered to customer
4. **released** - Stock booking released (canceled/refunded)

### Refund Flow:
- Finds bookings with status = **fulfilled**
- Updates to status = **released**
- Adds refund number to notes
- Sets released_at timestamp

---

## Testing Checklist

- [ ] UserPointsService methods work correctly
- [ ] Points are added/deducted properly
- [ ] Transactions are created with correct types
- [ ] StockBookingService releases refunded stock
- [ ] Stock status changes from fulfilled to released
- [ ] Refund observer uses services correctly
- [ ] Database transactions work properly
- [ ] Logging captures all operations
- [ ] Services can be mocked in tests

---

## Future Enhancements

### UserPointsService:
- Add points expiration scheduling
- Implement points transfer between users
- Add points history filtering
- Create points summary reports

### StockBookingService:
- Add stock reallocation for refunds
- Implement automatic restocking
- Create stock movement reports
- Add stock reservation priorities

---

## Dependencies

### RefundRequestObserver requires:
- `RefundNotificationService` - For notifications
- `StockBookingService` - For stock management
- `UserPointsService` - For points management

All services are injected via constructor dependency injection.

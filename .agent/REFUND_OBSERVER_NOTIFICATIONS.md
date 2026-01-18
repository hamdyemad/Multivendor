# Refund Observer - Automatic Notifications

## Overview
Added automatic notification handling to RefundRequestObserver. Notifications are now sent automatically when refund requests are created or updated, removing the need for manual notification calls in the service layer.

---

## Changes Made

### 1. Observer - Added `created` Event
**File:** `Modules/Refund/app/Observers/RefundRequestObserver.php`

**New Method:**
```php
public function created(RefundRequest $refundRequest): void
{
    // Only send notifications for vendor refunds (not parent)
    if (!$refundRequest->is_parent && $refundRequest->vendor_id) {
        // Notify vendor about new refund request
        $this->notificationService->notifyVendorNewRefund($refundRequest);
    }
    
    // If it's a parent refund, notify customer
    if ($refundRequest->is_parent) {
        $this->notificationService->notifyRefundCreated($refundRequest);
    }
}
```

**Logic:**
- Detects when a refund request is created
- If it's a vendor refund (child) → Notify vendor
- If it's a parent refund → Notify customer
- Automatically triggered by Eloquent

---

### 2. Service - Removed Manual Notifications
**File:** `Modules/Refund/app/Services/RefundRequestService.php`

**Before:**
```php
public function createRefund(array $data, $user)
{
    $parentRefund = $this->repository->createRefundWithVendorSplit($data, $user);

    // Manual notification calls
    $this->notificationService->notifyRefundCreated($parentRefund);
    
    foreach ($parentRefund->vendorRefunds as $vendorRefund) {
        $this->notificationService->notifyVendorNewRefund($vendorRefund);
    }

    return $parentRefund;
}
```

**After:**
```php
public function createRefund(array $data, $user)
{
    // Observer automatically sends notifications
    return $this->repository->createRefundWithVendorSplit($data, $user);
}
```

---

## Notification Flow

### Creating Refund Request

```
Customer submits refund
    ↓
Repository creates parent refund
    ↓
Observer::created() triggered
    ↓
Notify customer (parent refund created)
    ↓
Repository creates vendor refund 1
    ↓
Observer::created() triggered
    ↓
Notify vendor 1 (new refund request)
    ↓
Repository creates vendor refund 2
    ↓
Observer::created() triggered
    ↓
Notify vendor 2 (new refund request)
    ↓
Repository creates vendor refund 3
    ↓
Observer::created() triggered
    ↓
Notify vendor 3 (new refund request)
```

---

## Observer Events

### 1. `created` Event
**Triggered:** When refund request is created

**Actions:**
- Check if parent refund → Notify customer
- Check if vendor refund → Notify vendor
- Sends both Laravel and Firebase notifications

**Notifications Sent:**
- Customer: "Refund request created successfully"
- Vendor: "New refund request from {customer}"

---

### 2. `updated` Event
**Triggered:** When refund request is updated

**Actions:**
- Check if status changed → Notify customer and vendor
- Check if status is 'refunded' → Process refund completion

**Notifications Sent:**
- Customer: "Refund status updated to {status}"
- Vendor: "Refund status updated to {status}"

---

## Benefits

### 1. Automatic Notifications
- No need to manually call notification methods
- Notifications always sent when refund created
- Can't forget to send notifications

### 2. Cleaner Service Layer
```php
// Before (manual)
public function createRefund($data, $user)
{
    $refund = $this->repository->create($data);
    $this->notificationService->notify($refund); // Manual
    return $refund;
}

// After (automatic)
public function createRefund($data, $user)
{
    return $this->repository->create($data); // Observer handles it
}
```

### 3. Consistent Behavior
- Every refund creation triggers notifications
- No conditional notification logic in service
- Centralized notification handling

### 4. Separation of Concerns
- Service: Business operations
- Observer: Side effects (notifications, logging)
- Clear responsibility boundaries

### 5. Easy to Extend
```php
public function created(RefundRequest $refundRequest): void
{
    // Send notifications
    $this->notificationService->notify($refundRequest);
    
    // Easy to add more side effects
    $this->logService->logRefundCreated($refundRequest);
    $this->analyticsService->trackRefund($refundRequest);
    $this->webhookService->sendWebhook($refundRequest);
}
```

---

## Notification Types

### Customer Notifications

#### Refund Created
```json
{
  "type": "refund_created",
  "title": "Refund Request Created",
  "body": "Your refund request REF-20260118-0100 has been created successfully",
  "data": {
    "refund_id": "100",
    "refund_number": "REF-20260118-0100",
    "order_id": "123"
  }
}
```

#### Status Changed
```json
{
  "type": "refund_status_changed",
  "title": "Refund Status Updated",
  "body": "Your refund request REF-20260118-0100 status has been updated to Approved",
  "data": {
    "refund_id": "100",
    "old_status": "pending",
    "new_status": "approved"
  }
}
```

---

### Vendor Notifications

#### New Refund Request
```json
{
  "type": "new_refund_request",
  "title": "New Refund Request",
  "body": "New refund request REF-20260118-0101 from John Doe",
  "data": {
    "refund_id": "101",
    "refund_number": "REF-20260118-0101",
    "customer_id": "45",
    "order_id": "123"
  }
}
```

#### Status Changed
```json
{
  "type": "refund_status_changed",
  "title": "Refund Status Updated",
  "body": "Refund request REF-20260118-0101 status has been updated to In Progress",
  "data": {
    "refund_id": "101",
    "old_status": "approved",
    "new_status": "in_progress"
  }
}
```

---

## Observer Lifecycle

### Eloquent Events
```php
// When model is created
RefundRequest::create([...]);
    ↓
Observer::created() is called
    ↓
Notifications sent automatically

// When model is updated
$refund->update(['status' => 'approved']);
    ↓
Observer::updated() is called
    ↓
Status change notifications sent
```

---

## Testing

### Unit Test Example
```php
public function test_observer_sends_notifications_on_create()
{
    // Mock notification service
    $mockNotification = Mockery::mock(RefundNotificationService::class);
    
    // Expect notification to be called
    $mockNotification->shouldReceive('notifyRefundCreated')
        ->once()
        ->with(Mockery::type(RefundRequest::class));
    
    // Bind mock to container
    $this->app->instance(RefundNotificationService::class, $mockNotification);
    
    // Create refund (observer should trigger)
    $refund = RefundRequest::create([
        'is_parent' => true,
        'order_id' => 123,
        'customer_id' => 45,
        'status' => 'pending',
    ]);
    
    // Assert notification was called
    $mockNotification->shouldHaveReceived('notifyRefundCreated');
}
```

---

## Disabling Observer (If Needed)

### Temporarily Disable
```php
// Disable observer for specific operation
RefundRequest::withoutEvents(function () {
    RefundRequest::create([...]); // No notifications sent
});
```

### Disable in Tests
```php
public function setUp(): void
{
    parent::setUp();
    
    // Disable observers for all tests
    RefundRequest::unsetEventDispatcher();
}
```

---

## Architecture Comparison

### Before (Manual Notifications)
```
Service Layer:
- Create refund
- Send customer notification
- Loop through vendors
- Send vendor notifications
- Return refund

Lines of code: ~15
```

### After (Automatic Notifications)
```
Service Layer:
- Create refund
- Return refund

Lines of code: ~2

Observer Layer:
- Detect creation
- Send notifications automatically

Lines of code: ~10 (but reusable)
```

---

## Summary

### Service Layer Changes:
- ✅ Removed manual notification calls
- ✅ Simplified to single repository call
- ✅ Cleaner, more focused code

### Observer Changes:
- ✅ Added `created` event handler
- ✅ Automatic customer notifications
- ✅ Automatic vendor notifications
- ✅ Handles both parent and vendor refunds

### Benefits Achieved:
- ✅ Automatic notifications on creation
- ✅ Cleaner service layer
- ✅ Consistent notification behavior
- ✅ Centralized side effects
- ✅ Easy to extend with more actions

Notifications are now handled automatically by the observer, making the service layer cleaner and ensuring notifications are always sent when refunds are created or updated.

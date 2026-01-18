# Refund System - Thin Service Layer

## Overview
Refactored the refund system to move ALL business logic from the service layer to the repository layer. The service now acts as a thin wrapper that only handles notifications.

---

## Architecture Change

### Before (Fat Service):
```
Controller → Service (Business Logic) → Repository (Data Access) → Model
```

### After (Thin Service):
```
Controller → Service (Notifications Only) → Repository (Business Logic + Data Access) → Model
```

---

## Service Layer (Simplified)

**File:** `Modules/Refund/app/Services/RefundRequestService.php`

**Lines of Code:** ~60 (down from ~200)

**Responsibilities:**
1. Call repository methods
2. Handle notifications only

**Methods:**
```php
public function getAllRefunds(array $filters, int $perPage = 15)
{
    return $this->repository->getAllPaginated($filters, $perPage);
}

public function getRefundById(int $id)
{
    return $this->repository->findById($id);
}

public function createRefund(array $data, $user)
{
    // Create refund through repository
    $parentRefund = $this->repository->createRefundWithVendorSplit($data, $user);

    // Send notifications (only service responsibility)
    $this->notificationService->notifyRefundCreated($parentRefund);
    
    foreach ($parentRefund->vendorRefunds as $vendorRefund) {
        $this->notificationService->notifyVendorNewRefund($vendorRefund);
    }

    return $parentRefund;
}

public function updateRefundStatus(int $id, array $data, $user)
{
    return $this->repository->updateRefundStatus($id, $data, $user);
}

public function cancelRefund(int $id, $user)
{
    return $this->repository->cancelRefund($id, $user);
}

public function getStatistics(array $filters)
{
    return $this->repository->getStatistics($filters);
}

public function canUserAccessRefund(int $id, $user): bool
{
    return $this->repository->canUserAccessRefund($id, $user);
}
```

---

## Repository Layer (Enhanced)

**File:** `Modules/Refund/app/Repositories/RefundRequestRepository.php`

**New Responsibilities:**
1. All business logic
2. Data validation
3. Transaction management
4. Data access
5. Calculations

**New Methods:**

### `createRefundWithVendorSplit(array $data, $user)`
**Purpose:** Create parent refund and split into vendor refunds

**Logic:**
- Validates order ownership
- Groups items by vendor
- Creates parent refund
- Creates vendor refunds
- Creates refund items
- Calculates totals
- Manages database transactions

**Code:**
```php
public function createRefundWithVendorSplit(array $data, $user)
{
    DB::beginTransaction();
    try {
        // Get order
        $order = Order::findOrFail($data['order_id']);

        // Verify customer owns this order
        if ($order->customer_id !== $user->id) {
            throw new \Exception('Unauthorized access to this order');
        }

        // Group items by vendor
        $itemsByVendor = $this->groupItemsByVendor($data['items']);

        // Create parent refund
        $parentRefund = $this->create([...]);

        // Create vendor refunds
        foreach ($itemsByVendor as $vendorId => $vendorItems) {
            $this->createVendorRefund($parentRefund, $order, $vendorId, $vendorItems, $data);
        }

        // Calculate totals
        $parentRefund->calculateTotals();

        DB::commit();

        return $this->getRefundWithRelations($parentRefund->id, [...]);
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

### `updateRefundStatus(int $id, array $data, $user)`
**Purpose:** Update refund status with authorization

**Logic:**
- Checks user authorization
- Updates status
- Updates notes

### `cancelRefund(int $id, $user)`
**Purpose:** Cancel refund with validation

**Logic:**
- Checks if user can cancel
- Updates status to cancelled

### `groupItemsByVendor(array $items): array`
**Purpose:** Group refund items by vendor ID

**Logic:**
- Fetches order products
- Groups by vendor_id
- Caches order product data

### `createVendorRefund(...)`
**Purpose:** Create vendor-specific refund

**Logic:**
- Creates vendor refund record
- Creates refund items for vendor
- Creates items in parent refund
- Calculates vendor totals

---

## Interface Updates

**File:** `Modules/Refund/app/Interfaces/RefundRequestRepositoryInterface.php`

**New Methods:**
```php
// Main refund operations
public function createRefundWithVendorSplit(array $data, $user);
public function updateRefundStatus(int $id, array $data, $user);
public function cancelRefund(int $id, $user);

// Helper methods
public function getRefundWithRelations(int $refundId, array $relations = []);
```

**Removed Methods:**
```php
// No longer needed - internal to repository
public function getOrderById(int $orderId);
public function getOrderProductById(int $orderProductId);
public function createRefundItem(array $data);
public function calculateRefundTotals(int $refundId): void;
```

---

## Benefits

### 1. Thin Service Layer
- Service is now ~60 lines (was ~200)
- Only handles notifications
- Easy to understand
- Minimal logic

### 2. Fat Repository Layer
- All business logic in one place
- Complete control over data operations
- Transaction management
- Better encapsulation

### 3. Single Responsibility
- **Service**: Orchestration + Notifications
- **Repository**: Business Logic + Data Access
- **Model**: Data Structure + Relationships

### 4. Easier Testing
```php
// Test repository directly
$repository = new RefundRequestRepository($model);
$refund = $repository->createRefundWithVendorSplit($data, $user);

// Test service with mock repository
$mockRepo = Mockery::mock(RefundRequestRepositoryInterface::class);
$mockRepo->shouldReceive('createRefundWithVendorSplit')->andReturn($refund);
$service = new RefundRequestService($mockRepo, $notificationService);
```

### 5. Better Reusability
- Repository methods can be called from anywhere
- No need to go through service
- Direct access to business logic

---

## Code Comparison

### Creating Refund

**Before (Service had logic):**
```php
// Service
public function createRefund(array $data, $user)
{
    DB::beginTransaction();
    try {
        $order = $this->repository->getOrderById($data['order_id']);
        
        if ($order->customer_id !== $user->id) {
            throw new \Exception('Unauthorized');
        }
        
        $itemsByVendor = $this->groupItemsByVendor($data['items']);
        $parentRefund = $this->repository->create([...]);
        
        foreach ($itemsByVendor as $vendorId => $items) {
            $this->createVendorRefund(...);
        }
        
        $this->repository->calculateRefundTotals($parentRefund->id);
        DB::commit();
        
        $this->notificationService->notifyRefundCreated($parentRefund);
        return $parentRefund;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

protected function groupItemsByVendor(...) { ... }
protected function createVendorRefund(...) { ... }
```

**After (Repository has logic):**
```php
// Service
public function createRefund(array $data, $user)
{
    $parentRefund = $this->repository->createRefundWithVendorSplit($data, $user);
    
    $this->notificationService->notifyRefundCreated($parentRefund);
    
    foreach ($parentRefund->vendorRefunds as $vendorRefund) {
        $this->notificationService->notifyVendorNewRefund($vendorRefund);
    }
    
    return $parentRefund;
}

// Repository
public function createRefundWithVendorSplit(array $data, $user)
{
    DB::beginTransaction();
    try {
        // All business logic here
        ...
        DB::commit();
        return $parentRefund;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

---

## Responsibilities Matrix

| Layer | Before | After |
|-------|--------|-------|
| **Controller** | HTTP handling | HTTP handling |
| **Service** | Business logic + Notifications | Notifications only |
| **Repository** | Data access only | Business logic + Data access |
| **Model** | Data structure | Data structure |

---

## When to Use Each Layer

### Controller
- Receive HTTP requests
- Validate request format
- Call service methods
- Return HTTP responses

### Service
- Orchestrate operations
- Send notifications
- Handle cross-cutting concerns
- Call repository methods

### Repository
- Implement business logic
- Manage transactions
- Access database
- Validate business rules
- Calculate values

### Model
- Define data structure
- Define relationships
- Define accessors/mutators
- Define scopes

---

## Migration Guide

### For New Features

**Don't do this:**
```php
// Service
public function newFeature($data)
{
    // Business logic here
    $result = Model::where(...)->get();
    // More logic
    return $result;
}
```

**Do this:**
```php
// Service
public function newFeature($data)
{
    return $this->repository->newFeature($data);
}

// Repository
public function newFeature($data)
{
    // Business logic here
    $result = $this->model->where(...)->get();
    // More logic
    return $result;
}
```

---

## Summary

### Service Layer Changes:
- ❌ Removed all business logic
- ❌ Removed transaction management
- ❌ Removed data validation
- ❌ Removed helper methods
- ✅ Kept notification handling
- ✅ Kept method orchestration

### Repository Layer Changes:
- ✅ Added all business logic
- ✅ Added transaction management
- ✅ Added data validation
- ✅ Added helper methods
- ✅ Complete control over data

### Benefits Achieved:
- ✅ Thin service layer (~60 lines)
- ✅ Fat repository layer (complete logic)
- ✅ Clear separation of concerns
- ✅ Easier to test
- ✅ Better reusability
- ✅ Simpler service interface

The service is now a thin wrapper that only handles notifications, while the repository contains all business logic and data access.

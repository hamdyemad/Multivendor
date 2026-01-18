# Refund Service - Repository Pattern Refactoring

## Overview
Refactored RefundRequestService to follow clean architecture principles by removing all direct model access and using repository methods exclusively.

---

## Changes Made

### 1. Repository Interface Updates
**File:** `Modules/Refund/app/Interfaces/RefundRequestRepositoryInterface.php`

**New Methods Added:**
```php
// Order operations
public function getOrderById(int $orderId);
public function getOrderProductById(int $orderProductId);

// Refund item operations
public function createRefundItem(array $data);

// Refund calculations
public function calculateRefundTotals(int $refundId): void;

// Refund relationships
public function getRefundWithRelations(int $refundId, array $relations = []);
```

**Purpose:**
- Encapsulate all data access logic
- Provide clean interface for service layer
- Enable easy mocking for tests

---

### 2. Repository Implementation
**File:** `Modules/Refund/app/Repositories/RefundRequestRepository.php`

**Implemented Methods:**

#### `getOrderById(int $orderId)`
```php
public function getOrderById(int $orderId)
{
    return \Modules\Order\app\Models\Order::findOrFail($orderId);
}
```
- Fetches order by ID
- Throws exception if not found

#### `getOrderProductById(int $orderProductId)`
```php
public function getOrderProductById(int $orderProductId)
{
    return \Modules\Order\app\Models\OrderProduct::findOrFail($orderProductId);
}
```
- Fetches order product by ID
- Throws exception if not found

#### `createRefundItem(array $data)`
```php
public function createRefundItem(array $data)
{
    return \Modules\Refund\app\Models\RefundRequestItem::create($data);
}
```
- Creates refund item record
- Returns created item

#### `calculateRefundTotals(int $refundId): void`
```php
public function calculateRefundTotals(int $refundId): void
{
    $refund = $this->findById($refundId);
    $refund->calculateTotals();
}
```
- Calculates and updates refund totals
- Uses model method internally

#### `getRefundWithRelations(int $refundId, array $relations = [])`
```php
public function getRefundWithRelations(int $refundId, array $relations = [])
{
    return $this->model->with($relations)->findOrFail($refundId);
}
```
- Fetches refund with eager loaded relationships
- Flexible relationship loading

---

### 3. Service Layer Refactoring
**File:** `Modules/Refund/app/Services/RefundRequestService.php`

**Before (Direct Model Access):**
```php
// Direct Order model access
$order = Order::findOrFail($data['order_id']);

// Direct OrderProduct model access
$orderProduct = OrderProduct::findOrFail($item['order_product_id']);

// Direct RefundRequestItem model access
RefundRequestItem::create([...]);

// Direct model method call
$parentRefund->calculateTotals();

// Direct model refresh
$parentRefund->fresh(['vendorRefunds.items.orderProduct', 'items']);
```

**After (Repository Pattern):**
```php
// Through repository
$order = $this->repository->getOrderById($data['order_id']);

// Through repository
$orderProduct = $this->repository->getOrderProductById($item['order_product_id']);

// Through repository
$this->repository->createRefundItem([...]);

// Through repository
$this->repository->calculateRefundTotals($parentRefund->id);

// Through repository
$parentRefund = $this->repository->getRefundWithRelations(
    $parentRefund->id,
    ['vendorRefunds.items.orderProduct', 'items']
);
```

---

## Benefits

### 1. Separation of Concerns
- **Service Layer**: Business logic only
- **Repository Layer**: Data access only
- **Model Layer**: Data structure and relationships

### 2. Testability
```php
// Easy to mock repository in tests
$mockRepository = Mockery::mock(RefundRequestRepositoryInterface::class);
$mockRepository->shouldReceive('getOrderById')->andReturn($order);
$service = new RefundRequestService($mockRepository, $notificationService);
```

### 3. Maintainability
- Changes to data access logic only in repository
- Service layer remains stable
- Easy to switch data sources

### 4. Consistency
- All data access through repository
- Consistent error handling
- Centralized query logic

### 5. Flexibility
- Can add caching in repository
- Can add query optimization
- Can switch database implementations

---

## Architecture Flow

### Before:
```
Service → Direct Model Access
    ↓
Order::findOrFail()
OrderProduct::findOrFail()
RefundRequestItem::create()
$model->calculateTotals()
```

### After:
```
Service → Repository → Model
    ↓         ↓          ↓
Business  Data      Database
Logic     Access    Operations
```

---

## Code Comparison

### Creating Refund Items

**Before:**
```php
foreach ($items as $item) {
    $orderProduct = OrderProduct::findOrFail($item['order_product_id']);
    
    RefundRequestItem::create([
        'refund_request_id' => $vendorRefund->id,
        'order_product_id' => $orderProduct->id,
        'vendor_id' => $vendorId,
        'quantity' => $item['quantity'],
        'unit_price' => $orderProduct->price,
        'total_price' => $orderProduct->price * $item['quantity'],
        'reason' => $item['reason'] ?? null,
    ]);
}
```

**After:**
```php
foreach ($items as $item) {
    $orderProduct = $item['order_product']; // Cached from grouping
    
    $this->repository->createRefundItem([
        'refund_request_id' => $vendorRefund->id,
        'order_product_id' => $orderProduct->id,
        'vendor_id' => $vendorId,
        'quantity' => $item['quantity'],
        'unit_price' => $orderProduct->price,
        'total_price' => $orderProduct->price * $item['quantity'],
        'reason' => $item['reason'],
    ]);
}
```

---

## Performance Optimization

### Caching Order Products
The refactored service now caches order products during grouping:

```php
protected function groupItemsByVendor(array $items): array
{
    $grouped = [];
    
    foreach ($items as $item) {
        $orderProduct = $this->repository->getOrderProductById($item['order_product_id']);
        $vendorId = $orderProduct->vendor_id;
        
        if (!isset($grouped[$vendorId])) {
            $grouped[$vendorId] = [];
        }
        
        $grouped[$vendorId][] = [
            'order_product_id' => $item['order_product_id'],
            'quantity' => $item['quantity'],
            'reason' => $item['reason'] ?? null,
            'order_product' => $orderProduct, // Cache for later use
        ];
    }
    
    return $grouped;
}
```

**Benefits:**
- Fetches each order product only once
- Reduces database queries
- Improves performance

---

## Testing Examples

### Unit Test with Mock Repository
```php
public function test_create_refund_with_multiple_vendors()
{
    // Mock repository
    $mockRepo = Mockery::mock(RefundRequestRepositoryInterface::class);
    
    // Setup expectations
    $mockRepo->shouldReceive('getOrderById')
        ->once()
        ->with(123)
        ->andReturn($order);
    
    $mockRepo->shouldReceive('getOrderProductById')
        ->times(3)
        ->andReturn($orderProduct1, $orderProduct2, $orderProduct3);
    
    $mockRepo->shouldReceive('create')
        ->times(4) // 1 parent + 3 vendors
        ->andReturn($refund);
    
    $mockRepo->shouldReceive('createRefundItem')
        ->times(6) // 3 items × 2 (vendor + parent)
        ->andReturn($item);
    
    // Create service with mock
    $service = new RefundRequestService($mockRepo, $notificationService);
    
    // Test
    $result = $service->createRefund($data, $user);
    
    // Assert
    $this->assertNotNull($result);
}
```

---

## Migration Guide

### For Developers

**Old Way (Don't use):**
```php
// In service
$order = Order::findOrFail($id);
$item = RefundRequestItem::create($data);
```

**New Way (Use this):**
```php
// In service
$order = $this->repository->getOrderById($id);
$item = $this->repository->createRefundItem($data);
```

### Adding New Repository Methods

1. **Add to Interface:**
```php
// RefundRequestRepositoryInterface.php
public function newMethod(int $id);
```

2. **Implement in Repository:**
```php
// RefundRequestRepository.php
public function newMethod(int $id)
{
    return $this->model->where('id', $id)->first();
}
```

3. **Use in Service:**
```php
// RefundRequestService.php
$result = $this->repository->newMethod($id);
```

---

## Summary

### Removed from Service:
- ❌ Direct `Order::findOrFail()` calls
- ❌ Direct `OrderProduct::findOrFail()` calls
- ❌ Direct `RefundRequestItem::create()` calls
- ❌ Direct model method calls
- ❌ Direct model refresh calls

### Added to Repository:
- ✅ `getOrderById()` - Fetch order
- ✅ `getOrderProductById()` - Fetch order product
- ✅ `createRefundItem()` - Create refund item
- ✅ `calculateRefundTotals()` - Calculate totals
- ✅ `getRefundWithRelations()` - Fetch with relationships

### Benefits Achieved:
- ✅ Clean separation of concerns
- ✅ Improved testability
- ✅ Better maintainability
- ✅ Consistent data access
- ✅ Flexible architecture

The service layer now focuses purely on business logic while the repository handles all data access operations.

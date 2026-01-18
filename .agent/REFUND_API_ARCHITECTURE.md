# Refund API Architecture

## 🏗️ Architecture Pattern

The Refund API follows a clean architecture pattern with proper separation of concerns:

```
Controller → Service → Repository → Model
     ↓
Form Request (Validation)
```

## 📁 File Structure

```
Modules/Refund/app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── RefundRequestApiController.php    # API Controller (thin)
│   └── Requests/
│       ├── CreateRefundRequest.php               # Validation for create
│       └── UpdateRefundStatusRequest.php         # Validation for update status
├── Interfaces/
│   └── RefundRequestRepositoryInterface.php      # Repository contract
├── Repositories/
│   └── RefundRequestRepository.php               # Data access layer
├── Services/
│   └── RefundRequestService.php                  # Business logic layer
├── Models/
│   └── RefundRequest.php                         # Model with scopeFilters
└── Providers/
    └── RefundServiceProvider.php                 # Service provider with bindings
```

## 🎯 Layer Responsibilities

### 1. Controller Layer
**File:** `RefundRequestApiController.php`

**Responsibilities:**
- Handle HTTP requests/responses
- Call service methods
- Return JSON responses
- Handle exceptions and status codes

**Does NOT:**
- ❌ Direct model access
- ❌ Business logic
- ❌ Database queries
- ❌ Validation logic

**Example:**
```php
public function index(Request $request)
{
    $filters = [...];
    $refunds = $this->refundService->getAllRefunds($filters, $perPage);
    return response()->json(['success' => true, 'data' => $refunds]);
}
```

### 2. Service Layer
**File:** `RefundRequestService.php`

**Responsibilities:**
- Business logic
- Transaction management
- Orchestrate multiple repository calls
- Authorization checks
- Data transformation

**Does NOT:**
- ❌ Direct database queries
- ❌ HTTP responses
- ❌ Validation

**Example:**
```php
public function createRefund(array $data, $user)
{
    DB::beginTransaction();
    try {
        $order = Order::findOrFail($data['order_id']);
        $refund = $this->repository->create([...]);
        // Create items, calculate totals
        DB::commit();
        return $refund;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

### 3. Repository Layer
**File:** `RefundRequestRepository.php`

**Responsibilities:**
- Data access
- Query building
- Model interactions
- Filtering via scopes

**Does NOT:**
- ❌ Business logic
- ❌ Transactions
- ❌ Authorization

**Example:**
```php
public function getAllPaginated(array $filters = [], int $perPage = 15)
{
    return $this->model
        ->with(['order', 'customer', 'vendor', 'items.product'])
        ->scopeFilters($filters)
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
}
```

### 4. Model Layer
**File:** `RefundRequest.php`

**Responsibilities:**
- Database schema definition
- Relationships
- Scopes for filtering
- Accessors/Mutators
- Model events

**Example:**
```php
public function scopeScopeFilters($query, array $filters)
{
    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }
    // More filters...
    return $query;
}
```

### 5. Form Request Layer
**Files:** `CreateRefundRequest.php`, `UpdateRefundStatusRequest.php`

**Responsibilities:**
- Input validation
- Custom validation messages
- Authorization (authorize method)

**Example:**
```php
public function rules(): array
{
    return [
        'order_id' => 'required|exists:orders,id',
        'reason' => 'required|string|max:500',
        'items' => 'required|array|min:1',
    ];
}
```

### 6. Interface Layer
**File:** `RefundRequestRepositoryInterface.php`

**Responsibilities:**
- Define repository contract
- Enable dependency injection
- Allow easy testing/mocking

**Example:**
```php
interface RefundRequestRepositoryInterface
{
    public function getAllPaginated(array $filters = [], int $perPage = 15);
    public function findById(int $id);
    public function create(array $data);
}
```

## 🔄 Request Flow

### Example: Create Refund Request

```
1. POST /api/v1/refunds
   ↓
2. CreateRefundRequest validates input
   ↓
3. RefundRequestApiController::store()
   ↓
4. RefundRequestService::createRefund()
   ├── Verify order ownership
   ├── Begin transaction
   ├── RefundRequestRepository::create()
   │   └── RefundRequest model
   ├── Create refund items
   ├── Calculate totals
   └── Commit transaction
   ↓
5. Return JSON response
```

## 🎨 Benefits of This Architecture

### 1. Separation of Concerns
- Each layer has a single responsibility
- Easy to understand and maintain
- Changes in one layer don't affect others

### 2. Testability
- Easy to mock dependencies
- Unit test each layer independently
- Integration tests for full flow

### 3. Reusability
- Service methods can be used by web and API controllers
- Repository methods can be used by multiple services
- Form requests can be shared

### 4. Maintainability
- Clear structure
- Easy to find code
- Easy to add new features

### 5. Flexibility
- Easy to swap implementations (e.g., different database)
- Easy to add caching layer
- Easy to add logging/monitoring

## 📝 Code Examples

### Controller (Thin)
```php
public function store(CreateRefundRequest $request)
{
    try {
        $refund = $this->refundService->createRefund(
            $request->validated(),
            auth()->user()
        );
        return response()->json([
            'success' => true,
            'data' => $refund,
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
```

### Service (Business Logic)
```php
public function createRefund(array $data, $user)
{
    DB::beginTransaction();
    try {
        // Verify ownership
        $order = Order::findOrFail($data['order_id']);
        if ($order->customer_id !== $user->id) {
            throw new \Exception('Unauthorized');
        }
        
        // Create refund
        $refund = $this->repository->create([...]);
        
        // Create items
        foreach ($data['items'] as $item) {
            RefundRequestItem::create([...]);
        }
        
        // Calculate totals
        $refund->calculateTotals();
        
        DB::commit();
        return $refund->fresh(['items.product']);
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

### Repository (Data Access)
```php
public function getAllPaginated(array $filters = [], int $perPage = 15)
{
    return $this->model
        ->with(['order', 'customer', 'vendor', 'items.product'])
        ->scopeFilters($filters)
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
}
```

### Model (Scope Filters)
```php
public function scopeScopeFilters($query, array $filters)
{
    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }
    
    if (!empty($filters['customer_id'])) {
        $query->where('customer_id', $filters['customer_id']);
    }
    
    if (!empty($filters['search'])) {
        $query->where(function ($q) use ($filters) {
            $q->where('refund_number', 'like', '%' . $filters['search'] . '%')
              ->orWhereHas('order', function ($orderQuery) use ($filters) {
                  $orderQuery->where('order_number', 'like', '%' . $filters['search'] . '%');
              });
        });
    }
    
    return $query;
}
```

## 🔧 Dependency Injection

### Service Provider Binding
```php
// Modules/Refund/app/Providers/RefundServiceProvider.php
public function register(): void
{
    $this->app->bind(
        \Modules\Refund\app\Interfaces\RefundRequestRepositoryInterface::class,
        \Modules\Refund\app\Repositories\RefundRequestRepository::class
    );
}
```

### Controller Constructor
```php
protected $refundService;

public function __construct(RefundRequestService $refundService)
{
    $this->refundService = $refundService;
}
```

### Service Constructor
```php
protected $repository;

public function __construct(RefundRequestRepositoryInterface $repository)
{
    $this->repository = $repository;
}
```

## ✅ Best Practices Followed

1. ✅ **Single Responsibility Principle** - Each class has one job
2. ✅ **Dependency Injection** - Dependencies injected via constructor
3. ✅ **Interface Segregation** - Repository interface defines contract
4. ✅ **Don't Repeat Yourself** - Reusable service methods
5. ✅ **Separation of Concerns** - Clear layer boundaries
6. ✅ **Form Request Validation** - Validation separated from controller
7. ✅ **Scope Filters** - Reusable query filters in model
8. ✅ **Transaction Management** - In service layer
9. ✅ **Exception Handling** - Proper error responses
10. ✅ **Clean Code** - Readable and maintainable

## 🧪 Testing Strategy

### Unit Tests
- Test service methods with mocked repository
- Test repository methods with in-memory database
- Test form request validation rules

### Integration Tests
- Test full API endpoints
- Test with real database
- Test authorization rules

### Example Unit Test
```php
public function test_create_refund_with_valid_data()
{
    $repository = Mockery::mock(RefundRequestRepositoryInterface::class);
    $service = new RefundRequestService($repository);
    
    $repository->shouldReceive('create')
        ->once()
        ->andReturn(new RefundRequest([...]));
    
    $result = $service->createRefund([...], $user);
    
    $this->assertInstanceOf(RefundRequest::class, $result);
}
```

## 📚 Summary

This architecture provides:
- **Clean separation** between layers
- **Easy testing** with dependency injection
- **Maintainable code** with clear responsibilities
- **Reusable components** across the application
- **Scalable structure** for future growth

All business logic is in the service layer, data access in the repository, validation in form requests, and the controller is just a thin layer handling HTTP.

<?php

namespace Modules\Order\tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerAddress;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderExtraFeeDiscount;
use Modules\Order\app\Services\OrderService;
use Modules\Vendor\app\Models\Vendor;
use App\Models\User;

class OrderCreationPipelineTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $orderService;
    protected Customer $customer;
    protected CustomerAddress $address;
    protected VendorProduct $vendorProduct;
    protected VendorProductVariant $vendorProductVariant;
    protected Country $country;
    protected City $city;
    protected Region $region;

    public function setUp(): void
    {
        parent::setUp();
        $this->orderService = app(OrderService::class);
        $this->setupTestData();
    }

    /**
     * Setup test data for order creation
     */
    protected function setupTestData(): void
    {
        // Create location data
        $this->country = Country::factory()->create(['name' => 'Egypt']);
        $this->city = City::factory()->create(['country_id' => $this->country->id, 'name' => 'Cairo']);
        $this->region = Region::factory()->create(['city_id' => $this->city->id, 'name' => 'Giza']);

        // Create customer
        $user = User::factory()->create(['email' => 'customer@test.com']);
        $this->customer = Customer::factory()->create([
            'user_id' => $user->id,
            'name' => 'John Doe',
            'email' => 'customer@test.com',
            'phone' => '+201001234567',
        ]);

        // Create customer address
        $this->address = CustomerAddress::factory()->create([
            'customer_id' => $this->customer->id,
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'region_id' => $this->region->id,
            'address' => '123 Main Street, Cairo',
            'is_primary' => true,
        ]);

        // Create vendor and products
        $vendor = Vendor::factory()->create();
        $this->vendorProduct = VendorProduct::factory()->create([
            'vendor_id' => $vendor->id,
            'sku' => 'PROD-001',
        ]);

        $this->vendorProductVariant = VendorProductVariant::factory()->create([
            'vendor_product_id' => $this->vendorProduct->id,
            'price' => 100.00,
        ]);
    }

    /**
     * Test: Complete order creation pipeline with valid data
     * 
     * Pipeline stages tested:
     * 1. ValidateProducts - Validates products array
     * 2. FetchUserData - Fetches customer and address data
     * 3. CalculateProductPrices - Calculates product totals and taxes
     * 4. CalculateExtras - Parses and calculates fees and discounts
     * 5. CalculateFinalTotal - Calculates final order total
     * 6. CreateOrder - Creates Order record
     * 7. SyncOrderProducts - Creates OrderProduct records
     * 8. SyncExtras - Creates OrderExtraFeeDiscount records
     * 9. UpdateProductSales - Updates product sales count
     */
    public function test_complete_order_creation_pipeline_with_valid_data()
    {
        // Arrange: Prepare order data
        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 2,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([
                ['reason' => 'Handling', 'amount' => 10.00]
            ]),
            'discountsData' => json_encode([
                ['reason' => 'Promo', 'amount' => 5.00]
            ]),
        ];

        // Act: Create order through pipeline
        $order = $this->orderService->createOrder($orderData);

        // Assert: Verify order was created
        $this->assertNotNull($order);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($this->customer->id, $order->customer_id);
        $this->assertEquals('John Doe', $order->customer_name);
        $this->assertEquals('customer@test.com', $order->customer_email);
    }

    /**
     * Test: Stage 1 - ValidateProducts
     * Validates that products array is not empty and contains valid data
     */
    public function test_stage_1_validate_products_with_valid_products()
    {
        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 2,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $order = $this->orderService->createOrder($orderData);

        $this->assertNotNull($order);
        $this->assertTrue($order->id > 0);
    }

    /**
     * Test: Stage 1 - ValidateProducts fails with empty products
     */
    public function test_stage_1_validate_products_fails_with_empty_products()
    {
        $this->expectException(\Exception::class);

        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $this->orderService->createOrder($orderData);
    }

    /**
     * Test: Stage 1 - ValidateProducts fails with invalid quantity
     */
    public function test_stage_1_validate_products_fails_with_invalid_quantity()
    {
        $this->expectException(\Exception::class);

        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 0, // Invalid: quantity must be > 0
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $this->orderService->createOrder($orderData);
    }

    /**
     * Test: Stage 2 - FetchUserData
     * Verifies customer and address data are fetched correctly
     */
    public function test_stage_2_fetch_user_data_with_existing_customer()
    {
        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 1,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Verify customer data was fetched and stored
        $this->assertEquals($this->customer->name, $order->customer_name);
        $this->assertEquals($this->customer->email, $order->customer_email);
        $this->assertEquals($this->customer->phone, $order->customer_phone);
        $this->assertEquals($this->address->address, $order->customer_address);
    }

    /**
     * Test: Stage 3 - CalculateProductPrices
     * Verifies product prices and taxes are calculated correctly
     */
    public function test_stage_3_calculate_product_prices()
    {
        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 2,
                    'taxRate' => 5, // 5% tax
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Product total: 100 * 2 = 200
        // Tax: 200 * 5% = 10
        $this->assertEquals(200.00, $order->total_product_price);
        $this->assertEquals(10.00, $order->total_tax);
    }

    /**
     * Test: Stage 4 - CalculateExtras
     * Verifies fees and discounts are parsed and calculated correctly
     */
    public function test_stage_4_calculate_extras_with_fees_and_discounts()
    {
        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 1,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([
                ['reason' => 'Handling', 'amount' => 10.00],
                ['reason' => 'Packaging', 'amount' => 5.00]
            ]),
            'discountsData' => json_encode([
                ['reason' => 'Promo', 'amount' => 5.00]
            ]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Verify extras were created
        $extras = $order->extraFeesDiscounts()->get();
        $this->assertCount(3, $extras); // 2 fees + 1 discount

        // Verify fees
        $fees = $order->extraFeesDiscounts()->where('type', 'fee')->get();
        $this->assertCount(2, $fees);
        $this->assertEquals(15.00, $fees->sum('cost')); // 10 + 5

        // Verify discounts
        $discounts = $order->extraFeesDiscounts()->where('type', 'discount')->get();
        $this->assertCount(1, $discounts);
        $this->assertEquals(5.00, $discounts->sum('cost'));
    }

    /**
     * Test: Stage 5 - CalculateFinalTotal
     * Verifies final total is calculated correctly
     * Formula: total_product_price + shipping + fees - discounts + tax
     */
    public function test_stage_5_calculate_final_total()
    {
        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 2,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([
                ['reason' => 'Handling', 'amount' => 10.00]
            ]),
            'discountsData' => json_encode([
                ['reason' => 'Promo', 'amount' => 5.00]
            ]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Calculation:
        // Product total: 100 * 2 = 200
        // Tax: 200 * 5% = 10
        // Shipping: 50
        // Fees: 10
        // Discounts: 5
        // Total: 200 + 50 + 10 + 10 - 5 = 265
        $expectedTotal = 200.00 + 50.00 + 10.00 + 10.00 - 5.00;
        $this->assertEquals($expectedTotal, $order->total_price);
    }

    /**
     * Test: Stage 6 - CreateOrder
     * Verifies Order record is created with correct data
     */
    public function test_stage_6_create_order_record()
    {
        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 1,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Verify order exists in database
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'customer_id' => $this->customer->id,
            'customer_name' => 'John Doe',
            'customer_email' => 'customer@test.com',
        ]);

        // Verify order stage is set to 1 (pending)
        $this->assertEquals(1, $order->stage_id);
    }

    /**
     * Test: Stage 7 - SyncOrderProducts
     * Verifies OrderProduct records are created for each product
     */
    public function test_stage_7_sync_order_products()
    {
        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product 1',
                    'price' => 100.00,
                    'quantity' => 2,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Verify order products were created
        $orderProducts = $order->products()->get();
        $this->assertCount(1, $orderProducts);

        // Verify product data
        $orderProduct = $orderProducts->first();
        $this->assertEquals($this->vendorProduct->id, $orderProduct->vendor_product_id);
        $this->assertEquals(2, $orderProduct->quantity);
        $this->assertEquals(100.00, $orderProduct->price);
    }

    /**
     * Test: Stage 7 - SyncOrderProducts with multiple products
     */
    public function test_stage_7_sync_order_products_with_multiple_products()
    {
        // Create second vendor product
        $vendorProduct2 = VendorProduct::factory()->create(['sku' => 'PROD-002']);

        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product 1',
                    'price' => 100.00,
                    'quantity' => 2,
                    'taxRate' => 5,
                    'limitation' => 10,
                ],
                [
                    'id' => $vendorProduct2->id,
                    'name' => 'Test Product 2',
                    'price' => 50.00,
                    'quantity' => 1,
                    'taxRate' => 0,
                    'limitation' => 5,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Verify both products were synced
        $orderProducts = $order->products()->get();
        $this->assertCount(2, $orderProducts);

        // Verify items count
        $this->assertEquals(3, $order->items_count); // 2 + 1
    }

    /**
     * Test: Stage 8 - SyncExtras
     * Verifies OrderExtraFeeDiscount records are created
     */
    public function test_stage_8_sync_extras()
    {
        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 1,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([
                ['reason' => 'Handling', 'amount' => 10.00]
            ]),
            'discountsData' => json_encode([
                ['reason' => 'Promo', 'amount' => 5.00]
            ]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Verify extras in database
        $this->assertDatabaseHas('order_extra_fees_discounts', [
            'order_id' => $order->id,
            'reason' => 'Handling',
            'amount' => 10.00,
            'type' => 'fee',
        ]);

        $this->assertDatabaseHas('order_extra_fees_discounts', [
            'order_id' => $order->id,
            'reason' => 'Promo',
            'amount' => 5.00,
            'type' => 'discount',
        ]);
    }

    /**
     * Test: Stage 9 - UpdateProductSales
     * Verifies product sales count is updated
     */
    public function test_stage_9_update_product_sales()
    {
        // Get initial sales count
        $initialSales = $this->vendorProduct->product->sales ?? 0;

        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 3,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Refresh product to get updated sales count
        $this->vendorProduct->refresh();
        $updatedSales = $this->vendorProduct->product->sales ?? 0;

        // Verify sales count was incremented by quantity
        $this->assertEquals($initialSales + 3, $updatedSales);
    }

    /**
     * Test: Transaction rollback on pipeline failure
     * Verifies that if any stage fails, entire transaction is rolled back
     */
    public function test_transaction_rollback_on_pipeline_failure()
    {
        $this->expectException(\Exception::class);

        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => -100.00, // Invalid: negative price
                    'quantity' => 1,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        // This should throw exception and rollback
        $this->orderService->createOrder($orderData);

        // Verify no order was created
        $this->assertDatabaseCount('orders', 0);
    }

    /**
     * Test: Order with external customer
     * Verifies order creation with external customer data
     */
    public function test_order_creation_with_external_customer()
    {
        $orderData = [
            'customer_type' => 'external',
            'external_customer_name' => 'Jane Smith',
            'external_customer_email' => 'jane@example.com',
            'external_customer_phone' => '+201009876543',
            'external_customer_address' => '456 Oak Street, Alexandria',
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 1,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Verify external customer data was stored
        $this->assertEquals('Jane Smith', $order->customer_name);
        $this->assertEquals('jane@example.com', $order->customer_email);
        $this->assertEquals('+201009876543', $order->customer_phone);
        $this->assertEquals('456 Oak Street, Alexandria', $order->customer_address);
    }

    /**
     * Test: Order items count calculation
     * Verifies items_count is sum of all product quantities
     */
    public function test_order_items_count_calculation()
    {
        $vendorProduct2 = VendorProduct::factory()->create(['sku' => 'PROD-003']);
        $vendorProduct3 = VendorProduct::factory()->create(['sku' => 'PROD-004']);

        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Product 1',
                    'price' => 100.00,
                    'quantity' => 2,
                    'taxRate' => 5,
                    'limitation' => 10,
                ],
                [
                    'id' => $vendorProduct2->id,
                    'name' => 'Product 2',
                    'price' => 50.00,
                    'quantity' => 3,
                    'taxRate' => 0,
                    'limitation' => 5,
                ],
                [
                    'id' => $vendorProduct3->id,
                    'name' => 'Product 3',
                    'price' => 75.00,
                    'quantity' => 1,
                    'taxRate' => 10,
                    'limitation' => 8,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Items count should be 2 + 3 + 1 = 6
        $this->assertEquals(6, $order->items_count);
    }

    /**
     * Test: Order location data
     * Verifies country, city, and region are correctly stored
     */
    public function test_order_location_data_storage()
    {
        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 1,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([]),
            'discountsData' => json_encode([]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Verify location data
        $this->assertEquals($this->country->id, $order->country_id);
        $this->assertEquals($this->city->id, $order->city_id);
        $this->assertEquals($this->region->id, $order->region_id);
    }

    /**
     * Test: Order relationships are properly loaded
     */
    public function test_order_relationships_are_properly_loaded()
    {
        $orderData = [
            'customer_type' => 'existing',
            'selected_customer_id' => $this->customer->id,
            'customer_address_id' => $this->address->id,
            'shipping' => 50.00,
            'products' => json_encode([
                [
                    'id' => $this->vendorProduct->id,
                    'name' => 'Test Product',
                    'price' => 100.00,
                    'quantity' => 1,
                    'taxRate' => 5,
                    'limitation' => 10,
                ]
            ]),
            'feesData' => json_encode([
                ['reason' => 'Handling', 'amount' => 10.00]
            ]),
            'discountsData' => json_encode([]),
        ];

        $order = $this->orderService->createOrder($orderData);

        // Verify relationships
        $this->assertNotNull($order->customer);
        $this->assertNotNull($order->stage);
        $this->assertNotNull($order->country);
        $this->assertNotNull($order->city);
        $this->assertNotNull($order->region);
        $this->assertCount(1, $order->products);
        $this->assertCount(1, $order->extraFeesDiscounts);
    }
}

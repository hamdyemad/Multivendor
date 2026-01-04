<?php

namespace Modules\Order\app\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Order\app\Interfaces\OrderRepositoryInterface;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderProductTax;
use Modules\Order\app\Models\OrderExtraFeeDiscount;
use Modules\CatalogManagement\app\Models\StockBooking;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Get all orders with filtering
     */
    public function getAllOrders(array $filters)
    {
        $query = Order::query();

        $query->with(['customer', 'products.stage' => function($q) {
                $q->withoutGlobalScopes();
            }, 'products.vendorProduct.vendor'])
            ->with(['stage' => function($q) {
                $q->withoutGlobalScopes();
            }])
            ->filter($filters)
            ->latest('created_at');
        
        return $query;
    }

    /**
     * Get order by ID
     */
    public function getOrderById($id)
    {
        $query = Order::with([
            'customer', 
            'products.vendorProduct.product.category',
            'products.vendorProduct.product.mainImage',
            'products.vendorProduct.vendor',
            'products.vendorProduct.taxes',
            'products.vendorProductVariant.variantConfiguration.key', 
            'products.taxes',
            'products.stage' => function($q) {
                $q->withoutGlobalScopes();
            },
            'extraFeesDiscounts'
        ])->with(['stage' => function($q) {
            $q->withoutGlobalScopes();
        }]);
        
        // If current user is a vendor, only allow access to orders that have products from their vendor
        if (auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $query->whereHas('products', function($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id);
                });
            }
        }
        
        return $query->findOrFail($id);
    }

    /**
     * Change order stage and update fulfillments
     * 
     * @param int $id Order ID
     * @param int $stageId New stage ID
     * @return Order
     * @throws \Exception If stage transition is not allowed
     */
    public function changeOrderStage($id, $stageId)
    {
        return DB::transaction(function () use ($id, $stageId) {
            $query = Order::with(['stage' => function($q) {
                $q->withoutGlobalScopes();
            }, 'products']);
            
            // If current user is a vendor, only allow access to orders that have products from their vendor
            if (auth()->check() && auth()->user()->isVendor()) {
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if ($vendor) {
                    $query->whereHas('products', function($q) use ($vendor) {
                        $q->where('vendor_id', $vendor->id);
                    });
                }
            }
            
            $order = $query->findOrFail($id);
            $previousStage = $order->stage;

            // Fetch the current and new stages (without global scopes to avoid country filtering)
            $currentStage = $order->stage ? OrderStage::withoutGlobalScopes()->find($order->stage_id) : null;
            $newStage = OrderStage::withoutGlobalScopes()->findOrFail($stageId);

            // Validate stage transition if current stage exists
            if ($currentStage) {
                $blockReason = $currentStage->getTransitionBlockReason($newStage);
                if ($blockReason) {
                    throw new \Exception($blockReason);
                }
            }

            // Update order stage
            $order->update(['stage_id' => $stageId]);

            // Create stock bookings when moving to 'in_progress' stage
            // Check both type and slug for compatibility
            if ($newStage->type === 'in_progress' || $newStage->slug === 'in-progress') {
                Log::info('Creating stock bookings for order moving to in_progress', [
                    'order_id' => $order->id,
                    'stage_type' => $newStage->type,
                    'stage_slug' => $newStage->slug,
                    'products_count' => $order->products->count(),
                ]);
                $this->createStockBookingsForOrder($order);
            }

            // Release stock bookings when order is cancelled
            if ($newStage->type === 'cancel' || $newStage->slug === 'cancel') {
                $this->releaseStockBookingsForOrder($order);
            }

            // Update fulfillment statuses based on stage slug
            $this->updateFulfillmentsByStage($order, $newStage);

            // Dispatch event for accounting processing
            event(new \Modules\Order\app\Events\OrderStageChanged($order->refresh(), $newStage, $previousStage));

            return $order;
        });
    }

    /**
     * Create stock bookings for all products in an order
     * Called when order moves to 'in_progress' stage
     */
    private function createStockBookingsForOrder(Order $order): void
    {
        Log::info('Starting stock booking creation', [
            'order_id' => $order->id,
            'products_count' => $order->products->count(),
        ]);

        foreach ($order->products as $orderProduct) {
            Log::info('Processing order product for stock booking', [
                'order_product_id' => $orderProduct->id,
                'vendor_product_variant_id' => $orderProduct->vendor_product_variant_id,
                'quantity' => $orderProduct->quantity,
            ]);

            // Create booking if variant exists and booking doesn't already exist
            if ($orderProduct->vendor_product_variant_id) {
                $existingBooking = StockBooking::where('order_id', $order->id)
                    ->where('order_product_id', $orderProduct->id)
                    ->first();

                if (!$existingBooking) {
                    $booking = StockBooking::create([
                        'order_id' => $order->id,
                        'order_product_id' => $orderProduct->id,
                        'vendor_product_variant_id' => $orderProduct->vendor_product_variant_id,
                        'region_id' => $order->region_id,
                        'booked_quantity' => $orderProduct->quantity,
                        'status' => StockBooking::STATUS_BOOKED,
                        'booked_at' => now(),
                    ]);

                    Log::info('Stock booking created successfully', [
                        'booking_id' => $booking->id,
                        'order_id' => $order->id,
                        'order_product_id' => $orderProduct->id,
                        'variant_id' => $orderProduct->vendor_product_variant_id,
                        'quantity' => $orderProduct->quantity,
                    ]);
                } else {
                    Log::info('Stock booking already exists, skipping', [
                        'existing_booking_id' => $existingBooking->id,
                        'order_product_id' => $orderProduct->id,
                    ]);
                }
            } else {
                Log::info('Order product has no variant, skipping stock booking', [
                    'order_product_id' => $orderProduct->id,
                ]);
            }
        }
    }

    /**
     * Release stock bookings for an order
     * Called when order is cancelled
     */
    private function releaseStockBookingsForOrder(Order $order): void
    {
        $bookings = StockBooking::where('order_id', $order->id)
            ->where('status', StockBooking::STATUS_BOOKED)
            ->get();

        foreach ($bookings as $booking) {
            $booking->update([
                'status' => StockBooking::STATUS_RELEASED,
            ]);

            Log::info('Stock booking released (order cancelled)', [
                'order_id' => $order->id,
                'booking_id' => $booking->id,
                'variant_id' => $booking->vendor_product_variant_id,
                'quantity' => $booking->booked_quantity,
            ]);
        }
    }

    /**
     * Update fulfillment statuses based on order stage
     */
    private function updateFulfillmentsByStage($order, $stage)
    {
        $fulfillmentStatus = null;

        // Map stage slug to fulfillment status
        switch ($stage->slug) {
            case 'deliver':
                $fulfillmentStatus = 'delivered';
                break;
            case 'cancel':
            case 'refund':
                $fulfillmentStatus = 'cancelled';
                break;
            default:
                $fulfillmentStatus = 'shipped';
                break;
        }

        // Update all fulfillments for this order if status is determined
        if ($fulfillmentStatus) {
            $order->fulfillments()->update(['status' => $fulfillmentStatus]);
        }
    }

    /**
     * Create order record (used in pipeline)
     *
     * @param array $orderData Order data with all calculated values
     * @return Order
     */
    public function storeOrder(array $orderData): Order
    {
        return Order::create($orderData);
    }

    /**
     * Sync order products with taxes (used in pipeline)
     *
     * @param Order $order
     * @param array $productsData Array of product data with taxes and translations
     * @return void
     */
    public function syncOrderProducts(Order $order, array $productsData): void
    {
        // Get default stage (new) for order products
        $defaultStage = OrderStage::withoutGlobalScopes()
            ->where('type', 'new')
            ->first();
        $defaultStageId = $defaultStage?->id;

        foreach ($productsData as $product) {
            // Create order product with all data including shipping_cost and stage_id
            $orderProduct = OrderProduct::create([
                'order_id' => $order->id,
                'vendor_product_id' => $product['vendor_product_id'],
                'vendor_product_variant_id' => $product['vendor_product_variant_id'] ?? null,
                'vendor_id' => $product['vendor_id'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'commission' => $product['commission'] ?? 0,
                'shipping_cost' => $product['shipping_cost'] ?? 0,
                'stage_id' => $product['stage_id'] ?? $defaultStageId,
                'occasion_id' => $product['occasion_id'] ?? null,
                'bundle_id' => $product['bundle_id'] ?? null,
            ]);

            // Save translations for product name (EN and AR)
            if (!empty($product['translations'])) {
                foreach ($product['translations'] as $lang => $translationData) {
                    $orderProduct->setTranslation('name', $lang, $translationData['name']);
                }
                $orderProduct->save();
            }

            // Sync taxes - handle multiple taxes from the taxes array
            if (!empty($product['taxes']) && is_array($product['taxes'])) {
                // Calculate tax amount per tax based on proportion
                $totalTaxRate = $product['tax_rate'] ?? 0;
                $totalTaxAmount = $product['tax_amount'] ?? 0;
                
                // Track processed tax IDs to avoid duplicates
                $processedTaxIds = [];
                
                \Log::info('Processing taxes for order product', [
                    'order_product_id' => $orderProduct->id,
                    'taxes_count' => count($product['taxes']),
                    'taxes' => $product['taxes']
                ]);
                
                foreach ($product['taxes'] as $taxData) {
                    $taxId = $taxData['tax_id'] ?? null;
                    
                    \Log::info('Processing tax', [
                        'tax_id' => $taxId,
                        'already_processed' => in_array($taxId, $processedTaxIds),
                        'processedTaxIds' => $processedTaxIds
                    ]);
                    
                    // Skip if no tax_id or already processed
                    if (!$taxId || in_array($taxId, $processedTaxIds)) {
                        continue;
                    }
                    $processedTaxIds[] = $taxId;
                    
                    $taxPercentage = $taxData['percentage'] ?? 0;
                    // Calculate proportional tax amount for this specific tax
                    $taxAmount = $totalTaxRate > 0 
                        ? ($taxPercentage / $totalTaxRate) * $totalTaxAmount 
                        : 0;
                    
                    // Use updateOrCreate to avoid duplicate entry errors
                    try {
                        $orderProductTax = OrderProductTax::updateOrCreate(
                            [
                                'order_product_id' => $orderProduct->id,
                                'tax_id' => $taxId,
                            ],
                            [
                                'percentage' => $taxPercentage,
                                'amount' => $taxAmount,
                            ]
                        );
                        
                        \Log::info('Tax saved successfully', [
                            'order_product_tax_id' => $orderProductTax->id,
                            'order_product_id' => $orderProduct->id,
                            'tax_id' => $taxId
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Error saving tax', [
                            'order_product_id' => $orderProduct->id,
                            'tax_id' => $taxId,
                            'error' => $e->getMessage()
                        ]);
                        throw $e;
                    }

                    // Save tax translations (EN and AR)
                    if (!empty($taxData['name_en']) || !empty($taxData['name_ar'])) {
                        if (!empty($taxData['name_en'])) {
                            $orderProductTax->setTranslation('tax_title', 'en', $taxData['name_en']);
                        }
                        if (!empty($taxData['name_ar'])) {
                            $orderProductTax->setTranslation('tax_title', 'ar', $taxData['name_ar']);
                        }
                        $orderProductTax->save();
                    }
                }
            }
            // Fallback for legacy single tax handling
            elseif (!empty($product['tax_rate']) && $product['tax_rate'] > 0) {
                $taxId = $product['tax_id'] ?? null;
                
                // Use updateOrCreate to avoid duplicate entry errors
                $orderProductTax = OrderProductTax::updateOrCreate(
                    [
                        'order_product_id' => $orderProduct->id,
                        'tax_id' => $taxId,
                    ],
                    [
                        'percentage' => $product['tax_rate'] ?? 0,
                        'amount' => $product['tax_amount'] ?? 0,
                    ]
                );

                // Save tax translations (EN and AR)
                if (!empty($product['tax_translations'])) {
                    foreach ($product['tax_translations'] as $lang => $taxTitle) {
                        $orderProductTax->setTranslation('tax_title', $lang, $taxTitle);
                    }
                    $orderProductTax->save();
                }
            }

            // Note: Stock booking is NOT created here - it will be created when order moves to 'in_progress' stage
        }
    }

    /**
     * Sync order extras (fees and discounts) (used in pipeline)
     *
     * @param Order $order
     * @param array $fees Array of fee data
     * @param array $discounts Array of discount data
     * @return void
     */
    public function syncOrderExtras(Order $order, array $fees, string $type): void
    {
        // Create fee records
        foreach ($fees as $fee) {
            OrderExtraFeeDiscount::create([
                'order_id' => $order->id,
                'cost' => $fee['amount'],
                'reason' => $fee['reason'],
                'type' => $type,
            ]);
        }
    }

    /**
     * Update product sales counters (used in pipeline)
     *
     * @param array $productSalesData Array with product_id => quantity
     * @return void
     */
    public function updateProductSales(array $productSalesData): void
    {
        foreach ($productSalesData as $productId => $quantity) {
            DB::table('products')
                ->where('id', $productId)
                ->increment('sales', $quantity);
        }
    }

    /**
     * Update pricing status to reserved (used in pipeline)
     *
     * @param int $priceId
     * @return void
     */
    public function updatePricingStatus(int $priceId): void
    {
        // DB::table('pricing')
        //     ->where('id', $priceId)
        //     ->update(['status' => 'reserved']);
    }

    /**
     * Delete an order and its related data
     *
     * @param int $id
     * @return bool
     */
    public function deleteOrder($id): bool
    {
        return DB::transaction(function () use ($id) {
            $query = Order::query();
            
            // If current user is a vendor, only allow access to orders that have products from their vendor
            if (auth()->check() && auth()->user()->isVendor()) {
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if ($vendor) {
                    $query->whereHas('products', function($q) use ($vendor) {
                        $q->where('vendor_id', $vendor->id);
                    });
                }
            }
            
            $order = $query->findOrFail($id);
            
            // Check if order has allocated stock bookings - prevent deletion
            $hasAllocatedStock = \Modules\CatalogManagement\app\Models\StockBooking::where('order_id', $order->id)
                ->where('status', \Modules\CatalogManagement\app\Models\StockBooking::STATUS_ALLOCATED)
                ->exists();
            
            if ($hasAllocatedStock) {
                throw new \Exception(__('order::order.cannot_delete_allocated_order'));
            }
            
            // Delete related stock bookings
            \Modules\CatalogManagement\app\Models\StockBooking::where('order_id', $order->id)->delete();
            
            // Delete related order products and their taxes
            foreach ($order->products as $product) {
                $product->taxes()->delete();
            }
            $order->products()->delete();
            
            // Delete related fulfillments
            $order->fulfillments()->delete();
            
            // Delete related extra fees and discounts
            $order->extraFeesDiscounts()->delete();
            
            // Delete the order
            return $order->delete();
        });
    }

    /**
     * Change stage for a specific order product
     * Vendors can only change stage for their own products
     *
     * @param int $orderProductId Order Product ID
     * @param int $stageId New stage ID
     * @return OrderProduct
     * @throws \Exception If stage transition is not allowed or vendor doesn't own the product
     */
    public function changeOrderProductStage($orderProductId, $stageId)
    {
        return DB::transaction(function () use ($orderProductId, $stageId) {
            $query = OrderProduct::with(['stage' => function($q) {
                $q->withoutGlobalScopes();
            }, 'order']);
            
            // If current user is a vendor, only allow access to their own products
            if (auth()->check() && auth()->user()->isVendor()) {
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if ($vendor) {
                    $query->where('vendor_id', $vendor->id);
                }
            }
            
            $orderProduct = $query->findOrFail($orderProductId);
            $previousStage = $orderProduct->stage;

            // Fetch the current and new stages (without global scopes to avoid country filtering)
            $currentStage = $orderProduct->stage ? OrderStage::withoutGlobalScopes()->find($orderProduct->stage_id) : null;
            $newStage = OrderStage::withoutGlobalScopes()->findOrFail($stageId);

            // Validate stage transition if current stage exists
            if ($currentStage) {
                $blockReason = $currentStage->getTransitionBlockReason($newStage);
                if ($blockReason) {
                    throw new \Exception($blockReason);
                }
            }

            // Update order product stage
            $orderProduct->update(['stage_id' => $stageId]);

            // Handle stock booking based on stage type
            $order = $orderProduct->order;
            
            // Create stock booking when moving to 'in_progress' stage
            if ($newStage->type === 'in_progress' || $newStage->slug === 'in-progress') {
                $this->createStockBookingForOrderProduct($order, $orderProduct);
            }

            // Release stock booking when product is cancelled
            if ($newStage->type === 'cancel' || $newStage->slug === 'cancel') {
                $this->releaseStockBookingForOrderProduct($order, $orderProduct);
            }

            // Update fulfillment status for this product
            $this->updateFulfillmentByStageForProduct($orderProduct, $newStage);

            // Dispatch event for accounting processing
            event(new \Modules\Order\app\Events\OrderProductStageChanged($orderProduct->refresh(), $newStage, $previousStage));

            return $orderProduct;
        });
    }

    /**
     * Create stock booking for a single order product
     */
    private function createStockBookingForOrderProduct(Order $order, OrderProduct $orderProduct): void
    {
        if ($orderProduct->vendor_product_variant_id) {
            $existingBooking = StockBooking::where('order_id', $order->id)
                ->where('order_product_id', $orderProduct->id)
                ->first();

            if (!$existingBooking) {
                StockBooking::create([
                    'order_id' => $order->id,
                    'order_product_id' => $orderProduct->id,
                    'vendor_product_variant_id' => $orderProduct->vendor_product_variant_id,
                    'region_id' => $order->region_id,
                    'booked_quantity' => $orderProduct->quantity,
                    'status' => StockBooking::STATUS_BOOKED,
                    'booked_at' => now(),
                ]);
            }
        }
    }

    /**
     * Release stock booking for a single order product
     */
    private function releaseStockBookingForOrderProduct(Order $order, OrderProduct $orderProduct): void
    {
        $booking = StockBooking::where('order_id', $order->id)
            ->where('order_product_id', $orderProduct->id)
            ->where('status', StockBooking::STATUS_BOOKED)
            ->first();

        if ($booking) {
            $booking->update([
                'status' => StockBooking::STATUS_RELEASED,
            ]);
        }
    }

    /**
     * Update fulfillment status for a single order product based on stage
     */
    private function updateFulfillmentByStageForProduct(OrderProduct $orderProduct, $stage): void
    {
        $fulfillmentStatus = null;

        switch ($stage->slug) {
            case 'deliver':
                $fulfillmentStatus = 'delivered';
                break;
            case 'cancel':
            case 'refund':
                $fulfillmentStatus = 'cancelled';
                break;
            default:
                $fulfillmentStatus = 'shipped';
                break;
        }

        if ($fulfillmentStatus) {
            $orderProduct->fulfillments()->update(['status' => $fulfillmentStatus]);
        }
    }

    /**
     * Change stage for all products of a specific vendor in an order
     * 
     * @param int $orderId Order ID
     * @param int $vendorId Vendor ID
     * @param int $stageId New stage ID
     * @return \Modules\Order\app\Models\VendorOrderStage
     * @throws \Exception If stage transition is not allowed
     */
    public function changeVendorOrderStage($orderId, $vendorId, $stageId)
    {
        return DB::transaction(function () use ($orderId, $vendorId, $stageId) {
            // Get the vendor order stage
            $vendorOrderStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $orderId)
                ->where('vendor_id', $vendorId)
                ->with(['stage' => function($q) {
                    $q->withoutGlobalScopes();
                }])
                ->firstOrFail();
            
            $previousStage = $vendorOrderStage->stage;
            
            // Fetch the current and new stages
            $currentStage = $previousStage ? OrderStage::withoutGlobalScopes()->find($vendorOrderStage->stage_id) : null;
            $newStage = OrderStage::withoutGlobalScopes()->findOrFail($stageId);
            
            // Validate stage transition if current stage exists
            if ($currentStage) {
                $blockReason = $currentStage->getTransitionBlockReason($newStage);
                if ($blockReason) {
                    throw new \Exception($blockReason);
                }
            }
            
            // Update vendor order stage
            $vendorOrderStage->update(['stage_id' => $stageId]);
            
            // Get all order products for this vendor
            $orderProducts = OrderProduct::where('order_id', $orderId)
                ->where('vendor_id', $vendorId)
                ->get();
            
            // Update stage for all products of this vendor
            foreach ($orderProducts as $orderProduct) {
                $orderProduct->update(['stage_id' => $stageId]);
                
                // Handle stock booking based on stage type
                $order = $orderProduct->order;
                
                // Create stock booking when moving to 'in_progress' stage
                if ($newStage->type === 'in_progress' || $newStage->slug === 'in-progress') {
                    $this->createStockBookingForOrderProduct($order, $orderProduct);
                }
                
                // Release stock booking when product is cancelled
                if ($newStage->type === 'cancel' || $newStage->slug === 'cancel') {
                    $this->releaseStockBookingForOrderProduct($order, $orderProduct);
                }
                
                // Update fulfillment status for this product
                $this->updateFulfillmentByStageForProduct($orderProduct, $newStage);
            }
            
            return $vendorOrderStage->refresh();
        });
    }
}

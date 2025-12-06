<?php

namespace Modules\Order\app\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Order\app\Interfaces\OrderRepositoryInterface;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderFulfillment;
use Modules\Order\app\Models\OrderExtraFeeDiscount;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Create a new order
     */
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Implementation here
        });
    }

    /**
     * Get all orders with filtering
     */
    public function getAllOrders(array $filters)
    {
        $query = Order::query();

        $query->with(['stage', 'customer', 'products']);

        return $query;
    }

    /**
     * Get order by ID
     */
    public function getOrderById($id)
    {
        return Order::with(['stage', 'customer', 'products', 'extraFeesDiscounts'])
            ->findOrFail($id);
    }

    /**
     * Update order
     */
    public function updateOrder($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            // Implementation here
        });
    }

    /**
     * Delete order
     */
    public function deleteOrder($id)
    {
        return DB::transaction(function () use ($id) {
            // Implementation here
        });
    }

    /**
     * Get order with products
     */
    public function getOrderWithProducts($id)
    {
        // Implementation here
    }

    /**
     * Add product to order
     */
    public function addProductToOrder($orderId, array $productData)
    {
        return DB::transaction(function () use ($orderId, $productData) {
            // Implementation here
        });
    }

    /**
     * Remove product from order
     */
    public function removeProductFromOrder($orderId, $orderProductId)
    {
        return DB::transaction(function () use ($orderId, $orderProductId) {
            // Implementation here
        });
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($id, $stageId)
    {
        return DB::transaction(function () use ($id, $stageId) {
            // Implementation here
        });
    }

    /**
     * Add extra fee or discount
     */
    public function addExtraFeeDiscount($orderId, array $data)
    {
        return DB::transaction(function () use ($orderId, $data) {
            // Implementation here
        });
    }

    /**
     * Get order fulfillments
     */
    public function getOrderFulfillments($orderId)
    {
        // Implementation here
    }

    /**
     * Create fulfillment
     */
    public function createFulfillment($orderId, array $data)
    {
        return DB::transaction(function () use ($orderId, $data) {
            // Implementation here
        });
    }

    /**
     * Get datatable data
     */
    public function getDatatableData(array $filters)
    {
        // Implementation here
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
        foreach ($productsData as $product) {
            // Create order product with all data
            $orderProduct = OrderProduct::create([
                'order_id' => $order->id,
                'vendor_product_id' => $product['vendor_product_id'],
                'vendor_product_variant_id' => $product['vendor_product_variant_id'] ?? null,
                'vendor_id' => $product['vendor_id'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'commission' => $product['commission'] ?? 0,
            ]);

            // Save translations for product name (EN and AR)
            if (!empty($product['translations'])) {
                foreach ($product['translations'] as $lang => $translationData) {
                    $orderProduct->setTranslation('name', $lang, $translationData['name']);
                }
                $orderProduct->save();
            }

            // Sync taxes with translations if present
            if (!empty($product['tax_id'])) {
                $orderProductTax = $orderProduct->taxes()->create([
                    'tax_id' => $product['tax_id'],
                    'percentage' => $product['tax_rate'] ?? 0,
                ]);

                // Save tax translations (EN and AR)
                if (!empty($product['tax_translations'])) {
                    foreach ($product['tax_translations'] as $lang => $taxTitle) {
                        $orderProductTax->setTranslation('tax_title', $lang, $taxTitle);
                    }
                    $orderProductTax->save();
                }
            }
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
        DB::table('pricing')
            ->where('id', $priceId)
            ->update(['status' => 'reserved']);
    }
}

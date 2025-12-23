<?php

namespace Modules\Order\app\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Order\app\Interfaces\OrderRepositoryInterface;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderFulfillment;
use Modules\Order\app\Models\OrderExtraFeeDiscount;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Get all orders with filtering
     */
    public function getAllOrders(array $filters)
    {
        $query = Order::query();

        $query->with(['stage', 'customer', 'products'])->filter($filters)->latest('created_at');
        
        // Filter by vendor if user is not admin
        if (!isAdmin()) {
            $vendorId = auth()->user()->vendor_id ?? auth()->id();
            $query->whereHas('products', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        }
        
        return $query;
    }

    /**
     * Get order by ID
     */
    public function getOrderById($id)
    {
        return Order::with([
            'stage', 
            'customer', 
            'products.vendorProduct.product.category',
            'products.vendorProduct.product.mainImage',
            'products.vendorProduct.vendor',
            'products.vendorProductVariant.variantConfiguration.key', 
            'products.taxes',
            'extraFeesDiscounts'
        ])->findOrFail($id);
    }

    /**
     * Change order stage and update fulfillments
     */
    public function changeOrderStage($id, $stageId)
    {
        return DB::transaction(function () use ($id, $stageId) {
            $order = Order::with('stage')->findOrFail($id);

            // Fetch the new stage
            $newStage = OrderStage::findOrFail($stageId);

            // Update order stage
            $order->update(['stage_id' => $stageId]);

            // Update fulfillment statuses based on stage slug
            $this->updateFulfillmentsByStage($order, $newStage);

            return $order->refresh();
        });
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
                    'amount' => $product["tax_amount"] ?? 0,
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
            $order = Order::findOrFail($id);
            
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
}

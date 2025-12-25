<?php

namespace Modules\Order\app\Repositories;

use Modules\Order\app\Interfaces\OrderFulfillmentRepositoryInterface;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderFulfillment;
use Modules\Order\app\Models\OrderProduct;
use Modules\AreaSettings\app\Models\Region;
use Modules\CatalogManagement\app\Models\StockBooking;
use Illuminate\Support\Facades\DB;

class OrderFulfillmentRepository implements OrderFulfillmentRepositoryInterface
{
    /**
     * Get fulfillments for an order
     */
    public function getByOrder($orderId)
    {
        return OrderFulfillment::where('order_id', $orderId)->get();
    }

    /**
     * Get fulfillments with order products and regions
     */
    public function getByOrderWithRelations($orderId)
    {
        return OrderFulfillment::where('order_id', $orderId)
            ->with(['orderProduct', 'region'])
            ->get()
            ->groupBy('order_product_id');
    }

    /**
     * Create or update fulfillment
     */
    public function createOrUpdate($orderId, $orderProductId, $regionId, $quantity)
    {
        if ($quantity > 0) {
            return OrderFulfillment::updateOrCreate(
                [
                    'order_product_id' => $orderProductId,
                    'region_id' => $regionId,
                ],
                [
                    'order_id' => $orderId,
                    'allocated_quantity' => $quantity,
                ]
            );
        } else {
            // Delete if quantity is 0
            OrderFulfillment::where('order_product_id', $orderProductId)
                ->where('region_id', $regionId)
                ->delete();
        }
    }

    /**
     * Delete fulfillment
     */
    public function delete($orderProductId, $regionId)
    {
        return OrderFulfillment::where('order_product_id', $orderProductId)
            ->where('region_id', $regionId)
            ->delete();
    }

    /**
     * Get stock data for order
     */
    public function getStockDataForOrder($orderId)
    {
        $order = Order::with([
            'products.vendorProduct.product',
            'products.vendorProduct.vendor',
            'products.vendorProductVariant.variantConfiguration.key'
        ])->findOrFail($orderId);

        $regions = Region::with('stocks')->get();
        $existingFulfillments = $this->getByOrderWithRelations($orderId);

        $stockData = [];

        foreach ($order->products as $orderProduct) {
            $vendorProductVariant = $orderProduct->vendorProductVariant;

            $stockData[$orderProduct->id] = [
                'order_product' => $orderProduct,
                'vendor_product_variant' => $vendorProductVariant,
                'regions' => []
            ];

            foreach ($regions as $region) {
                // Calculate allocated from OrderFulfillment
                $allocatedQuantity = $existingFulfillments->get($orderProduct->id, collect())
                    ->where('region_id', $region->id)
                    ->sum('allocated_quantity');

                // Get available stock (Total Stock - Allocated Stock) from Region model
                $availableStock = $region->getAvailableStockForVariant($orderProduct->vendor_product_variant_id);

                $stockData[$orderProduct->id]['regions'][$region->id] = [
                    'region' => $region,
                    'available_stock' => $availableStock,
                    'allocated_quantity' => $allocatedQuantity,
                    'remaining_stock' => $availableStock - $allocatedQuantity
                ];
            }
        }

        return [
            'order' => $order,
            'stockData' => $stockData,
            'regions' => $regions,
        ];
    }

    /**
     * Save allocations
     */
    public function saveAllocations($orderId, $allocations)
    {
        DB::beginTransaction();
        try {
            foreach ($allocations as $allocation) {
                $this->createOrUpdate(
                    $orderId,
                    $allocation['order_product_id'],
                    $allocation['region_id'],
                    $allocation['quantity']
                );
            }

            // Validate allocations
            $this->validateAllocations($orderId);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate allocations
     */
    public function validateAllocations($orderId)
    {
        $order = Order::with('products')->findOrFail($orderId);

        foreach ($order->products as $orderProduct) {
            $totalAllocated = OrderFulfillment::where('order_product_id', $orderProduct->id)
                ->sum('allocated_quantity');

            if ($totalAllocated != $orderProduct->quantity) {
                throw new \Exception(
                    "Total allocated quantity ({$totalAllocated}) for product '{$orderProduct->product_title}' must equal ordered quantity ({$orderProduct->quantity})"
                );
            }
        }
    }

    /**
     * Update stock regions after fulfillment
     */
    public function updateStockRegions($orderId)
    {
        $fulfillments = OrderFulfillment::where('order_id', $orderId)->get();

        foreach ($fulfillments as $fulfillment) {
            // Update stock booking with allocated region and status (don't decrement actual stock)
            $stockBooking = StockBooking::where('order_id', $orderId)
                ->where('order_product_id', $fulfillment->order_product_id)
                ->first();

            if ($stockBooking) {
                $stockBooking->update([
                    'allocated_region_id' => $fulfillment->region_id,
                    'status' => StockBooking::STATUS_ALLOCATED,
                    'allocated_at' => now(),
                ]);
            }
        }
    }
}

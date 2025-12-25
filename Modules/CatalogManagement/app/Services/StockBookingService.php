<?php

namespace Modules\CatalogManagement\app\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Models\StockBooking;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;

class StockBookingService
{
    /**
     * Book stock when order is created
     */
    public function bookOrderStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->orderProducts as $orderProduct) {
                $this->bookProductStock($order, $orderProduct);
            }
        });
    }

    /**
     * Book stock for a single order product
     */
    public function bookProductStock(Order $order, OrderProduct $orderProduct): void
    {
        $variantId = $orderProduct->vendor_product_variant_id;
        $quantity = $orderProduct->quantity;
        $regionId = $order->region_id;

        // Check if stock is available
        $variant = VendorProductVariant::find($variantId);
        if (!$variant) {
            Log::warning('Variant not found for stock booking', [
                'order_id' => $order->id,
                'variant_id' => $variantId
            ]);
            return;
        }

        // Create stock booking
        StockBooking::bookStock(
            $order->id,
            $orderProduct->id,
            $variantId,
            $regionId,
            $quantity
        );

        Log::info('Stock booked for order', [
            'order_id' => $order->id,
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'region_id' => $regionId
        ]);
    }

    /**
     * Release booked stock when order is canceled
     */
    public function releaseOrderStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $bookings = StockBooking::where('order_id', $order->id)
                ->where('status', StockBooking::STATUS_BOOKED)
                ->get();

            foreach ($bookings as $booking) {
                $booking->release();
            }

            Log::info('Stock released for canceled order', [
                'order_id' => $order->id,
                'bookings_released' => $bookings->count()
            ]);
        });
    }

    /**
     * Fulfill booked stock when order is delivered
     * This also decreases the actual stock quantity
     */
    public function fulfillOrderStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $bookings = StockBooking::where('order_id', $order->id)
                ->where('status', StockBooking::STATUS_BOOKED)
                ->get();

            foreach ($bookings as $booking) {
                // Decrease actual stock
                $this->decreaseStock(
                    $booking->vendor_product_variant_id,
                    $booking->region_id,
                    $booking->booked_quantity
                );

                // Mark booking as fulfilled
                $booking->fulfill();
            }

            Log::info('Stock fulfilled for delivered order', [
                'order_id' => $order->id,
                'bookings_fulfilled' => $bookings->count()
            ]);
        });
    }

    /**
     * Decrease actual stock quantity
     */
    protected function decreaseStock(int $variantId, int $regionId, int $quantity): void
    {
        $stock = VendorProductVariantStock::where('vendor_product_variant_id', $variantId)
            ->where('region_id', $regionId)
            ->first();

        if ($stock) {
            $newQuantity = max(0, $stock->quantity - $quantity);
            $stock->update(['quantity' => $newQuantity]);

            Log::info('Stock decreased', [
                'variant_id' => $variantId,
                'region_id' => $regionId,
                'decreased_by' => $quantity,
                'new_quantity' => $newQuantity
            ]);
        }
    }

    /**
     * Check if stock is available for booking
     */
    public function isStockAvailable(int $variantId, int $regionId, int $quantity): bool
    {
        $variant = VendorProductVariant::find($variantId);
        if (!$variant) {
            return false;
        }

        return $variant->remaining_stock >= $quantity;
    }

    /**
     * Get stock summary for a variant
     */
    public function getStockSummary(int $variantId): array
    {
        $variant = VendorProductVariant::with(['stocks', 'activeBookings'])->find($variantId);
        
        if (!$variant) {
            return [
                'total_stock' => 0,
                'booked_stock' => 0,
                'allocated_stock' => 0,
                'fulfilled_stock' => 0,
                'remaining_stock' => 0,
            ];
        }

        return [
            'total_stock' => $variant->total_stock,
            'booked_stock' => $variant->booked_stock,
            'allocated_stock' => $variant->allocated_stock,
            'fulfilled_stock' => $variant->fulfilled_stock,
            'remaining_stock' => $variant->remaining_stock,
        ];
    }
}

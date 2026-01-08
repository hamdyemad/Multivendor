<?php

namespace Modules\Order\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use App\Exceptions\OrderException;
use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\Models\Promocode;
use Modules\Order\app\Actions\OrderQueryAction;
use Modules\Order\app\Interfaces\Api\OrderApiRepositoryInterface;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;
use Modules\Order\app\Models\VendorOrderStage;

class OrderApiRepository implements OrderApiRepositoryInterface
{

    public function __construct(
        private OrderQueryAction $query,
        private IsPaginatedAction $paginated
    ) {}

    /**
     * Get customer's orders with filtering
     */
    public function getCustomerOrders(array $filters)
    {
        $query = $this->query->handle($filters);

        return $this->paginated->handle($query, $filters["per_page"] ?? null, $filters["paginated"] ?? null);
    }

    /**
     * Get specific order for customer
     */
    public function getCustomerOrderById(int $customerId, int $orderId)
    {
        return Order::where('customer_id', $customerId)
            ->where('id', $orderId)
            ->with([
                'stage',
                'products',
                'products.vendorProduct',
                'products.vendorProduct.product',
                'products.vendorProductVariant',
                'products.taxes',
                'products.stage' => function($q) {
                    $q->withoutGlobalScopes();
                },
                'extraFeesDiscounts',
                'country',
                'city',
                'region',
                'payments'
            ])
            ->firstOrFail();
    }

    public function changeOrderStage(int $customerId, int $orderId, int $stageId, $allowedStage)
    {
        return DB::transaction(function () use ($customerId, $orderId, $stageId, $allowedStage) {
            $order = Order::where('customer_id', $customerId)
                ->where('id', $orderId)
                ->firstOrFail();

            if ($order->stage_id !== $allowedStage) {
                throw new OrderException('order.cannot_change_stage');
            }

            $order->update(['stage_id' => $stageId]);

            return $order;
        });
    }

    /**
     * Cancel order - only if ALL vendors have 'new' stage
     */
    public function cancelOrder(int $customerId, int $orderId)
    {
        return DB::transaction(function () use ($customerId, $orderId) {
            $order = Order::where('customer_id', $customerId)
                ->where('id', $orderId)
                ->firstOrFail();

            // Get the 'new' and 'cancel' stage IDs
            $newStage = OrderStage::withoutGlobalScopes()->where('type', 'new')->first();
            $cancelStage = OrderStage::withoutGlobalScopes()->where('type', 'cancel')->first();

            if (!$newStage || !$cancelStage) {
                throw new OrderException('order.stages_not_configured', trans('order::order.stages_not_configured'));
            }

            // Get all vendor stages for this order
            $vendorStages = VendorOrderStage::where('order_id', $orderId)->get();

            if ($vendorStages->isEmpty()) {
                throw new OrderException('order.no_vendor_stages_found', trans('order::order.no_vendor_stages_found'));
            }

            // Check if ALL vendors have 'new' stage
            $allVendorsNew = $vendorStages->every(function ($vendorStage) use ($newStage) {
                return $vendorStage->stage_id === $newStage->id;
            });

            if (!$allVendorsNew) {
                throw new OrderException('order.cannot_cancel_order_not_all_new', trans('order::order.cannot_cancel_order_not_all_new'));
            }

            // Update all vendor stages to 'cancel'
            VendorOrderStage::where('order_id', $orderId)
                ->update(['stage_id' => $cancelStage->id]);

            // Update main order stage to 'cancel'
            $order->update(['stage_id' => $cancelStage->id]);

            return $order->fresh();
        });
    }

    /**
     * Refund order - only for vendors with 'deliver' stage
     */
    public function refundOrder(int $customerId, int $orderId)
    {
        return DB::transaction(function () use ($customerId, $orderId) {
            $order = Order::where('customer_id', $customerId)
                ->where('id', $orderId)
                ->firstOrFail();

            // Get the 'deliver' and 'refund' stage IDs
            $deliverStage = OrderStage::withoutGlobalScopes()->where('type', 'deliver')->first();
            $refundStage = OrderStage::withoutGlobalScopes()->where('type', 'refund')->first();

            if (!$deliverStage || !$refundStage) {
                throw new OrderException('order.stages_not_configured', trans('order::order.stages_not_configured'));
            }

            // Get all vendor stages for this order that have 'deliver' stage
            $deliveredVendorStages = VendorOrderStage::where('order_id', $orderId)
                ->where('stage_id', $deliverStage->id)
                ->get();

            if ($deliveredVendorStages->isEmpty()) {
                throw new OrderException('order.no_delivered_vendors_to_refund', trans('order::order.no_delivered_vendors_to_refund'));
            }

            // Update only delivered vendor stages to 'refund'
            VendorOrderStage::where('order_id', $orderId)
                ->where('stage_id', $deliverStage->id)
                ->update(['stage_id' => $refundStage->id]);

            // Check if all vendors are now refunded to update main order stage
            $allVendorStages = VendorOrderStage::where('order_id', $orderId)->get();
            $allRefunded = $allVendorStages->every(function ($vendorStage) use ($refundStage) {
                return $vendorStage->stage_id === $refundStage->id;
            });

            if ($allRefunded) {
                $order->update(['stage_id' => $refundStage->id]);
            }

            return $order->fresh();
        });
    }

    /**
     * Validate promo code for customer
     */
    public function validatePromoCode(string $code, ?int $customerId)
    {
        // Find active promo code
        $promoCode = Promocode::where('code', $code)->isValid()->first();

        if (!$promoCode) {
            return null;
        }

        $hasUsed = false;
        // Check if customer has already used this promo code
        if($customerId)
        {
            $hasUsed = Order::where('customer_id', $customerId)
                        ->where('customer_promo_code_title', $code)
                        ->exists();
            if ($hasUsed) {
                throw new OrderException('order.promo_code_already_used', trans('order::order.promo_code_already_used'));
            }
        }


        // Check if promo code has reached maximum usage
        $usageCount = Order::where('customer_promo_code_title', $code)->count();
        if ($usageCount >= $promoCode->maximum_of_use) {
            throw new OrderException('order.promo_code_limit_reached', trans('order::order.promo_code_limit_reached'));
        }

        return $promoCode;
    }
}

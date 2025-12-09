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
                'extraFeesDiscounts',
                'country',
                'city',
                'region'
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
                throw new OrderException('order.promo_code_already_used');
            }
        }


        // Check if promo code has reached maximum usage
        $usageCount = Order::where('customer_promo_code_title', $code)->count();
        if ($usageCount >= $promoCode->maximum_of_use) {
            throw new OrderException('order.promo_code_limit_reached');
        }

        return $promoCode;
    }
}

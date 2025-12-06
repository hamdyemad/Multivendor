<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Modules\Order\app\Interfaces\OrderRepositoryInterface;

class SyncExtras
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * Handle the pipeline.
     *
     * Syncs order extras (fees and discounts) to the database using repository.
     * This step creates OrderExtraFeeDiscount records for all fees and discounts.
     */
    public function handle($payload, Closure $next)
    {

        $data = $payload['data'];
        $context = $payload['context'];

        $order = $context['order'];
        $fees = $context['fees'];
        $discounts = $context['discounts'];

        $this->orderRepository->syncOrderExtras($order, $fees, 'fee');
        $this->orderRepository->syncOrderExtras($order, $discounts, 'discount');

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}

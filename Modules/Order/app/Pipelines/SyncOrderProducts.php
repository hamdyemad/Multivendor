<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Modules\Order\app\Interfaces\OrderRepositoryInterface;

class SyncOrderProducts
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}


    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        $order = $context['order'];
        $productsData = $context['products_data'];

        $this->orderRepository->syncOrderProducts($order, $productsData);

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}

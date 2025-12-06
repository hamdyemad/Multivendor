<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Modules\CatalogManagement\app\Services\Api\ProductApiService;

class UpdateProductSales
{
    public function __construct(
        private ProductApiService $productService,
    ) {}

    /**
     * Handle the pipeline.
     *
     * Updates product sales counters using repository.
     * This step increments the sales count for each product by the ordered quantity.
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];
        $productSalesData = $context['product_sales_to_update'];

        // Update product sales using repository
        foreach ($productSalesData as $product => $productSales) {
            $this->productService->incrementProductSales($product, $productSales);
        }

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}

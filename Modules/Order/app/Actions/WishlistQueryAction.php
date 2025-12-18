<?php

namespace Modules\Order\app\Actions;

use Modules\Order\app\Models\Wishlist;
use Modules\CatalogManagement\app\Models\VendorProduct;

class WishlistQueryAction
{
    /**
     * Build wishlist query for a customer with filters
     */
    public function handle($customerId, array $filters = [])
    {
        $query = Wishlist::query()
            ->byCustomer($customerId)
            ->with([
                'vendorProduct' => function ($q) {
                    $q->active()
                        ->status(VendorProduct::STATUS_APPROVED)
                        ->with([
                            'product' => function ($q) {
                                $q->with(['brand', 'attachments', 'translations']);
                            },
                            'variants',
                            'vendor',
                            'tax',
                        ])
                        ->withCount('reviews')
                        ->withAvg('reviews', 'star');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->filter($filters);

        return $query;
    }
}

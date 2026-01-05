<?php

namespace Modules\CatalogManagement\app\Repositories\Api;

use Modules\CatalogManagement\app\Models\Bundle;
use App\Models\Language;
use App\Models\Attachment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\CatalogManagement\app\Interfaces\Api\BundleRepositoryApiInterface;

class BundleApiRepository implements BundleRepositoryApiInterface
{
    protected $bundle;

    public function __construct(Bundle $bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * Get all bundles with filters
     */
    public function getAllBundles($filters = [], $perPage = 15)
    {
        $query = Bundle::with([
            'country', 
            'vendor', 
            'bundleCategory',
            'bundleProducts' => function ($q) {
                // Only include products where vendor product is active and product is approved
                $q->whereHas('vendorProductVariant', function ($vpvQuery) {
                    $vpvQuery->whereHas('vendorProduct', function ($vpQuery) {
                        $vpQuery->where('vendor_products.status', 'active');
                    })->whereHas('vendorProduct.product', function ($pQuery) {
                        $pQuery
                        ->where('vendor_products.status', 'approved')
                        ->where('is_active', '1');
                    });
                });
                $q->with([
                    'vendorProductVariant.vendorProduct',
                    'vendorProductVariant.variantConfiguration.parent_data.key',
                    'vendorProductVariant.variantConfiguration.key'
                ]);
            }
        ])->filter($filters)
        ->active() // active() now includes is_active = 1 AND admin_approval = 1
        ->withCount(['bundleProducts' => function ($q) {
            // Only count products where vendor product is active and product is approved
            $q->whereHas('vendorProductVariant', function ($vpvQuery) {
                $vpvQuery->whereHas('vendorProduct', function ($vpQuery) {
                    $vpQuery
                    ->where('vendor_products.status', 'approved')
                    ->where('is_active', '1');
                });
            });
        }])
        ->latest();
        return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get bundle by ID
     */
    public function getBundleById($id, array $filters = [])
    {
        return Bundle::with([
            'attachments',
            'country',
            'vendor',
            'bundleCategory',
            'bundleProducts' => function ($q) use ($filters) {
                // Only include products where vendor product is active and product is approved
                $q->whereHas('vendorProductVariant', function ($vpvQuery) {
                    $vpvQuery->whereHas('vendorProduct', function ($vpQuery) {
                        $vpQuery
                        ->where('vendor_products.status', 'approved')
                        ->where('is_active', '1');
                    });
                });
                
                // Apply search filter on bundle products via VendorProduct filter scope
                if (!empty($filters['search'])) {
                    $q->whereHas('vendorProductVariant.vendorProduct', function ($vpQuery) use ($filters) {
                        $vpQuery->filter($filters);
                    });
                }
                
                // Eager load relationships
                $q->with([
                    'vendorProductVariant.vendorProduct',
                    'vendorProductVariant.variantConfiguration.parent_data.key',
                    'vendorProductVariant.variantConfiguration.key'
                ]);
            },
        ])
        ->withCount(['bundleProducts' => function ($q) {
            // Only count products where vendor product is active and product is approved
            $q->whereHas('vendorProductVariant', function ($vpvQuery) {
                $vpvQuery->whereHas('vendorProduct', function ($vpQuery) {
                    $vpQuery
                        ->where('vendor_products.status', 'approved')
                        ->where('is_active', '1');
                });
            });
        }])
        ->where(function($q) use ($id) {
            $q->where('id', $id)->orWhere('slug', $id);
        })
        ->active() // active() now includes is_active = 1 AND admin_approval = 1
        ->firstOrFail();
    }

}

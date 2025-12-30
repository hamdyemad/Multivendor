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
        $query = Bundle::with('country', 'vendor', 'bundleCategory',
         'bundleProducts.vendorProductVariant.vendorProduct'
        )->filter($filters)
        ->active() // active() now includes is_active = 1 AND admin_approval = 1
        ->withCount('bundleProducts')
        ->latest();
        return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get bundle by ID
     */
    public function getBundleById($id)
    {
        return Bundle::with([
            'attachments',
            'country',
            'vendor',
            'bundleCategory',
            'bundleProducts.vendorProductVariant.vendorProduct',
        ])
        ->withCount('bundleProducts')
        ->where(function($q) use ($id) {
            $q->where('id', $id)->orWhere('slug', $id);
        })
        ->active() // active() now includes is_active = 1 AND admin_approval = 1
        ->firstOrFail();
    }

}

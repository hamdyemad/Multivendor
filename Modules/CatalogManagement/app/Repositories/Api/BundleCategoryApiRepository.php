<?php

namespace Modules\CatalogManagement\app\Repositories\Api;

use Modules\CatalogManagement\app\Models\BundleCategory;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\CatalogManagement\app\Interfaces\Api\BundleCategoryApiRepositoryInterface;

class BundleCategoryApiRepository implements BundleCategoryApiRepositoryInterface
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    public function getAll(array $filters = [], $per_page = 10)
    {
        // Temporarily disable cache to debug
        $query = BundleCategory::with(['translations'])
        ->withCount('bundles')
        ->active()
        ->filter($filters)
        ->orderBy('created_at', 'desc');
        return ($per_page == 0) ? $query->get() : $query->paginate($per_page);
    }


    /**
     * Get bundle category by ID
     */
    public function getBundleCategoryById($id)
    {
        // Temporarily disable cache to debug
        return BundleCategory::with(['translations', 'attachments', 'bundles' => function($q) {
            $q->active()
              ->withCount(['bundleProducts' => function($q) {
                  $q->whereHas('vendorProductVariant.vendorProduct', function($q) {
                      $q->where('is_active', true)
                        ->where('status', 'approved');
                  });
              }])
              ->with([
                  'bundleProducts' => function($q) {
                      $q->whereHas('vendorProductVariant.vendorProduct', function($q) {
                          $q->where('is_active', true)
                            ->where('status', 'approved');
                      });
                  },
                  'bundleProducts.vendorProductVariant',
                  'vendor',
                  'bundleCategory' => function($q) {
                      $q->withCount('bundles');
                  }
              ]);
        }])
        ->withCount('bundles')
        ->where('slug', $id)
        ->orWhere('id', $id)->firstOrFail();
    }

    /**
     * Clear bundle category API cache
     */
    public function clearCache(): void
    {
        $this->cache->forgetByPattern('bundlecategoryapi:*');
    }

}

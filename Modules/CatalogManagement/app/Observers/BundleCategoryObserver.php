<?php

namespace Modules\CatalogManagement\app\Observers;

use Modules\CatalogManagement\app\Models\BundleCategory;
use App\Services\CacheService;
use Illuminate\Support\Facades\Log;

class BundleCategoryObserver
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the BundleCategory "created" event.
     */
    public function created(BundleCategory $bundleCategory): void
    {
        Log::info('BundleCategoryObserver: created event fired', ['id' => $bundleCategory->id]);
        $this->clearCacheAfterCommit();
    }

    /**
     * Handle the BundleCategory "updated" event.
     */
    public function updated(BundleCategory $bundleCategory): void
    {
        Log::info('BundleCategoryObserver: updated event fired', ['id' => $bundleCategory->id]);
        $this->clearCacheAfterCommit();
    }

    /**
     * Handle the BundleCategory "saved" event (fires after create/update and all relations)
     */
    public function saved(BundleCategory $bundleCategory): void
    {
        Log::info('BundleCategoryObserver: saved event fired', ['id' => $bundleCategory->id]);
        $this->clearCacheAfterCommit();
    }

    /**
     * Handle the BundleCategory "deleted" event.
     */
    public function deleted(BundleCategory $bundleCategory): void
    {
        Log::info('BundleCategoryObserver: deleted event fired', ['id' => $bundleCategory->id]);
        $this->clearCache();
    }

    /**
     * Clear cache after database transaction commits
     */
    protected function clearCacheAfterCommit(): void
    {
        \Illuminate\Support\Facades\DB::afterCommit(function () {
            $this->clearCache();
        });
    }

    /**
     * Clear all bundle category-related cache
     */
    protected function clearCache(): void
    {
        Log::info('BundleCategoryObserver: Clearing cache patterns', [
            'patterns' => ['bundlecategoryapi:*', 'bundleapi:*']
        ]);
        
        $cleared1 = $this->cache->forgetByPattern('bundlecategoryapi:*');
        $cleared2 = $this->cache->forgetByPattern('bundleapi:*');
        
        Log::info('BundleCategoryObserver: Cache cleared', [
            'bundlecategoryapi_keys' => $cleared1,
            'bundleapi_keys' => $cleared2
        ]);
    }
}

<?php

namespace Modules\CatalogManagement\app\Observers;

use Modules\CatalogManagement\app\Models\Bundle;
use App\Services\CacheService;
use Illuminate\Support\Facades\Log;

class BundleObserver
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the Bundle "created" event.
     */
    public function created(Bundle $bundle): void
    {
        // Don't clear cache here, wait for saved event
    }

    /**
     * Handle the Bundle "updated" event.
     */
    public function updated(Bundle $bundle): void
    {
        // Don't clear cache here, wait for saved event
    }

    /**
     * Handle the Bundle "saved" event (fires after create/update and all relations)
     */
    public function saved(Bundle $bundle): void
    {
        // Use afterCommit to ensure cache is cleared after transaction completes
        \Illuminate\Support\Facades\DB::afterCommit(function () {
            $this->clearBundleCache();
        });
    }

    /**
     * Handle the Bundle "deleted" event.
     */
    public function deleted(Bundle $bundle): void
    {
        $this->clearBundleCache();
    }

    /**
     * Clear all bundle-related cache
     */
    protected function clearBundleCache(): void
    {
        $this->cache->forgetByPattern('bundleapi:*');
        $this->cache->forgetByPattern('bundlecategoryapi:*'); // Also clear category cache as bundles affect categories
    }
}

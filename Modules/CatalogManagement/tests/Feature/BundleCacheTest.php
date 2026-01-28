<?php

namespace Modules\CatalogManagement\Tests\Feature;

use Tests\TestCase;
use Modules\CatalogManagement\app\Models\Bundle;
use Modules\CatalogManagement\app\Models\BundleCategory;
use Modules\CatalogManagement\app\Repositories\Api\BundleApiRepository;
use Modules\CatalogManagement\app\Repositories\Api\BundleCategoryApiRepository;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class BundleCacheTest extends TestCase
{
    use RefreshDatabase;

    protected BundleApiRepository $bundleRepo;
    protected BundleCategoryApiRepository $categoryRepo;
    protected CacheService $cache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        
        $this->bundleRepo = app(BundleApiRepository::class);
        $this->categoryRepo = app(BundleCategoryApiRepository::class);
        $this->cache = app(CacheService::class);
        
        Cache::flush();
    }

    /** @test */
    public function bundle_list_is_cached_on_first_request()
    {
        Bundle::factory()->count(3)->create(['is_active' => true, 'admin_approval' => 1]);

        // First call
        $result1 = $this->bundleRepo->getAllBundles([], 10);
        
        // Check cache exists
        $cacheKey = $this->cache->key('BundleApi', 'all', ['per_page' => 10]);
        $this->assertTrue($this->cache->has($cacheKey));
    }

    /** @test */
    public function bundle_list_returns_cached_data_on_second_request()
    {
        Bundle::factory()->count(3)->create(['is_active' => true, 'admin_approval' => 1]);

        // First call
        $start1 = microtime(true);
        $result1 = $this->bundleRepo->getAllBundles([], 10);
        $time1 = microtime(true) - $start1;

        // Second call (should be faster due to cache)
        $start2 = microtime(true);
        $result2 = $this->bundleRepo->getAllBundles([], 10);
        $time2 = microtime(true) - $start2;

        $this->assertLessThan($time1, $time2);
        $this->assertEquals($result1->count(), $result2->count());
    }

    /** @test */
    public function bundle_cache_is_cleared_when_bundle_is_created()
    {
        Bundle::factory()->count(2)->create(['is_active' => true, 'admin_approval' => 1]);
        
        // Cache the data
        $this->bundleRepo->getAllBundles([], 10);
        
        $cacheKey = $this->cache->key('BundleApi', 'all', ['per_page' => 10]);
        $this->assertTrue($this->cache->has($cacheKey));

        // Create new bundle
        Bundle::factory()->create(['is_active' => true, 'admin_approval' => 1]);

        // Cache should be cleared
        sleep(1); // Wait for observer to clear cache
        $this->assertFalse($this->cache->has($cacheKey));
    }

    /** @test */
    public function bundle_cache_is_cleared_when_bundle_is_updated()
    {
        $bundle = Bundle::factory()->create(['is_active' => true, 'admin_approval' => 1]);
        
        // Cache the data
        $this->bundleRepo->getAllBundles([], 10);
        
        $cacheKey = $this->cache->key('BundleApi', 'all', ['per_page' => 10]);
        $this->assertTrue($this->cache->has($cacheKey));

        // Update bundle
        $bundle->update(['is_active' => false]);

        // Cache should be cleared
        sleep(1); // Wait for observer to clear cache
        $this->assertFalse($this->cache->has($cacheKey));
    }

    /** @test */
    public function bundle_cache_is_cleared_when_bundle_is_deleted()
    {
        $bundle = Bundle::factory()->create(['is_active' => true, 'admin_approval' => 1]);
        
        // Cache the data
        $this->bundleRepo->getAllBundles([], 10);
        
        $cacheKey = $this->cache->key('BundleApi', 'all', ['per_page' => 10]);
        $this->assertTrue($this->cache->has($cacheKey));

        // Delete bundle
        $bundle->delete();

        // Cache should be cleared
        sleep(1); // Wait for observer to clear cache
        $this->assertFalse($this->cache->has($cacheKey));
    }

    /** @test */
    public function bundle_category_list_is_cached_on_first_request()
    {
        BundleCategory::factory()->count(3)->create(['active' => 1]);

        // First call
        $result1 = $this->categoryRepo->getAll([], 10);
        
        // Check cache exists
        $cacheKey = $this->cache->key('BundleCategoryApi', 'all', ['per_page' => 10]);
        $this->assertTrue($this->cache->has($cacheKey));
    }

    /** @test */
    public function bundle_category_cache_is_cleared_when_category_is_updated()
    {
        $category = BundleCategory::factory()->create(['active' => 1]);
        
        // Cache the data
        $this->categoryRepo->getAll([], 10);
        
        $cacheKey = $this->cache->key('BundleCategoryApi', 'all', ['per_page' => 10]);
        $this->assertTrue($this->cache->has($cacheKey));

        // Update category
        $category->update(['active' => 0]);

        // Cache should be cleared
        sleep(1); // Wait for observer to clear cache
        $this->assertFalse($this->cache->has($cacheKey));
    }

    /** @test */
    public function updating_bundle_category_also_clears_bundle_cache()
    {
        $category = BundleCategory::factory()->create(['active' => 1]);
        Bundle::factory()->create([
            'bundle_category_id' => $category->id,
            'is_active' => true,
            'admin_approval' => 1
        ]);
        
        // Cache both
        $this->categoryRepo->getAll([], 10);
        $this->bundleRepo->getAllBundles([], 10);
        
        $categoryCacheKey = $this->cache->key('BundleCategoryApi', 'all', ['per_page' => 10]);
        $bundleCacheKey = $this->cache->key('BundleApi', 'all', ['per_page' => 10]);
        
        $this->assertTrue($this->cache->has($categoryCacheKey));
        $this->assertTrue($this->cache->has($bundleCacheKey));

        // Update category
        $category->update(['active' => 0]);

        // Both caches should be cleared
        sleep(1); // Wait for observer to clear cache
        $this->assertFalse($this->cache->has($categoryCacheKey));
        $this->assertFalse($this->cache->has($bundleCacheKey));
    }

    /** @test */
    public function clear_cache_method_removes_all_bundle_cache_keys()
    {
        Bundle::factory()->count(3)->create(['is_active' => true, 'admin_approval' => 1]);
        
        // Create multiple cache keys
        $this->bundleRepo->getAllBundles([], 10);
        $this->bundleRepo->getAllBundles([], 20);
        
        // Clear all cache
        $this->bundleRepo->clearCache();
        
        $cacheKey1 = $this->cache->key('BundleApi', 'all', ['per_page' => 10]);
        $cacheKey2 = $this->cache->key('BundleApi', 'all', ['per_page' => 20]);
        
        $this->assertFalse($this->cache->has($cacheKey1));
        $this->assertFalse($this->cache->has($cacheKey2));
    }
}

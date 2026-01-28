<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\CatalogManagement\app\Repositories\Api\BundleCategoryApiRepository;
use Modules\CatalogManagement\app\Models\BundleCategory;
use App\Services\CacheService;
use Illuminate\Support\Facades\Redis;

class TestBundleCategoryCache extends Command
{
    protected $signature = 'test:bundle-category-cache';
    protected $description = 'Test bundle category cache invalidation on update';

    public function handle()
    {
        $this->info('=== Testing Bundle Category Cache on Update ===');
        $this->newLine();

        $repo = app(BundleCategoryApiRepository::class);
        $cache = app(CacheService::class);

        // Step 1: Get first category
        $this->info('1. Getting first bundle category...');
        $category = BundleCategory::first();

        if (!$category) {
            $this->error('   No bundle categories found');
            return 1;
        }

        $this->line("   Category ID: {$category->id}");
        $this->line("   Current name (EN): " . $category->getTranslation('name', 'en'));
        $this->line("   Current name (AR): " . $category->getTranslation('name', 'ar'));
        $this->newLine();

        // Step 2: Call API first time
        $this->info('2. Calling API first time (will be cached)...');
        $start = microtime(true);
        $result1 = $repo->getAll([], 10);
        $time1 = round((microtime(true) - $start) * 1000, 2);
        $this->line("   Time: {$time1}ms");
        $this->line("   Results count: " . $result1->count());
        $this->newLine();

        // Step 3: Call API second time
        $this->info('3. Calling API second time (should be cached)...');
        $start = microtime(true);
        $result2 = $repo->getAll([], 10);
        $time2 = round((microtime(true) - $start) * 1000, 2);
        $this->line("   Time: {$time2}ms");
        if ($time2 < $time1) {
            $this->line("   <fg=green>✓</> Cache is working! (" . round($time1/$time2, 1) . "x faster)");
        } else {
            $this->line("   <fg=yellow>!</> Cache might not be working");
        }
        $this->newLine();

        // Step 4: Update the category
        $this->info('4. Updating category name...');
        $newNameEn = "Test Category " . time();
        $newNameAr = "فئة اختبار " . time();

        $category->translations()->where('lang_key', 'name')->where('lang_id', 1)->update(['lang_value' => $newNameEn]);
        $category->translations()->where('lang_key', 'name')->where('lang_id', 2)->update(['lang_value' => $newNameAr]);
        $category->touch();

        $this->line("   Updated EN name to: {$newNameEn}");
        $this->line("   Updated AR name to: {$newNameAr}");
        $this->line("   Waiting for observer to clear cache...");
        sleep(1);
        $this->newLine();

        // Step 5: Check cache
        $this->info('5. Checking if cache was cleared...');
        $keys = Redis::connection()->keys('*bundlecategoryapi*');
        $this->line("   Cache keys found: " . count($keys));
        if (count($keys) == 0) {
            $this->line("   <fg=green>✓</> Cache was cleared!");
        } else {
            $this->line("   <fg=yellow>!</> Cache still exists");
        }
        $this->newLine();

        // Step 6: Call API again
        $this->info('6. Calling API again (should fetch fresh data)...');
        $start = microtime(true);
        $result3 = $repo->getAll([], 10);
        $time3 = round((microtime(true) - $start) * 1000, 2);
        $this->line("   Time: {$time3}ms");
        $this->newLine();

        // Step 7: Verify
        $this->info('7. Verifying updated name in API response...');
        $foundCategory = $result3->firstWhere('id', $category->id);
        if ($foundCategory) {
            $apiNameEn = $foundCategory->getTranslation('name', 'en');
            $apiNameAr = $foundCategory->getTranslation('name', 'ar');
            
            $this->line("   API returned EN name: {$apiNameEn}");
            $this->line("   API returned AR name: {$apiNameAr}");
            
            if ($apiNameEn === $newNameEn && $apiNameAr === $newNameAr) {
                $this->line("   <fg=green>✓ SUCCESS!</> API returned the updated names!");
            } else {
                $this->error("   ✗ FAILED! API returned old names");
                $this->line("   Expected EN: {$newNameEn}");
                $this->line("   Expected AR: {$newNameAr}");
            }
        } else {
            $this->error("   Category not found in API response");
        }

        $this->newLine();
        $this->info('=== Test Complete ===');
        $this->newLine();
        $this->line('Check logs: storage/logs/laravel.log');
        $this->line('Look for: BundleCategoryObserver: saved event fired');

        return 0;
    }
}

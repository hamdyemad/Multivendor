<?php

namespace Modules\CatalogManagement\Tests\Unit;

use Tests\TestCase;
use Modules\CatalogManagement\app\Models\Bundle;
use Modules\CatalogManagement\app\Models\BundleCategory;
use Modules\CatalogManagement\app\Models\BundleProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BundleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /** @test */
    public function bundle_has_required_attributes()
    {
        $bundle = Bundle::factory()->create();

        $this->assertNotNull($bundle->id);
        $this->assertNotNull($bundle->sku);
        $this->assertNotNull($bundle->slug);
    }

    /** @test */
    public function bundle_belongs_to_bundle_category()
    {
        $category = BundleCategory::factory()->create();
        $bundle = Bundle::factory()->create(['bundle_category_id' => $category->id]);

        $this->assertInstanceOf(BundleCategory::class, $bundle->bundleCategory);
        $this->assertEquals($category->id, $bundle->bundleCategory->id);
    }

    /** @test */
    public function bundle_has_many_bundle_products()
    {
        $bundle = Bundle::factory()->create();
        $bundleProduct = BundleProduct::factory()->create(['bundle_id' => $bundle->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $bundle->bundleProducts);
        $this->assertCount(1, $bundle->bundleProducts);
        $this->assertEquals($bundleProduct->id, $bundle->bundleProducts->first()->id);
    }

    /** @test */
    public function active_scope_returns_only_active_and_approved_bundles()
    {
        Bundle::factory()->create(['is_active' => true, 'admin_approval' => 1]);
        Bundle::factory()->create(['is_active' => false, 'admin_approval' => 1]);
        Bundle::factory()->create(['is_active' => true, 'admin_approval' => 0]);

        $activeBundles = Bundle::active()->get();

        $this->assertCount(1, $activeBundles);
    }

    /** @test */
    public function is_active_scope_returns_only_active_bundles()
    {
        Bundle::factory()->create(['is_active' => true, 'admin_approval' => 0]);
        Bundle::factory()->create(['is_active' => false, 'admin_approval' => 1]);

        $activeBundles = Bundle::isActive()->get();

        $this->assertCount(1, $activeBundles);
    }

    /** @test */
    public function approved_scope_returns_only_approved_bundles()
    {
        Bundle::factory()->create(['admin_approval' => 1]);
        Bundle::factory()->create(['admin_approval' => 0]);
        Bundle::factory()->create(['admin_approval' => 2]);

        $approvedBundles = Bundle::approved()->get();

        $this->assertCount(1, $approvedBundles);
    }

    /** @test */
    public function pending_scope_returns_only_pending_bundles()
    {
        Bundle::factory()->create(['admin_approval' => 0]);
        Bundle::factory()->create(['admin_approval' => 1]);

        $pendingBundles = Bundle::pending()->get();

        $this->assertCount(1, $pendingBundles);
    }

    /** @test */
    public function rejected_scope_returns_only_rejected_bundles()
    {
        Bundle::factory()->create(['admin_approval' => 2]);
        Bundle::factory()->create(['admin_approval' => 1]);

        $rejectedBundles = Bundle::rejected()->get();

        $this->assertCount(1, $rejectedBundles);
    }

    /** @test */
    public function filter_scope_filters_by_search()
    {
        $bundle = Bundle::factory()->create();
        $bundle->translations()->create([
            'lang_id' => 1,
            'lang_key' => 'name',
            'lang_value' => 'Test Bundle Name'
        ]);

        $results = Bundle::filter(['search' => 'Test Bundle'])->get();

        $this->assertCount(1, $results);
    }

    /** @test */
    public function filter_scope_filters_by_active_status()
    {
        Bundle::factory()->create(['is_active' => true]);
        Bundle::factory()->create(['is_active' => false]);

        $results = Bundle::filter(['active' => 1])->get();

        $this->assertCount(1, $results);
    }

    /** @test */
    public function filter_scope_filters_by_approval_status()
    {
        Bundle::factory()->create(['admin_approval' => 1]);
        Bundle::factory()->create(['admin_approval' => 0]);

        $results = Bundle::filter(['approval_status' => 1])->get();

        $this->assertCount(1, $results);
    }

    /** @test */
    public function filter_scope_filters_by_category()
    {
        $category = BundleCategory::factory()->create();
        Bundle::factory()->create(['bundle_category_id' => $category->id]);
        Bundle::factory()->create();

        $results = Bundle::filter(['category_id' => $category->id])->get();

        $this->assertCount(1, $results);
    }

    /** @test */
    public function bundle_type_attribute_returns_bundle()
    {
        $bundle = Bundle::factory()->create();

        $this->assertEquals('bundle', $bundle->type);
    }

    /** @test */
    public function bundle_can_be_soft_deleted()
    {
        $bundle = Bundle::factory()->create();
        $bundleId = $bundle->id;

        $bundle->delete();

        $this->assertSoftDeleted('bundles', ['id' => $bundleId]);
    }

    /** @test */
    public function bundle_casts_is_active_to_boolean()
    {
        $bundle = Bundle::factory()->create(['is_active' => 1]);

        $this->assertIsBool($bundle->is_active);
        $this->assertTrue($bundle->is_active);
    }

    /** @test */
    public function bundle_casts_admin_approval_to_integer()
    {
        $bundle = Bundle::factory()->create(['admin_approval' => '1']);

        $this->assertIsInt($bundle->admin_approval);
        $this->assertEquals(1, $bundle->admin_approval);
    }

    /** @test */
    public function bundle_is_created_by_admin()
    {
        $bundle = Bundle::factory()->create();

        // Bundles are created by administrators, not vendors
        $this->assertNull($bundle->vendor_id);
    }
}

<?php

namespace Modules\CatalogManagement\Tests\Feature;

use Tests\TestCase;
use Modules\CatalogManagement\app\Models\Bundle;
use Modules\CatalogManagement\app\Models\BundleCategory;
use Modules\Vendor\app\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BundleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /** @test */
    public function can_get_list_of_active_bundles()
    {
        Bundle::factory()->count(3)->create(['is_active' => true, 'admin_approval' => 1]);
        Bundle::factory()->create(['is_active' => false, 'admin_approval' => 1]);

        $response = $this->getJson('/api/bundles');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function can_get_single_bundle_by_id()
    {
        $bundle = Bundle::factory()->create(['is_active' => true, 'admin_approval' => 1]);

        $response = $this->getJson("/api/bundles/{$bundle->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $bundle->id);
    }

    /** @test */
    public function can_get_single_bundle_by_slug()
    {
        $bundle = Bundle::factory()->create([
            'slug' => 'test-bundle',
            'is_active' => true,
            'admin_approval' => 1
        ]);

        $response = $this->getJson("/api/bundles/test-bundle");

        $response->assertStatus(200);
        $response->assertJsonPath('data.slug', 'test-bundle');
    }

    /** @test */
    public function bundle_list_includes_total_points()
    {
        $bundle = Bundle::factory()->create(['is_active' => true, 'admin_approval' => 1]);

        $response = $this->getJson('/api/bundles');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'total_price', 'total_points']
            ]
        ]);
    }

    /** @test */
    public function bundle_detail_includes_bundle_products()
    {
        $bundle = Bundle::factory()->create(['is_active' => true, 'admin_approval' => 1]);

        $response = $this->getJson("/api/bundles/{$bundle->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'total_price',
                'total_points',
                'bundle_products'
            ]
        ]);
    }

    /** @test */
    public function cannot_get_inactive_bundle()
    {
        $bundle = Bundle::factory()->create(['is_active' => false, 'admin_approval' => 1]);

        $response = $this->getJson("/api/bundles/{$bundle->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function cannot_get_unapproved_bundle()
    {
        $bundle = Bundle::factory()->create(['is_active' => true, 'admin_approval' => 0]);

        $response = $this->getJson("/api/bundles/{$bundle->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function can_filter_bundles_by_category()
    {
        $category = BundleCategory::factory()->create();
        Bundle::factory()->count(2)->create([
            'bundle_category_id' => $category->id,
            'is_active' => true,
            'admin_approval' => 1
        ]);
        Bundle::factory()->create(['is_active' => true, 'admin_approval' => 1]);

        $response = $this->getJson("/api/bundles?category_id={$category->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    /** @test */
    public function can_paginate_bundles()
    {
        Bundle::factory()->count(15)->create(['is_active' => true, 'admin_approval' => 1]);

        $response = $this->getJson('/api/bundles?per_page=10');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.total', 15);
    }

    /** @test */
    public function bundle_response_includes_vendor_product_points()
    {
        $bundle = Bundle::factory()->create(['is_active' => true, 'admin_approval' => 1]);

        $response = $this->getJson("/api/bundles/{$bundle->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'bundle_products' => [
                    '*' => [
                        'points',
                        'vendor_product' => ['points']
                    ]
                ]
            ]
        ]);
    }
}

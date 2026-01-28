<?php

namespace Modules\CatalogManagement\Tests\Feature;

use Tests\TestCase;
use Modules\CatalogManagement\app\Models\BundleCategory;
use Modules\CatalogManagement\app\Models\Bundle;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BundleCategoryApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /** @test */
    public function can_get_list_of_active_bundle_categories()
    {
        BundleCategory::factory()->count(3)->create(['active' => 1]);
        BundleCategory::factory()->create(['active' => 0]);

        $response = $this->getJson('/api/bundle-categories');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function can_get_single_bundle_category_by_id()
    {
        $category = BundleCategory::factory()->create(['active' => 1]);

        $response = $this->getJson("/api/bundle-categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $category->id);
    }

    /** @test */
    public function can_get_single_bundle_category_by_slug()
    {
        $category = BundleCategory::factory()->create([
            'slug' => 'test-category',
            'active' => 1
        ]);

        $response = $this->getJson("/api/bundle-categories/test-category");

        $response->assertStatus(200);
        $response->assertJsonPath('data.slug', 'test-category');
    }

    /** @test */
    public function bundle_category_includes_bundles_count()
    {
        $category = BundleCategory::factory()->create(['active' => 1]);
        Bundle::factory()->count(3)->create([
            'bundle_category_id' => $category->id,
            'is_active' => true,
            'admin_approval' => 1
        ]);

        $response = $this->getJson('/api/bundle-categories');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'bundles_count']
            ]
        ]);
    }

    /** @test */
    public function bundle_category_detail_includes_bundles()
    {
        $category = BundleCategory::factory()->create(['active' => 1]);
        Bundle::factory()->count(2)->create([
            'bundle_category_id' => $category->id,
            'is_active' => true,
            'admin_approval' => 1
        ]);

        $response = $this->getJson("/api/bundle-categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'bundles_count',
                'bundles'
            ]
        ]);
    }

    /** @test */
    public function cannot_get_inactive_bundle_category()
    {
        $category = BundleCategory::factory()->create(['active' => 0]);

        $response = $this->getJson("/api/bundle-categories/{$category->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function can_filter_bundle_categories_by_search()
    {
        $category = BundleCategory::factory()->create(['active' => 1]);
        $category->translations()->create([
            'lang_id' => 1,
            'lang_key' => 'name',
            'lang_value' => 'Unique Category Name'
        ]);
        BundleCategory::factory()->create(['active' => 1]);

        $response = $this->getJson('/api/bundle-categories?search=Unique');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function can_paginate_bundle_categories()
    {
        BundleCategory::factory()->count(15)->create(['active' => 1]);

        $response = $this->getJson('/api/bundle-categories?per_page=10');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.total', 15);
    }

    /** @test */
    public function bundle_category_response_includes_translations()
    {
        $category = BundleCategory::factory()->create(['active' => 1]);
        $category->translations()->create([
            'lang_id' => 1,
            'lang_key' => 'name',
            'lang_value' => 'Test Category'
        ]);

        $response = $this->getJson("/api/bundle-categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Test Category');
    }

    /** @test */
    public function bundle_category_response_includes_seo_fields()
    {
        $category = BundleCategory::factory()->create(['active' => 1]);

        $response = $this->getJson("/api/bundle-categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'seo_title',
                'seo_description',
                'seo_keywords'
            ]
        ]);
    }
}

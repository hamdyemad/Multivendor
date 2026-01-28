<?php

namespace Modules\CatalogManagement\Tests\Unit;

use Tests\TestCase;
use Modules\CatalogManagement\app\Models\BundleCategory;
use Modules\CatalogManagement\app\Models\Bundle;
use App\Models\Attachment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BundleCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /** @test */
    public function bundle_category_has_required_attributes()
    {
        $category = BundleCategory::factory()->create();

        $this->assertNotNull($category->id);
        $this->assertNotNull($category->slug);
    }

    /** @test */
    public function bundle_category_has_many_bundles()
    {
        $category = BundleCategory::factory()->create();
        $bundle = Bundle::factory()->create(['bundle_category_id' => $category->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $category->bundles);
        $this->assertCount(1, $category->bundles);
        $this->assertEquals($bundle->id, $category->bundles->first()->id);
    }

    /** @test */
    public function bundle_category_has_many_attachments()
    {
        $category = BundleCategory::factory()->create();
        $attachment = Attachment::factory()->create([
            'attachable_type' => BundleCategory::class,
            'attachable_id' => $category->id
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $category->attachments);
        $this->assertCount(1, $category->attachments);
    }

    /** @test */
    public function active_scope_returns_only_active_categories()
    {
        BundleCategory::factory()->create(['active' => 1]);
        BundleCategory::factory()->create(['active' => 0]);

        $activeCategories = BundleCategory::active()->get();

        $this->assertCount(1, $activeCategories);
    }

    /** @test */
    public function filter_scope_filters_by_search()
    {
        $category = BundleCategory::factory()->create();
        $category->translations()->create([
            'lang_id' => 1,
            'lang_key' => 'name',
            'lang_value' => 'Test Category Name'
        ]);

        $results = BundleCategory::filter(['search' => 'Test Category'])->get();

        $this->assertCount(1, $results);
    }

    /** @test */
    public function filter_scope_filters_by_active_status()
    {
        BundleCategory::factory()->create(['active' => 1]);
        BundleCategory::factory()->create(['active' => 0]);

        $results = BundleCategory::filter(['active' => 1])->get();

        $this->assertCount(1, $results);
    }

    /** @test */
    public function filter_scope_filters_by_created_date_from()
    {
        BundleCategory::factory()->create(['created_at' => now()->subDays(5)]);
        BundleCategory::factory()->create(['created_at' => now()->subDays(1)]);

        $results = BundleCategory::filter(['created_date_from' => now()->subDays(2)->format('Y-m-d')])->get();

        $this->assertCount(1, $results);
    }

    /** @test */
    public function filter_scope_filters_by_created_date_to()
    {
        BundleCategory::factory()->create(['created_at' => now()->subDays(5)]);
        BundleCategory::factory()->create(['created_at' => now()->subDays(1)]);

        $results = BundleCategory::filter(['created_date_to' => now()->subDays(2)->format('Y-m-d')])->get();

        $this->assertCount(1, $results);
    }

    /** @test */
    public function get_image_attribute_returns_image_path()
    {
        $category = BundleCategory::factory()->create();
        $attachment = Attachment::factory()->create([
            'attachable_type' => BundleCategory::class,
            'attachable_id' => $category->id,
            'type' => 'image',
            'path' => 'test/image.jpg'
        ]);

        $this->assertEquals('test/image.jpg', $category->image);
    }

    /** @test */
    public function get_image_attribute_returns_null_when_no_image()
    {
        $category = BundleCategory::factory()->create();

        $this->assertNull($category->image);
    }

    /** @test */
    public function get_type_attribute_returns_bundle_category()
    {
        $category = BundleCategory::factory()->create();

        $this->assertEquals('bundle_category', $category->type);
    }

    /** @test */
    public function get_seo_title_returns_translated_value()
    {
        $category = BundleCategory::factory()->create();
        $category->translations()->create([
            'lang_id' => 1,
            'lang_key' => 'seo_title',
            'lang_value' => 'SEO Title'
        ]);

        $this->assertEquals('SEO Title', $category->getSeoTitle('en'));
    }

    /** @test */
    public function get_seo_title_falls_back_to_name_when_not_set()
    {
        $category = BundleCategory::factory()->create();
        $category->translations()->create([
            'lang_id' => 1,
            'lang_key' => 'name',
            'lang_value' => 'Category Name'
        ]);

        $seoTitle = $category->getSeoTitle('en');
        
        $this->assertNotNull($seoTitle);
    }

    /** @test */
    public function bundle_category_can_be_soft_deleted()
    {
        $category = BundleCategory::factory()->create();
        $categoryId = $category->id;

        $category->delete();

        $this->assertSoftDeleted('bundle_categories', ['id' => $categoryId]);
    }

    /** @test */
    public function bundle_category_uses_translation_trait()
    {
        $category = BundleCategory::factory()->create();

        $this->assertTrue(method_exists($category, 'translations'));
        $this->assertTrue(method_exists($category, 'getTranslation'));
    }
}

<?php

namespace Modules\CatalogManagement\app\Repositories;

use Modules\CatalogManagement\app\Interfaces\BundleRepositoryInterface;
use Modules\CatalogManagement\app\Models\Bundle;
use App\Models\Language;
use App\Models\Attachment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BundleRepository implements BundleRepositoryInterface
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
        $query = Bundle::with('attachments', 'country', 'vendor', 'bundleCategory', 'bundleProducts')->filter($filters)
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
            'bundleProducts.vendorProductVariant' => function ($query) {
                $query->with([
                    'vendorProduct.product.translations',
                    'variantConfiguration.key'
                ]);
            }
        ])
        ->where('id', $id)
        ->orwhere('slug', $id)->first();
    }

    /**
     * Create bundle
     */
    public function createBundle($data)
    {
        $bundle = Bundle::create([
            'vendor_id' => $data['vendor_id'],
            'bundle_category_id' => $data['bundle_category_id'],
            'sku' => $data['sku'],
            'slug' => uniqid(),
            'is_active' => $data['is_active'] ?? true,
        ]);

        // Handle main image upload
        $this->handleMainImage($bundle, $data, false);

        // Store translations
        $this->storeTranslations($bundle, $data);

        // Store bundle products
        $this->storeBundleProducts($bundle, $data);

        return $bundle;
    }

    /**
     * Update bundle
     */
    public function updateBundle($bundle, $data)
    {
        $bundle->update([
            'vendor_id' => $data['vendor_id'] ?? $bundle->vendor_id,
            'bundle_category_id' => $data['bundle_category_id'] ?? $bundle->bundle_category_id,
            'sku' => $data['sku'] ?? $bundle->sku,
            'is_active' => $data['is_active'] ?? $bundle->is_active,
        ]);

        // Handle main image upload
        $this->handleMainImage($bundle, $data, true);

        // Update translations
        $this->storeTranslations($bundle, $data);

        // Update bundle products
        $this->storeBundleProducts($bundle, $data);

        return $bundle;
    }

    /**
     * Delete bundle
     */
    public function deleteBundle($bundle)
    {
        $bundle->delete();
        return true;
    }

    /**
     * Store translations
     */
    public function storeTranslations($bundle, $data)
    {

        if (!isset($data['translations'])) {
            return;
        }

        // Force delete existing translations (including soft deleted ones)
        $bundle->translations()->forceDelete();

        $languages = Language::all()->keyBy('code');
        $translationFields = ['name', 'description', 'seo_title', 'seo_description', 'seo_keywords'];

        foreach ($data['translations'] as $langId => $translationData) {
            $language = $languages->firstWhere('id', $langId);
            if (!$language) {
                continue;
            }
            foreach ($translationFields as $field) {
                if (isset($translationData[$field])) {
                    if($field == 'name' && $language->code == 'en') {
                        // $originalSlug = $slug;
                        $model = Bundle::where('slug', Str::slug($translationData[$field]))
                        ->withoutCountryFilter()
                        ->first();
                        if($model) {
                            $newSlug = $model->slug . '-' . rand(1, 1000);
                            $bundle->update([
                                'slug' => $newSlug
                            ]);
                        } else {
                            $bundle->update([
                                'slug' => Str::slug($translationData[$field])
                            ]);
                        }
                    }

                    $bundle->translations()->updateOrCreate(
                        [
                            'lang_id' => $language->id,
                            'lang_key' => $field,
                        ],
                        [
                            'lang_value' => $translationData[$field],
                        ]
                    );
                }
            }
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($bundle)
    {
        $bundle->update(['is_active' => !$bundle->is_active]);
        return $bundle;
    }

    /**
     * Store bundle products
     */
    protected function storeBundleProducts(Bundle $bundle, array $data): void
    {
        // If bundle_products is not set, do nothing.
        if (!isset($data['bundle_products'])) {
            return;
        }

        $bundleProductsData = $data['bundle_products'] ?? [];
        $incomingVariantIds = array_column($bundleProductsData, 'vendor_product_variant_id');

        // Delete products that are no longer in the incoming data
        $bundle->bundleProducts()->whereNotIn('vendor_product_variant_id', $incomingVariantIds)->delete();

        // Update existing products or create new ones
        foreach ($bundleProductsData as $productData) {
            $bundle->bundleProducts()->updateOrCreate(
                [
                    'vendor_product_variant_id' => $productData['vendor_product_variant_id']
                ],
                [
                    'price' => $productData['price'],
                    'limitation_quantity' => $productData['limitation_quantity'] ?? null,
                    'min_quantity' => $productData['min_quantity'] ?? 1,
                ]
            );
        }
    }

    /**
     * Handle main image upload
     */
    protected function handleMainImage(Bundle $bundle, array $data, bool $isUpdate = false): void
    {
        \Log::error('aaaaaaaaaaaa', $data);

        if (isset($data['image'])) {
            // Delete old main image if updating
            if ($isUpdate) {
                if ($bundle->main_image) {
                    Storage::disk('public')->delete($bundle->main_image->path);
                    $bundle->attachments()->delete();
                }
            }

            $id = $bundle->id;
            // Store new main image
            $mainImagePath = $data['image']->store("bundles/$id", 'public');

            $bundle->attachments()->create([
                'path' => $mainImagePath,
                'type' => 'main_image',
            ]);
        }
    }
}

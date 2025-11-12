<?php

namespace Modules\CatalogManagement\app\Repositories;

use App\Models\Attachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\CatalogManagement\app\Interfaces\ProductInterface;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\ProductVariant;
use Modules\CatalogManagement\app\Models\VariantStock;

class ProductRepository implements ProductInterface
{
    public function getAllProducts(array $filters = [], int $perPage = 10)
    {
        $query = Product::with(['brand', 'category', 'variants', 'translations']);

        // Search in translations
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('translations', function($query) use ($searchTerm) {
                    $query->where('lang_key', 'title')
                          ->where('lang_value', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by active status
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter by featured status
        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $query->where('is_featured', $filters['is_featured']);
        }

        // Filter by brand
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter by department
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        // Filter by date range
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }
        
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getQuery(array $filters = [])
    {
        $query = Product::with(['brand', 'category', 'variants', 'translations'])->latest();

        // Apply same filters as getAllProducts
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('translations', function($query) use ($searchTerm) {
                    $query->where('lang_key', 'title')
                          ->where('lang_value', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }
        
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $query;
    }

    public function getProductById(int $id)
    {
        return Product::with([
            'brand',
            'department',
            'category',
            'subCategory',
            'tax',
            'translations',
            'attachments',
            'variants.translations',
            'variants.stocks.region'
        ])->findOrFail($id);
    }

    public function createProduct(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create product
            $product = Product::create([
                'slug' => $this->generateSlug($data),
                'sku' => $data['sku'],
                'points' => $data['points'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
                'brand_id' => $data['brand_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
                'max_per_order' => $data['max_per_order'] ?? null,
                'video_link' => $data['video_link'] ?? null,
            ]);

            // Store translations
            $this->storeTranslations($product, $data);

            // Handle main image
            $this->handleMainImage($product, $data);

            // Handle additional images
            $this->handleAdditionalImages($product, $data);

            // Handle variants or simple product
            $this->handleProductVariants($product, $data);

            return $product;
        });
    }

    public function updateProduct(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $product = Product::findOrFail($id);

            // Update product
            $product->update([
                'sku' => $data['sku'],
                'points' => $data['points'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
                'brand_id' => $data['brand_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
                'max_per_order' => $data['max_per_order'] ?? null,
                'video_link' => $data['video_link'] ?? null,
            ]);

            // Update translations
            $this->storeTranslations($product, $data);

            // Handle main image update
            $this->handleMainImage($product, $data, true);

            // Handle additional images
            $this->handleAdditionalImages($product, $data);

            // Update variants (delete old ones and create new ones)
            $product->variants()->delete();
            $this->handleProductVariants($product, $data);

            return $product;
        });
    }

    public function deleteProduct(int $id)
    {
        return DB::transaction(function () use ($id) {
            $product = Product::with(['attachments', 'variants'])->findOrFail($id);

            // Delete associated images
            foreach ($product->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }

            // Delete translations
            $product->translations()->delete();

            // Delete variants and their stocks
            foreach ($product->variants as $variant) {
                $variant->stocks()->delete();
                $variant->translations()->delete();
                $variant->delete();
            }

            // Delete the product (soft delete)
            $product->delete();

            return true;
        });
    }

    /**
     * Generate unique slug for product
     */
    protected function generateSlug(array $data): string
    {
        $baseSlug = Str::slug($data['sku'] . '-' . time());
        $slug = $baseSlug;
        $counter = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Store translations for product
     */
    protected function storeTranslations(Product $product, array $data): void
    {
        // Delete existing translations
        $product->translations()->delete();

        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $languageId => $fields) {
                $language = \App\Models\Language::find($languageId);
                if (!$language) {
                    continue;
                }

                // Store all translation fields
                $translationFields = [
                    'title', 'details', 'summary', 'features', 'instructions',
                    'extra_description', 'material', 'tags', 'meta_title',
                    'meta_description', 'meta_keywords'
                ];

                foreach ($translationFields as $field) {
                    if (!empty($fields[$field])) {
                        $product->translations()->create([
                            'lang_id' => $language->id,
                            'lang_key' => $field,
                            'lang_value' => $fields[$field],
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Handle main image upload
     */
    protected function handleMainImage(Product $product, array $data, bool $isUpdate = false): void
    {
        if (isset($data['main_image'])) {
            // Delete old main image if updating
            if ($isUpdate) {
                $oldMainImage = $product->mainImage;
                if ($oldMainImage) {
                    Storage::disk('public')->delete($oldMainImage->path);
                    $oldMainImage->delete();
                }
            }

            // Store new main image
            $mainImagePath = $data['main_image']->store('products/images', 'public');
            Attachment::create([
                'attachable_type' => Product::class,
                'attachable_id' => $product->id,
                'type' => 'main_image',
                'path' => $mainImagePath,
            ]);
        }
    }

    /**
     * Handle additional images upload
     */
    protected function handleAdditionalImages(Product $product, array $data): void
    {
        if (isset($data['additional_images']) && is_array($data['additional_images'])) {
            foreach ($data['additional_images'] as $image) {
                if ($image) {
                    $imagePath = $image->store('products/images', 'public');
                    Attachment::create([
                        'attachable_type' => Product::class,
                        'attachable_id' => $product->id,
                        'type' => 'additional_image',
                        'path' => $imagePath,
                    ]);
                }
            }
        }
    }

    /**
     * Handle product variants or simple product
     */
    protected function handleProductVariants(Product $product, array $data): void
    {
        $configurationType = $data['configuration_type'] ?? 'simple';

        if ($configurationType === 'simple') {
            // Create single variant for simple product
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $data['simple_sku'] ?? $data['sku'],
                'price' => ($data['price'] ?? 0) * 100, // Store in cents
                'has_discount' => $data['has_discount'] ?? false,
                'discount_price' => isset($data['price_before_discount']) ? $data['price_before_discount'] * 100 : 0,
                'discount_end_date' => $data['offer_end_date'] ?? null,
            ]);

            // Create stocks for simple product
            if (isset($data['stocks']) && is_array($data['stocks'])) {
                foreach ($data['stocks'] as $stockData) {
                    if (isset($stockData['region_id']) && isset($stockData['quantity'])) {
                        VariantStock::create([
                            'product_variant_id' => $variant->id,
                            'region_id' => $stockData['region_id'],
                            'stock' => $stockData['quantity'],
                        ]);
                    }
                }
            }
        } else {
            // Handle variants
            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variantData['sku'] ?? null,
                        'price' => ($variantData['price'] ?? 0) * 100, // Store in cents
                        'has_discount' => $variantData['has_discount'] ?? false,
                        'discount_price' => isset($variantData['discount_price']) ? $variantData['discount_price'] * 100 : 0,
                        'discount_end_date' => $variantData['discount_end_date'] ?? null,
                        'variant_key_id' => $variantData['key_id'] ?? null,
                        'variant_value_id' => $variantData['variant_id'] ?? null,
                    ]);

                    // Save variant translations
                    if (isset($variantData['translations'])) {
                        foreach ($variantData['translations'] as $langId => $translations) {
                            $language = \App\Models\Language::find($langId);
                            if (!$language) {
                                continue;
                            }

                            foreach ($translations as $key => $value) {
                                if (!empty($value)) {
                                    $variant->translations()->create([
                                        'lang_id' => $language->id,
                                        'lang_key' => $key,
                                        'lang_value' => $value,
                                    ]);
                                }
                            }
                        }
                    }

                    // Create variant stocks
                    if (isset($variantData['stock']) && is_array($variantData['stock'])) {
                        foreach ($variantData['stock'] as $stockData) {
                            if (isset($stockData['region_id']) && isset($stockData['quantity'])) {
                                VariantStock::create([
                                    'product_variant_id' => $variant->id,
                                    'region_id' => $stockData['region_id'],
                                    'stock' => $stockData['quantity'],
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}

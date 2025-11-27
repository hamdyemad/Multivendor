<?php

namespace Modules\CatalogManagement\app\Repositories;

use App\Models\Attachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\CatalogManagement\app\Interfaces\ProductInterface;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\VendorProduct;
use App\Models\UserType;
use Illuminate\Support\Facades\Auth;
use Modules\CatalogManagement\app\Http\Resources\BankProductResource;

class ProductRepository implements ProductInterface
{
    public function getAllProducts(array $filters = [], int $perPage = 10)
    {
        $query = Product::with(['brand', 'category', 'variants', 'translations'])->filter($filters);
        return ($perPage == 0) ? $query->get() : $query->latest()->paginate($perPage);
    }

    public function getAllBankProducts(array $filters = [], int $perPage = 10)
    {
        // Bank-specific relationships
        $with = [
            'brand',
            'department',
            'category',
            'subCategory',
            'variants',
            'translations',
            'brand.translations',
            'department.translations',
            'category.translations',
            'subCategory.translations',
            'mainImage',
            'variants.variantConfiguration.key.translations',
            'variants.variantConfiguration.translations'
        ];

        // Add bank-specific filters
        $bankFilters = array_merge($filters, [
            'type' => Product::TYPE_BANK,
            'is_active' => true,
        ]);

        $query = Product::with($with)->filter($bankFilters);
        return ($perPage == 0) ? $query->get() : $query->latest()->paginate($perPage);
    }

    public function getProductById($id)
    {
        return VendorProduct::with([
            'product.brand',
            'product.department',
            'product.category',
            'product.subCategory',
            'product.translations',
            'product.attachments',
            'vendor',
            'tax',
            'variants',
            'variants.stocks.region.translations',
            'variants.variantConfiguration.parent_data.parent_data.parent_data', // Load parent hierarchy
            'variants.variantConfiguration.key', // Load variant key
            'variants.variantConfiguration.parent_data.key', // Load parent keys
            'variants.variantConfiguration.parent_data.parent_data.key',
        ])->where('product_id', $id)->firstOrFail();
    }

    public function createProduct(array $data)
    {

        return DB::transaction(function () use ($data) {
            // Determine vendor_id based on user role
            $vendorId = $this->determineVendorId($data);

            // Get current user
            $currentUser = Auth::user();

            // Create product with temporary slug
            $product = Product::create([
                'slug' => Str::random(8), // Temporary slug
                'is_active' => $data['is_active'] ?? true,
                'configuration_type' => $data['configuration_type'],
                'vendor_id' => $vendorId,
                'brand_id' => $data['brand_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'created_by_user_id' => $currentUser->id,
            ]);

            // Get or create VendorProduct (handles both create and update)
            $vendorProduct = VendorProduct::firstOrCreate(
                ['vendor_id' => $vendorId, 'product_id' => $product->id],
                [
                    'tax_id' => $data['tax_id'],
                    'sku' => $data['sku'] ?? null,
                    'points' => $data['points'] ?? 0,
                    'max_per_order' => $data['max_per_order'],
                    'is_active' => $data['is_active'] ?? false,
                    'is_featured' => $data['is_featured'] ?? false,
                    'status' => in_array($currentUser->user_type_id, UserType::vendorIds()) ? 'pending' : 'approved',
                ]
            );

            // Store translations
            $this->storeTranslations($product, $data);

            // Handle main image
            $this->handleMainImage($product, $data);

            // Handle additional images
            $this->handleAdditionalImages($product, $data);

            // Handle variants or simple product
            $this->handleProductVariants($vendorProduct, $data);

            return $product;
        });
    }

    public function updateProduct(int $id, array $data)
    {
        Log::info($data);
        return DB::transaction(function () use ($id, $data) {
            $product = Product::findOrFail($id);

            // Determine vendor_id based on user role
            $vendorId = $this->determineVendorId($data);

            // Update product
            $product->update([
                'is_active' => $data['is_active'] ?? true,
                'configuration_type' => $data['configuration_type'],
                'vendor_id' => $vendorId,
                'brand_id' => $data['brand_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'sub_category_id' => $data['sub_category_id'] ?? null,
            ]);

            // Get current user
            $currentUser = Auth::user();

            // Get or create VendorProduct (handles both create and update)
            $vendorProduct = VendorProduct::firstOrCreate(
                ['vendor_id' => $vendorId, 'product_id' => $product->id],
                [
                    'tax_id' => $data['tax_id'],
                    'sku' => $data['sku'] ?? null,
                    'points' => $data['points'] ?? 0,
                    'max_per_order' => $data['max_per_order'],
                    'is_active' => $data['is_active'] ?? false,
                    'is_featured' => $data['is_featured'] ?? false,
                    'status' => in_array($currentUser->user_type_id, UserType::vendorIds()) ? 'pending' : 'approved',
                ]
            );

            // Update translations
            $this->storeTranslations($product, $data);

            // Handle main image update
            $this->handleMainImage($product, $data, true);

            // Handle additional images
            $this->handleAdditionalImages($product, $data);

            // Handle variants (unified method for both create and update)
            $this->handleProductVariants($vendorProduct, $data);

            return $product;
        });
    }

    public function deleteProduct(int $id)
    {
        return DB::transaction(function () use ($id) {
            // Find VendorProduct by product_id (not by vendorProduct id)
            $vendorProduct = VendorProduct::with(['product.attachments', 'variants'])
                ->where('product_id', $id)
                ->first();

            if (!$vendorProduct) {
                throw new \Exception(__('catalogmanagement::product.product_not_found'));
            }

            // Delete associated images
            foreach ($vendorProduct->product->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }

            // Delete translations
            $vendorProduct->product->translations()->delete();

            // Delete variants and their stocks
            foreach ($vendorProduct->variants as $variant) {
                $variant->stocks()->delete();
                $variant->delete();
            }
            $vendorProduct->product->delete();
            if($vendorProduct->product->variants){
                $vendorProduct->product->variants()->delete();
            }
            // Delete the product (soft delete)
            $vendorProduct->delete();


            return true;
        });
    }

    /**
     * Store translations for product
     */
    protected function storeTranslations(Product $product, array $data): void
    {
        // Force delete existing translations (including soft deleted ones)
        $product->translations()->forceDelete();

        if (!empty($data['translations'])) {
            Log::info('Storing translations for product', [
                'product_id' => $product->id,
                'translations_data' => $data['translations']
            ]);

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

                if(isset($fields['title']) && $language->code == 'en') {
                    $product->update([
                        'slug' => Str::of($fields['title'])->trim()->slug('-')
                    ]);
                }

                foreach ($translationFields as $field) {
                    if (isset($fields[$field])) {
                        Log::info('Creating translation', [
                            'field' => $field,
                            'language' => $language->code,
                            'value' => $fields[$field]
                        ]);

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
     * Handle product variants or simple product (works for both create and update)
     */
    protected function handleProductVariants(VendorProduct $vendorProduct, array $data): void
    {
        $configurationType = $data['configuration_type'] ?? 'simple';

        if ($configurationType === 'simple') {
            // If switching from variants to simple, delete all existing variants first
            $existingVariants = $vendorProduct->variants()->get();
            if ($existingVariants->count() > 1) {
                // Multiple variants exist, delete all and create a new simple one
                foreach ($existingVariants as $variant) {
                    $variant->stocks()->delete();
                    $variant->delete();
                }
                $existingVariant = null;
            } else {
                // Single variant exists (could be existing simple product)
                $existingVariant = $existingVariants->first();
            }

            // Prepare variant data with discount logic
            $hasDiscount = $data['has_discount'] ?? false;
            $variantData = [
                'sku' => $vendorProduct->sku,
                'price' => ($data['price'] ?? 0),
                'has_discount' => $hasDiscount,
                'price_before_discount' => $hasDiscount ? ($data['price_before_discount'] ?? 0) : 0,
                'discount_end_date' => $hasDiscount ? ($data['discount_end_date'] ?? null) : null,
                'variant_configuration_id' => null, // Simple products don't have variant configuration
            ];

            if ($existingVariant) {
                // Update existing variant
                $existingVariant->update($variantData);
                $vendorProductVariant = $existingVariant;
            } else {
                // Create new variant
                $vendorProductVariant = $vendorProduct->variants()->create($variantData);
            }

            // Sync stocks for simple product
            $this->syncVariantStocks($vendorProductVariant, $data['stocks'] ?? []);
        } else {
            // Handle variants with multiple configurations

            // If switching from simple to variants, delete the old simple variant first
            $existingVariants = $vendorProduct->variants()->get();
            if ($existingVariants->count() == 1) {
                $singleVariant = $existingVariants->first();
                // Check if it's a simple product variant (no variant_configuration_id)
                if (!$singleVariant->variant_configuration_id) {
                    $singleVariant->stocks()->delete();
                    $singleVariant->delete();
                    Log::info('Deleted old simple variant when switching to variants product');
                }
            }

            if (isset($data['variants']) && is_array($data['variants'])) {
                $incomingVariantIds = [];

                foreach ($data['variants'] as $variantIndex => $variantData) {
                    // Get variant configuration ID (standardized field name)
                    $variantConfigId = $variantData['variant_configuration_id'] ?? null;

                    Log::info('Processing variant', [
                        'variant_index' => $variantIndex,
                        'variant_data' => $variantData,
                        'variant_config_id' => $variantConfigId,
                        'vendor_product_id' => $vendorProduct->id
                    ]);

                    $existingProductVariant = null;
                    if(isset($variantData['id'])) {
                        // Find existing vendor product variant by ID
                        $existingProductVariant = $vendorProduct->variants()
                            ->find($variantData['id']);
                    } elseif ($variantConfigId) {
                        // Check if a variant with this configuration already exists for this vendor
                        $existingProductVariant = $vendorProduct->variants()
                            ->where('variant_configuration_id', $variantConfigId)
                            ->first();

                        if ($existingProductVariant) {
                            Log::info('Found existing variant by configuration_id', [
                                'variant_id' => $existingProductVariant->id,
                                'variant_config_id' => $variantConfigId
                            ]);
                        }
                    }

                    // Skip only if we don't have a configuration ID AND can't find existing variant
                    if (!$variantConfigId && !$existingProductVariant) {
                        Log::error('❌ VARIANT SKIPPED - Missing variant_configuration_id', [
                            'variant_index' => $variantIndex,
                            'available_keys' => array_keys($variantData),
                            'variant_data' => $variantData,
                            'message' => 'New variants must have a variant_configuration_id (or value_id/variant_id/key_id). Please ensure the variant configuration is selected in the form before submitting.'
                        ]);

                        // Throw exception to alert user instead of silently skipping
                        throw new \Exception("Variant at index {$variantIndex} is missing variant configuration. Please select a variant configuration (Color, Size, etc.) for all new variants.");
                    }

                    if ($existingProductVariant) {
                        // Prepare variant data with discount logic
                        $hasVariantDiscount = $variantData['has_discount'] ?? false;
                        $updateData = [
                            'sku' => $variantData['sku'] ?? null,
                            'price' => $variantData['price'] ?? 0,
                            'has_discount' => $hasVariantDiscount,
                            'price_before_discount' => $hasVariantDiscount ? ($variantData['price_before_discount'] ?? 0) : 0,
                            'discount_end_date' => $hasVariantDiscount ? ($variantData['discount_end_date'] ?? null) : null,
                        ];

                        Log::info('Updating variant', [
                            'variant_id' => $variantData['id'],
                            'has_discount' => $hasVariantDiscount,
                            'price_before_discount_input' => $variantData['price_before_discount'] ?? 'not_set',
                            'price_before_discount_final' => $updateData['price_before_discount'],
                            'update_data' => $updateData
                        ]);

                        // Update existing vendor variant
                        $existingProductVariant->update($updateData);
                        $incomingVariantIds[] = $variantData['id'];
                        $vendorProductVariant = $existingProductVariant;
                    } else {
                        // Create new global variant if it doesn't exist
                        if (!$existingProductVariant) {
                            $vendorProduct->product->variants()->create([
                                'variant_configuration_id' => $variantConfigId,
                            ]);
                        }

                        // Prepare variant data with discount logic for creation
                        $hasVariantDiscount = $variantData['has_discount'] ?? false;

                        // Generate SKU if not provided (required field)
                        $sku = $variantData['sku'] ?? null;
                        if (empty($sku)) {
                            // Generate SKU: PRODUCT_ID-VARIANT_CONFIG_ID-TIMESTAMP
                            $sku = $vendorProduct->product_id . '-V' . $variantConfigId . '-' . time();
                            Log::info('Generated SKU for variant', [
                                'generated_sku' => $sku,
                                'variant_config_id' => $variantConfigId
                            ]);
                        }

                        $createData = [
                            'variant_configuration_id' => $variantConfigId,
                            'sku' => $sku,
                            'price' => $variantData['price'] ?? 0,
                            'has_discount' => $hasVariantDiscount,
                            'price_before_discount' => $hasVariantDiscount ? ($variantData['price_before_discount'] ?? 0) : 0,
                            'discount_end_date' => $hasVariantDiscount ? ($variantData['discount_end_date'] ?? null) : null,
                        ];

                        // Create new vendor variant
                        $vendorProductVariant = $vendorProduct->variants()->create($createData);
                        $incomingVariantIds[] = $vendorProductVariant->id;
                    }

                    // Sync stocks for this variant
                    $this->syncVariantStocks($vendorProductVariant, $variantData['stocks'] ?? []);
                }

                // Delete variants that are no longer in the data (only for updates)
                if (!empty($incomingVariantIds)) {
                    $vendorProduct->variants()->whereNotIn('id', $incomingVariantIds)->delete();
                }
            }
        }
    }

    /**
     * Sync stocks for a variant (add new, update existing, delete removed)
     */
    protected function syncVariantStocks($variant, array $stocksData): void
    {
        $incomingStockRegionIds = [];
        foreach ($stocksData as $stockData) {
            if (isset($stockData['region_id']) && isset($stockData['quantity'])) {
                $existingStock = $variant->stocks()
                    ->where('region_id', $stockData['region_id'])
                    ->first();

                if ($existingStock) {
                    // Update existing stock
                    $existingStock->update([
                        'quantity' => $stockData['quantity'],
                    ]);
                    $incomingStockRegionIds[] = $existingStock->id;
                } else {
                    // Create new stock
                    $newStock = $variant->stocks()->create([
                        'region_id' => $stockData['region_id'],
                        'quantity' => $stockData['quantity'],
                    ]);
                    $incomingStockRegionIds[] = $newStock->id;
                }
            }
        }

        // Delete stocks that are no longer in the data
        $variant->stocks()->whereNotIn('id', $incomingStockRegionIds)->delete();
    }

    /**
     * Determine vendor ID based on user role and form data
     */
    protected function determineVendorId(array $data): ?int
    {
        $currentUser = Auth::user();
        $userType = $currentUser->user_type_id;

        if (in_array($userType, UserType::adminIds())) {
            // Admin/Super Admin can select vendor from form
            return $data['vendor_id'] ?? null;
        } elseif (in_array($userType, UserType::vendorIds())) {
            // Vendor can only create products for themselves
            if($currentUser->vendor_id) {
                $vendor_id = $currentUser->vendor_id;
            } else {
                $vendor = $currentUser->vendor;
                return $vendor ? $vendor->id : null;
            }
        }

        return null;
    }

    /**
     * Update only stock and pricing for a product
     * Uses the same logic as handleProductVariants for consistency
     */
    public function updateStockAndPricing($id, array $data)
    {
        // Get VendorProduct (which includes the product relationship)
        $vendorProduct = $this->getProductById($id);

        if (!$vendorProduct) {
            throw new \Exception('Product not found');
        }

        // Get the actual Product model
        $product = $vendorProduct->product;

        if (!$product) {
            throw new \Exception('Product data not found');
        }

        // Use the same handleProductVariants method for consistency
        $this->handleProductVariants($vendorProduct, $data);

        return $vendorProduct->fresh();
    }

    /**
     * Search bank products for Select2 AJAX
     */
    public function searchBankProducts(string $search = '', ?int $vendorId = null, int $perPage = 20)
    {
        // Build filters array
        $filters = [
            'search' => $search,
            'exclude_vendor_id' => $vendorId, // Exclude products already in this vendor's catalog
        ];

        // Use the dedicated getAllBankProducts method
        $products = $this->getAllBankProducts($filters, $perPage);

        return [
            'success' => true,
            'products' => BankProductResource::collection($products->items()),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'total' => $products->total()
        ];
    }

    /**
     * Get vendor product for a specific product and vendor combination
     */
    public function getVendorProductByProductAndVendor(int $productId, int $vendorId)
    {
        $vendorProduct = VendorProduct::with(['variants.stocks.region', 'variants.variantConfiguration'])
            ->where('product_id', $productId)
            ->where('vendor_id', $vendorId)
            ->first();

        if (!$vendorProduct) {
            return null;
        }

        // Format variants data
        $variants = $vendorProduct->variants->map(function($variant) {
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'has_discount' => $variant->has_discount,
                'price_before_discount' => $variant->price_before_discount,
                'discount_end_date' => $variant->discount_end_date,
                'variant_configuration_id' => $variant->variant_configuration_id,
                'variant_name' => $variant->variantConfiguration ? $variant->variantConfiguration->name : null,
                'stocks' => $variant->stocks->map(function($stock) {
                    return [
                        'id' => $stock->id,
                        'region_id' => $stock->region_id,
                        'quantity' => $stock->quantity
                    ];
                })
            ];
        });

        return [
            'id' => $vendorProduct->id,
            'configuration_type' => $vendorProduct->configuration_type,
            'tax_id' => $vendorProduct->tax_id,
            'sku' => $vendorProduct->sku,
            'points' => $vendorProduct->points,
            'max_per_order' => $vendorProduct->max_per_order,
            'video_link' => $vendorProduct->video_link,
            'is_active' => $vendorProduct->is_active,
            'is_featured' => $vendorProduct->is_featured,
            'offer_date_view' => $vendorProduct->offer_date_view,
            'variants' => $variants
        ];
    }

    /**
     * Save bank product stock for a vendor (create or update VendorProduct)
     */
    public function saveBankStock(array $data)
    {
        return DB::transaction(function () use ($data) {
            $productId = $data['product_id'];
            $vendorId = $data['vendor_id'];
            $vendorProductId = $data['vendor_product_id'] ?? null;

            // Find or create VendorProduct
            if ($vendorProductId) {
                // EXISTING vendor product - only update stock data, not general settings
                $vendorProduct = VendorProduct::findOrFail($vendorProductId);
                $configurationType = $vendorProduct->configuration_type;
            } else {
                // NEW vendor product - create with all general settings
                $configurationType = $data['configuration_type'];
                $taxId = $data['tax_id'] ?? null;

                $vendorProductData = [
                    'product_id' => $productId,
                    'vendor_id' => $vendorId,
                    'configuration_type' => $configurationType,
                    'tax_id' => $taxId,
                    'sku' => $data['sku'] ?? null,
                    'points' => $data['points'] ?? 0,
                    'max_per_order' => $data['max_per_order'] ?? 10,
                    'video_link' => $data['video_link'] ?? null,
                    'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
                    'is_featured' => isset($data['is_featured']) ? (bool) $data['is_featured'] : false,
                    'offer_date_view' => isset($data['offer_date_view']) ? (bool) $data['offer_date_view'] : false,
                    'status' => VendorProduct::STATUS_APPROVED,
                ];

                $vendorProduct = VendorProduct::create($vendorProductData);
            }

            // Handle stock data (for both new and existing)
            if ($configurationType === 'simple') {
                $this->handleSimpleBankStock($vendorProduct, $data);
            } else {
                $this->handleVariantsBankStock($vendorProduct, $data);
            }

            return $vendorProduct->fresh(['variants.stocks']);
        });
    }

    /**
     * Handle simple product stock for bank
     */
    private function handleSimpleBankStock(VendorProduct $vendorProduct, array $data)
    {
        $variant = $vendorProduct->variants()->first();

        $variantData = [
            'sku' => $data['sku'] ?? null,
            'price' => $data['price'] ?? 0,
            'has_discount' => isset($data['has_discount']) && $data['has_discount'],
            'price_before_discount' => $data['price_before_discount'] ?? null,
            'discount_end_date' => $data['discount_end_date'] ?? null
        ];

        if (!$variant) {
            $variant = $vendorProduct->variants()->create($variantData);
        } else {
            $variant->update($variantData);
        }

        // Handle stocks
        $this->syncBankVariantStocks($variant, $data['stocks'] ?? []);
    }

    /**
     * Handle variants stock for bank
     */
    private function handleVariantsBankStock(VendorProduct $vendorProduct, array $data)
    {
        $variantsData = $data['variants'] ?? [];

        foreach ($variantsData as $variantData) {
            if (isset($variantData['id']) && $variantData['id']) {
                // Update existing variant
                $variant = $vendorProduct->variants()->find($variantData['id']);
                if ($variant) {
                    $variant->update([
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'] ?? 0,
                        'has_discount' => isset($variantData['has_discount']) && $variantData['has_discount'],
                        'price_before_discount' => $variantData['price_before_discount'] ?? null,
                        'discount_end_date' => $variantData['discount_end_date'] ?? null
                    ]);
                }
            } else {
                // Create new variant
                $variant = $vendorProduct->variants()->create([
                    'variant_configuration_id' => $variantData['variant_configuration_id'] ?? null,
                    'sku' => $variantData['sku'] ?? null,
                    'price' => $variantData['price'] ?? 0,
                    'has_discount' => isset($variantData['has_discount']) && $variantData['has_discount'],
                    'price_before_discount' => $variantData['price_before_discount'] ?? null,
                    'discount_end_date' => $variantData['discount_end_date'] ?? null
                ]);
            }

            // Handle stocks for this variant
            if (isset($variantData['stocks'])) {
                $this->syncBankVariantStocks($variant, $variantData['stocks']);
            }
        }
    }

    /**
     * Sync stocks for a variant (bank stock management)
     */
    private function syncBankVariantStocks($variant, array $stocksData)
    {
        $processedStockIds = [];

        foreach ($stocksData as $stockData) {
            if (!isset($stockData['region_id']) || !$stockData['region_id']) {
                continue;
            }

            if (isset($stockData['id']) && $stockData['id']) {
                // Update existing stock
                $stock = $variant->stocks()->find($stockData['id']);
                if ($stock) {
                    $stock->update([
                        'region_id' => $stockData['region_id'],
                        'quantity' => $stockData['quantity'] ?? 0
                    ]);
                    $processedStockIds[] = $stock->id;
                }
            } else {
                // Create new stock or update by region
                $stock = $variant->stocks()->updateOrCreate(
                    ['region_id' => $stockData['region_id']],
                    ['quantity' => $stockData['quantity'] ?? 0]
                );
                $processedStockIds[] = $stock->id;
            }
        }

        // Delete stocks not in the processed list
        if (!empty($processedStockIds)) {
            $variant->stocks()->whereNotIn('id', $processedStockIds)->delete();
        }
    }

    /**
     * Get products that are not in VendorProduct for a specific vendor
     */
    public function getProductsNotInVendor(int $vendorId, string $search = '')
    {
        try {
            Log::info('Getting products not in vendor', ['vendor_id' => $vendorId, 'search' => $search]);

            $query = Product::with(['translations', 'brand.translations', 'department.translations', 'category.translations', 'subCategory.translations', 'attachments'])
                ->whereNotExists(function ($query) use ($vendorId) {
                    $query->select('id')
                        ->from('vendor_products')
                        ->whereColumn('vendor_products.product_id', 'products.id')
                        ->where('vendor_products.vendor_id', $vendorId);
                });

            if (!empty($search)) {
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('lang_value', 'like', '%' . $search . '%');
                });
            }

            $products = $query->limit(50)->get();
            Log::info('Found products', ['count' => $products->count()]);

            return $products->map(function ($product) {
                try {
                    return [
                        'id' => $product->id,
                        'title_en' => $product->getTranslation('title', 'en') ?? '-',
                        'title_ar' => $product->getTranslation('title', 'ar') ?? '-',
                        'brand' => $product->brand ? ($product->brand->getTranslation('name', app()->getLocale()) ?? $product->brand->name ?? '-') : '-',
                        'department' => $product->department ? ($product->department->getTranslation('name', app()->getLocale()) ?? $product->department->name ?? '-') : '-',
                        'category' => $product->category ? ($product->category->getTranslation('name', app()->getLocale()) ?? $product->category->name ?? '-') : '-',
                        'sub_category' => $product->subCategory ? ($product->subCategory->getTranslation('name', app()->getLocale()) ?? $product->subCategory->name ?? '-') : '-',
                        'image' => $product->attachments->where('type', 'main_image')->first()?->path ?? null,
                        'created_at' => $product->created_at ? (is_string($product->created_at) ? $product->created_at : $product->created_at->format('Y-m-d')) : date('Y-m-d'),
                    ];
                } catch (\Exception $e) {
                    Log::error('Error processing product', ['product_id' => $product->id, 'error' => $e->getMessage()]);
                    return [
                        'id' => $product->id,
                        'title_en' => 'Error loading product',
                        'title_ar' => 'خطأ في تحميل المنتج',
                        'brand' => '-',
                        'department' => '-',
                        'category' => '-',
                        'sub_category' => '-',
                        'image' => null,
                        'created_at' => $product->created_at ? (is_string($product->created_at) ? $product->created_at : $product->created_at->format('Y-m-d')) : date('Y-m-d'),
                    ];
                }
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error in getProductsNotInVendor', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

}

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
use Modules\CatalogManagement\app\Models\Tax;

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
            'attachments',
            'mainImage',
            'additionalImages',
            'variants.variantConfiguration.key.translations',
            'variants.variantConfiguration.translations',
            'vendorProduct'
        ];

        // Add bank-specific filters
        $bankFilters = array_merge($filters, [
            'type' => Product::TYPE_BANK,
            'is_active' => true,
        ]);
        \Log::info('bankFilters', $bankFilters);

        $query = Product::with($with)->filter($bankFilters);
        
        // Filter by vendor's departments if vendor_id is provided (without excluding existing products)
        if (!empty($filters['vendor_id'])) {
            $vendorId = $filters['vendor_id'];
            $vendor = \Modules\Vendor\app\Models\Vendor::withoutGlobalScopes()->find($vendorId);
            
            if ($vendor) {
                $departmentIds = $vendor->departments()->pluck('departments.id')->toArray();
                
                if (!empty($departmentIds)) {
                    $query->whereIn('department_id', $departmentIds);
                }
            }
        }
        
        // Filter by vendor's departments if exclude_vendor_id is provided (and exclude existing products)
        if (!empty($filters['exclude_vendor_id'])) {
            $vendorId = $filters['exclude_vendor_id'];
            $vendor = \Modules\Vendor\app\Models\Vendor::withoutGlobalScopes()->find($vendorId);
            
            if ($vendor) {
                $departmentIds = $vendor->departments()->pluck('departments.id')->toArray();
                
                if (!empty($departmentIds)) {
                    $query->whereIn('department_id', $departmentIds);
                }
            }
        }
        
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
        ])->findOrFail($id);
    }

    public function createProduct(array $data)
    {

        return DB::transaction(function () use ($data) {
            // Determine vendor_id based on user role
            $vendorId = $this->determineVendorId($data);

            // Get current user
            $currentUser = Auth::user();

            // Check if we are creating from a bank product
            if (isset($data['bank_product_id']) && !empty($data['bank_product_id'])) {
                $product = Product::findOrFail($data['bank_product_id']);
                Log::info('Linking vendor product to existing bank product', ['bank_product_id' => $product->id]);
            } else {
                // Create new product
                $product = Product::create([
                    'slug' => \Str::uuid(),
                    'is_active' => $data['is_active'] ?? true,
                    'configuration_type' => $data['configuration_type'],
                    'vendor_id' => $vendorId,
                    'brand_id' => $data['brand_id'] ?? null,
                    'department_id' => $data['department_id'] ?? null,
                    'category_id' => $data['category_id'] ?? null,
                    'sub_category_id' => $data['sub_category_id'] ?? null,
                    'created_by_user_id' => $currentUser->id,
                ]);

                // Store translations only for new products
                $this->storeTranslations($product, $data);
                
                // Handle main image only for new products
                $this->handleMainImage($product, $data);

                // Handle additional images only for new products
                $this->handleAdditionalImages($product, $data);
            }

            // Get or create VendorProduct
            $vendorProduct = VendorProduct::updateOrCreate(
                ['vendor_id' => $vendorId, 'product_id' => $product->id],
                [
                    'tax_id' => $data['tax_id'],
                    'sku' => $data['sku'] ?? null,
                    'video_link' => $data['video_link'] ?? null,
                    'max_per_order' => $data['max_per_order'],
                    'is_active' => $data['is_active'] ?? false,
                    'is_featured' => $data['is_featured'] ?? false,
                    'status' => in_array($currentUser->user_type_id, UserType::vendorIds()) ? 'pending' : 'approved',
                ]
            );

            // Ensure product relationship is loaded
            $vendorProduct->setRelation('product', $product);

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
                    'max_per_order' => $data['max_per_order'],
                    'is_active' => $data['is_active'] ?? false,
                    'is_featured' => $data['is_featured'] ?? false,
                    'status' => in_array($currentUser->user_type_id, UserType::vendorIds()) ? 'pending' : 'approved',
                ]
            );

            // Ensure product relationship is loaded
            $vendorProduct->setRelation('product', $product);

            // Update vendor product fields (for both new and existing records)
            // Reset status to pending when vendor edits the product
            $newStatus = $vendorProduct->status;
            if (in_array($currentUser->user_type_id, UserType::vendorIds())) {
                $newStatus = 'pending';
            }
            
            $vendorProduct->update([
                'tax_id' => $data['tax_id'],
                'sku' => $data['sku'] ?? null,
                'video_link' => $data['video_link'] ?? null,
                'max_per_order' => $data['max_per_order'],
                'is_active' => $data['is_active'] ?? false,
                'is_featured' => $data['is_featured'] ?? false,
                'status' => $newStatus,
            ]);

            // Update translations
            $this->storeTranslations($product, $data);

            // Handle main image update
            $this->handleMainImage($product, $data, true);

            // Handle additional images
            $this->handleAdditionalImages($product, $data, true);

            // Handle variants (unified method for both create and update)
            $this->handleProductVariants($vendorProduct, $data);

            return $product;
        });
    }

    public function deleteProduct(int $id)
    {
        return DB::transaction(function () use ($id) {
            // Find VendorProduct by its own id (not by product_id)
            $vendorProduct = VendorProduct::with(['product.attachments', 'variants'])
                ->where('id', $id)
                ->first();

            if (!$vendorProduct) {
                throw new \Exception(__('catalogmanagement::product.product_not_found'));
            }

            $product = $vendorProduct->product;

            // Delete variants and their stocks
            foreach ($vendorProduct->variants as $variant) {
                $variant->stocks()->delete();
                $variant->delete();
            }

            // Delete the vendor product (soft delete)
            $vendorProduct->delete();

            // Check if the product has any other vendor products linked to it
            // If not, also soft delete the product
            $otherVendorProducts = VendorProduct::where('product_id', $product->id)
                ->where('id', '!=', $id)
                ->count();

            if ($otherVendorProducts === 0) {
                // No other vendors are using this product, safe to delete
                $product->delete();
            }

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

                foreach ($translationFields as $field) {
                    if (isset($fields[$field])) {

                        if($field == 'title' && $language->code == 'en') {
                            // $originalSlug = $slug;
                            $model = Product::where('slug', Str::slug($fields[$field]))
                            ->withoutCountryFilter()
                            ->first();
                            if($model) {
                                $newSlug = $model->slug . '-' . rand(1, 1000);
                                $product->update([
                                    'slug' => $newSlug
                                ]);
                            } else {
                                $product->update([
                                    'slug' => Str::slug($fields[$field])
                                ]);
                            }
                        }

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
    protected function handleAdditionalImages(Product $product, array $data, bool $isUpdate = false): void
    {
        if (isset($data['additional_images']) && is_array($data['additional_images']) && count($data['additional_images']) > 0) {
            // If updating and new images are provided, delete old additional images first
            if ($isUpdate) {
                $oldAdditionalImages = $product->additionalImages;
                if ($oldAdditionalImages && $oldAdditionalImages->count() > 0) {
                    foreach ($oldAdditionalImages as $oldImage) {
                        // Delete the file from storage
                        if (Storage::disk('public')->exists($oldImage->path)) {
                            Storage::disk('public')->delete($oldImage->path);
                        }
                        // Delete the database record
                        $oldImage->delete();
                    }
                }
            }

            // Store new additional images
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
        \Log::info('vendorProduct', ['vendorProduct' => $vendorProduct]);
        \Log::info('vendorProduct data', $data);
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
            $hasDiscount = filter_var($data['has_discount'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // Get SKU from vendor product variant ID if provided (for bank imports)
            $sku = $data['sku'] ?? rand(1000, 9999);
            if (isset($data['vendor_product_variant_id']) && !empty($data['vendor_product_variant_id'])) {
                $variantToGetSku = $vendorProduct->variants()
                    ->find($data['vendor_product_variant_id']);
                if ($variantToGetSku) {
                    $baseSku = $variantToGetSku->sku;
                    // Append vendor slug to SKU
                    $vendor = $vendorProduct->vendor;
                    $vendorSlug = $vendor ? $vendor->slug : 'vendor';
                    $sku = $baseSku . '-' . $vendorSlug;
                    Log::info('Generated SKU with vendor slug', [
                        'vendor_product_variant_id' => $data['vendor_product_variant_id'],
                        'base_sku' => $baseSku,
                        'vendor_slug' => $vendorSlug,
                        'final_sku' => $sku
                    ]);
                }
            }

            $variantData = [
                'price' => ($data['price'] ?? 0),
                'has_discount' => $hasDiscount,
                'price_before_discount' => $hasDiscount ? ($data['price_before_discount'] ?? 0) : 0,
                'discount_end_date' => $hasDiscount ? ($data['discount_end_date'] ?? null) : null,
                'variant_configuration_id' => null, // Simple products don't have variant configuration
            ];

            // Add SKU if retrieved
            if ($sku) {
                $variantData['sku'] = $sku;
            }

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

            // Handle explicitly deleted variants first
            if (isset($data['deleted_variants']) && is_array($data['deleted_variants'])) {
                foreach ($data['deleted_variants'] as $deletedVariantId) {
                    $variantToDelete = $vendorProduct->variants()->find($deletedVariantId);
                    if ($variantToDelete) {
                        $variantToDelete->stocks()->delete();
                        $variantToDelete->delete();
                        Log::info('Deleted variant explicitly', ['variant_id' => $deletedVariantId]);
                    }
                }
            }

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
                        $hasVariantDiscount = filter_var($variantData['has_discount'] ?? false, FILTER_VALIDATE_BOOLEAN);

                        // Get SKU from vendor product variant ID if provided (for bank imports)
                        $variantSku = null;
                        if (isset($variantData['vendor_product_variant_id']) && !empty($variantData['vendor_product_variant_id'])) {
                            $variantToGetSku = $vendorProduct->variants()
                                ->find($variantData['vendor_product_variant_id']);
                            if ($variantToGetSku) {
                                $baseSku = $variantToGetSku->sku;
                                // Append vendor slug to SKU
                                $vendor = $vendorProduct->vendor;
                                $vendorSlug = $vendor ? $vendor->slug : 'vendor';
                                $variantSku = $baseSku . '-' . $vendorSlug;
                                Log::info('Generated SKU with vendor slug for existing variant', [
                                    'vendor_product_variant_id' => $variantData['vendor_product_variant_id'],
                                    'base_sku' => $baseSku,
                                    'vendor_slug' => $vendorSlug,
                                    'final_sku' => $variantSku
                                ]);
                            }
                        }

                        $updateData = [
                            'sku' => $variantSku ?? ($variantData['sku'] ?? null),
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
                        // Create new global variant if it doesn't exist (for regular product creation/update)
                        // Skip this for bank products or bank imports where global variants already exist
                        $isBankImport = (isset($data['is_bank_import']) && $data['is_bank_import']) || (isset($data['bank_product_id']) && !empty($data['bank_product_id']));

                        if (!$existingProductVariant && !$isBankImport) {
                            $vendorProduct->product->variants()->create([
                                'variant_configuration_id' => $variantConfigId,
                            ]);
                        }

                        // Prepare variant data with discount logic for creation
                        $hasVariantDiscount = filter_var($variantData['has_discount'] ?? false, FILTER_VALIDATE_BOOLEAN);

                        // Generate SKU if not provided (required field)
                        $sku = $variantData['sku'] ?? null;
                        if (empty($sku)) {
                            // Check if vendor_product_variant_id is provided (for bank imports)
                            if (isset($variantData['vendor_product_variant_id']) && !empty($variantData['vendor_product_variant_id'])) {
                                $variantToGetSku = $vendorProduct->variants()
                                    ->find($variantData['vendor_product_variant_id']);
                                if ($variantToGetSku) {
                                    $baseSku = $variantToGetSku->sku;
                                    // Append vendor slug to SKU
                                    $vendor = $vendorProduct->vendor;
                                    $vendorSlug = $vendor ? $vendor->slug : 'vendor';
                                    $sku = $baseSku . '-' . $vendorSlug;
                                    Log::info('Generated SKU with vendor slug for new variant', [
                                        'vendor_product_variant_id' => $variantData['vendor_product_variant_id'],
                                        'base_sku' => $baseSku,
                                        'vendor_slug' => $vendorSlug,
                                        'final_sku' => $sku
                                    ]);
                                }
                            } else {
                                // Generate SKU: PRODUCT_ID-VARIANT_CONFIG_ID-TIMESTAMP
                                $sku = $vendorProduct->product_id . '-V' . $variantConfigId . '-' . time();
                                Log::info('Generated SKU for variant', [
                                    'generated_sku' => $sku,
                                    'variant_config_id' => $variantConfigId
                                ]);
                            }
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
     * Save bank product stock for a vendor (create or update VendorProduct)
     */
    public function saveBankStock(array $data)
    {
        return DB::transaction(function () use ($data) {
            $productId = $data['product_id'];
            $vendorId = $data['vendor_id'];
            $taxId = $data['tax_id'] ?? null;

            // If no tax_id provided, get the first available tax as default
            if (!$taxId) {
                $defaultTax = Tax::first();
                $taxId = $defaultTax ? $defaultTax->id : null;

                // If still no tax available, throw an error since tax_id is required in database
                if (!$taxId) {
                    throw new \Exception('No tax found in the system. Please create at least one tax before adding products to bank.');
                }
            }

            $vendorProductData = [
                'product_id' => $productId,
                'vendor_id' => $vendorId,
                'tax_id' => $taxId,
                'sku' => Str::random(4),
                'max_per_order' => $data['max_per_order'] ?? 10,
                'video_link' => $data['video_link'] ?? null,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
                'is_featured' => isset($data['is_featured']) ? (bool) $data['is_featured'] : false,
            ];

            if(in_array(auth()->user()->user_type_id, UserType::adminIds())) {
                $vendorProductData['status'] = $data['status'] ?? VendorProduct::STATUS_APPROVED;
            } else {
                $vendorProductData['status'] = VendorProduct::STATUS_PENDING;
            }

            // Check if vendor product already exists (including soft deleted)
            $vendorProduct = VendorProduct::where('product_id', $productId)
                ->where('vendor_id', $vendorId)
                ->withTrashed()
                ->first();

            $sku = null;

            if ($vendorProduct) {
                // Restore if it was deleted
                if ($vendorProduct->trashed()) {
                    $vendorProduct->restore();
                }
                
                // Update existing record
                $vendorProduct->update($vendorProductData);
                $sku = $vendorProduct->sku;
            } else {
                // Generate SKU for new vendor product
                $product = Product::find($productId);
                $sku = $product ? $product->slug : 'VP-' . $productId;
                // Add vendor ID and random string to ensure uniqueness
                $sku = $sku . '-' . $vendorId . '-' . Str::random(4);
                
                $vendorProductData['sku'] = $sku;
                
                // Create new record
                $vendorProduct = VendorProduct::create($vendorProductData);
            }

            $data['configuration_type'] = $vendorProduct->product->configuration_type;
            $data['is_bank_import'] = true; // Flag to indicate this is a bank import operation
            // Use the vendor product SKU for the variant if not provided
            if (!isset($data['sku'])) {
                $data['sku'] = $sku;
            }
            // Handle variants or simple product
            $this->handleProductVariants($vendorProduct, $data);

            return $vendorProduct;
        });
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
        return DB::transaction(function () use ($id, $data) {
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

            // Update configuration type on the Product model
            $configurationType = $data['configuration_type'] ?? 'simple';
            $product->update([
                'configuration_type' => $configurationType
            ]);

            // Use the same handleProductVariants method for consistency
            $this->handleProductVariants($vendorProduct, $data);

            return $vendorProduct->fresh();
        });
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
            'max_per_order' => $vendorProduct->max_per_order,
            'video_link' => $vendorProduct->video_link,
            'is_active' => $vendorProduct->is_active,
            'is_featured' => $vendorProduct->is_featured,
            'offer_date_view' => $vendorProduct->offer_date_view,
            'variants' => $variants
        ];
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

    /**
     * Change vendor product status (approve/reject) with optional bank product assignment
     */
    public function changeVendorProductStatus(int $vendorProductId, array $data)
    {
        return DB::transaction(function () use ($vendorProductId, $data) {
            // $product = Product::findOrFail($productId);
            $vendorProduct = VendorProduct::findOrFail($vendorProductId);

            // Handle bank product assignment on approval
            if ($data['status'] == 'approved' && isset($data['bank_product_id'])) {
                // Verify the bank product exists and is of type bank
                $bankProduct = Product::findOrFail($data['bank_product_id']);

                if ($bankProduct->type !== Product::TYPE_BANK) {
                    throw new \Exception(__('catalogmanagement::product.selected_product_is_not_bank_product'));
                }

                // Get the vendor from the current vendor product
                $vendorId = $vendorProduct->vendor_id;

                // Check if vendor already has this bank product
                $existingVendorProduct = VendorProduct::where('vendor_id', $vendorId)
                    ->where('product_id', $data['bank_product_id'])
                    ->first();

                if ($existingVendorProduct) {
                    throw new \Exception(__('catalogmanagement::product.vendor_already_has_this_bank_product'));
                }

                // Store original vendor product data before deletion
                $originalVendorProductData = [
                    'tax_id' => $vendorProduct->tax_id,
                    'sku' => $vendorProduct->sku,
                    'max_per_order' => $vendorProduct->max_per_order,
                    'is_active' => $vendorProduct->is_active,
                    'is_featured' => $vendorProduct->is_featured,
                ];

                // Delete the current vendor product
                $vendorProduct->delete();

                // Optionally delete the original product if it's not a bank product
                if ($vendorProduct->product->type !== Product::TYPE_BANK) {
                    $vendorProduct->product->delete();
                }

                // Create a new vendor product linking to the bank product
                VendorProduct::create([
                    'vendor_id' => $vendorId,
                    'product_id' => $bankProduct->id,
                    'tax_id' => $originalVendorProductData['tax_id'],
                    'sku' => $originalVendorProductData['sku'],
                    'max_per_order' => $originalVendorProductData['max_per_order'],
                    'is_active' => $originalVendorProductData['is_active'],
                    'is_featured' => $originalVendorProductData['is_featured'],
                    'status' => 'approved',
                    'rejection_reason' => null
                ]);

                return ['message' => __('catalogmanagement::product.vendor_product_replaced_with_bank_product')];
            }

            // Normal status update without bank product assignment
            $vendorProduct->update([
                'status' => $data['status'],
                'rejection_reason' => $data['status'] === 'rejected' ? ($data['rejection_reason'] ?? null) : null
            ]);

            return ['message' => __('catalogmanagement::product.status_updated_successfully')];
        });
    }

    /**
     * Change product activation status (active/inactive)
     */
    public function changeProductActivation(int $vendorProductId, bool $isActive)
    {
        return DB::transaction(function () use ($vendorProductId, $isActive) {
            $vendorProduct = VendorProduct::findOrFail($vendorProductId);

            // Check if status is already set to the requested value
            if ($vendorProduct->is_active === $isActive) {
                throw new \Exception(__('catalogmanagement::product.activation_already_set'));
            }

            $vendorProduct->update([
                'is_active' => $isActive
            ]);

            $statusText = $isActive ? __('common.active') : __('common.inactive');
            return ['message' => __('catalogmanagement::product.activation_updated_to', ['status' => $statusText])];
        });
    }

}

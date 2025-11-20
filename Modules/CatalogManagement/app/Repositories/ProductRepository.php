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
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use App\Models\UserType;
use Illuminate\Support\Facades\Auth;

class ProductRepository implements ProductInterface
{
    public function getAllProducts(array $filters = [], int $perPage = 10)
    {
        $query = Product::with(['brand', 'category', 'variants', 'translations'])->filter($filters);
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
            'variants.variantConfiguration',
        ])->where('product_id', $id)->firstOrFail();
    }

    public function createProduct(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Determine vendor_id based on user role
            $vendorId = $this->determineVendorId($data);

            // Determine product type and status based on user role
            $currentUser = Auth::user();
            $userType = $currentUser->user_type_id;

            if (in_array($userType, UserType::vendorIds())) {
                $isVendorCreated = true;
            } else {
                $isVendorCreated = false;
            }
            $status = $isVendorCreated ? 'pending' : 'approved';

            // Create product with temporary slug
            $product = Product::create([
                'slug' => Str::random(8), // Temporary slug
                'is_active' => $data['is_active'] ?? true,
                'configuration_type' => $data['configuration_type'],
                'status' => $status,
                'vendor_id' => $vendorId,
                'brand_id' => $data['brand_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'created_by_user_id' => $currentUser->id,
            ]);

            // Store translations
            $this->storeTranslations($product, $data);

            // Generate proper slug after translations are saved
            // $product->regenerateSlug();

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

            // Determine vendor_id based on user role
            $vendorId = $this->determineVendorId($data);

            // Get current user for status determination
            $currentUser = Auth::user();
            $userType = $currentUser->user_type_id;

            // Determine if status should change (only if vendor is editing)
            if (in_array($userType, UserType::vendorIds())) {
                // Vendor editing: set status to pending for re-approval
                $status = 'pending';
            } else {
                // Admin editing: keep current status or set from data
                $status = $data['status'] ?? $product->status;
            }

            // Update product
            $product->update([
                'is_active' => $data['is_active'] ?? true,
                'configuration_type' => $data['configuration_type'],
                'status' => $status,
                'vendor_id' => $vendorId,
                'brand_id' => $data['brand_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'sub_category_id' => $data['sub_category_id'] ?? null,
            ]);

            // Update translations
            $this->storeTranslations($product, $data);

            // Regenerate slug after translations are updated
            // $product->regenerateSlug();

            // Handle main image update
            $this->handleMainImage($product, $data);

            // Handle additional images
            $this->handleAdditionalImages($product, $data);

            // Handle variants (unified method for both create and update)
            $this->handleProductVariants($product, $data);

            return $product;
        });
    }

    public function deleteProduct(int $id)
    {
        return DB::transaction(function () use ($id) {
            $vendorProduct = VendorProduct::with(['product.attachments', 'variants'])->findOrFail($id);

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
    protected function handleProductVariants(Product $product, array $data): void
    {
        $configurationType = $data['configuration_type'] ?? 'simple';
        $vendorId = $product->vendor_id;

        // Get or create VendorProduct (handles both create and update)
        $vendorProduct = VendorProduct::firstOrCreate(
            ['vendor_id' => $vendorId, 'product_id' => $product->id],
            [
                'tax_id' => $data['tax_id'],
                'sku' => $data['sku'],
                'points' => $data['points'] ?? 0,
                'max_per_order' => $data['max_per_order'],
                'is_active' => $data['is_active'] ?? false,
                'is_featured' => $data['is_featured'] ?? false,
            ]
        );

        // Update VendorProduct fields (in case it already existed)
        $vendorProduct->update([
            'tax_id' => $data['tax_id'],
            'sku' => $data['sku'],
            'points' => $data['points'] ?? 0,
            'max_per_order' => $data['max_per_order'],
            'is_active' => $data['is_active'] ?? false,
            'is_featured' => $data['is_featured'] ?? false,
        ]);
        if ($configurationType === 'simple') {
            // Handle simple product variant (update or create)
            $existingVariant = $vendorProduct->variants()->first();

            if ($existingVariant) {
                // Update existing variant
                $existingVariant->update([
                    'sku' => $data['sku'],
                    'price' => ($data['price'] ?? 0),
                    'has_offer' => $data['has_discount'] ?? false,
                    'price_before_discount' => isset($data['price_before_discount']) ? $data['price_before_discount'] : 0,
                    'offer_end_date' => $data['offer_end_date'] ?? null,
                ]);
                $vendorProductVariant = $existingVariant;
            } else {
                // Create new variant
                $vendorProductVariant = $vendorProduct->variants()->create([
                    'sku' => $data['sku'],
                    'price' => ($data['price'] ?? 0),
                    'has_offer' => $data['has_discount'] ?? false,
                    'price_before_discount' => isset($data['price_before_discount']) ? $data['price_before_discount'] : 0,
                    'offer_end_date' => $data['offer_end_date'] ?? null,
                ]);
            }

            // Sync stocks for simple product
            $this->syncVariantStocks($vendorProductVariant, $data['stocks'] ?? []);
        } else {
            // Handle variants with multiple configurations
            if (isset($data['variants']) && is_array($data['variants'])) {
                $incomingVariantIds = [];

                foreach ($data['variants'] as $variantData) {
                    $variantConfigId = $variantData['value_id'] ?? null;

                    if (!$variantConfigId) {
                        continue;
                    }

                    // Find existing variant by configuration ID
                    $existingProductVariant = $product->variants()
                        ->where('variant_configuration_id', $variantConfigId)
                        ->first();

                    $existingVendorVariant = $vendorProduct->variants()
                        ->where('variant_configuration_id', $variantConfigId)
                        ->first();

                    if ($existingVendorVariant) {
                        // Update existing vendor variant
                        $existingVendorVariant->update([
                            'sku' => $variantData['sku'] ?? null,
                            'price' => $variantData['price'] ?? 0,
                            'has_discount' => $variantData['has_discount'] ?? false,
                            'price_before_discount' => $variantData['price_before_discount'] ?? 0,
                            'offer_end_date' => $variantData['offer_end_date'] ?? null,
                        ]);
                        $vendorProductVariant = $existingVendorVariant;
                        $incomingVariantIds[] = $existingVendorVariant->id;
                    } else {
                        // Create new global variant if it doesn't exist
                        if (!$existingProductVariant) {
                            $product->variants()->create([
                                'variant_configuration_id' => $variantConfigId,
                            ]);
                        }

                        // Create new vendor variant
                        $vendorProductVariant = $vendorProduct->variants()->create([
                            'variant_configuration_id' => $variantConfigId,
                            'sku' => $variantData['sku'] ?? null,
                            'price' => $variantData['price'] ?? 0,
                            'has_discount' => $variantData['has_discount'] ?? false,
                            'price_before_discount' => $variantData['price_before_discount'] ?? 0,
                            'offer_end_date' => $variantData['offer_end_date'] ?? null,
                        ]);
                        $incomingVariantIds[] = $vendorProductVariant->id;
                    }

                    // Sync stocks for this variant
                    $this->syncVariantStocks($vendorProductVariant, $variantData['stock'] ?? []);
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
     */
    public function updateStockAndPricing($id, array $data)
    {
        $product = $this->getProductById($id);

        if (!$product) {
            throw new \Exception('Product not found');
        }

        $configurationType = $product->product ? $product->product->configuration_type : $product->configuration_type;

        if ($configurationType === 'simple') {
            $this->updateSimpleProductStockPricing($product, $data);
        } else {
            $this->updateVariantProductStockPricing($product, $data);
        }

        return $product;
    }

    /**
     * Update simple product stock and pricing
     */
    private function updateSimpleProductStockPricing($product, $data)
    {
        // Get the first (and only) variant for simple products
        $variant = $product->variants->first();

        if ($variant) {
            // Update variant pricing
            $variant->update([
                'price' => $data['price'] ?? $variant->price,
                'has_offer' => isset($data['has_discount']) ? (bool)$data['has_discount'] : $variant->has_offer,
                'price_before_discount' => $data['price_before_discount'] ?? $variant->price_before_discount,
                'offer_end_date' => $data['offer_end_date'] ?? $variant->offer_end_date,
            ]);

            // Update stock data
            if (isset($data['stocks']) && is_array($data['stocks'])) {
                // Delete existing stocks
                $variant->stocks()->delete();

                // Create new stock entries
                foreach ($data['stocks'] as $stockData) {
                    if (!empty($stockData['region_id']) && isset($stockData['quantity'])) {
                        $variant->stocks()->create([
                            'region_id' => $stockData['region_id'],
                            'stock' => $stockData['quantity']
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Update variant product stock and pricing
     */
    private function updateVariantProductStockPricing($product, $data)
    {
        if (isset($data['variants']) && is_array($data['variants'])) {
            foreach ($data['variants'] as $variantIndex => $variantData) {
                // Find the variant by index or ID
                $variant = null;
                if (isset($variantData['id'])) {
                    $variant = $product->variants->where('id', $variantData['id'])->first();
                } else {
                    $variant = $product->variants->skip($variantIndex)->first();
                }

                if ($variant) {
                    // Update variant pricing
                    $variant->update([
                        'price' => $variantData['price'] ?? $variant->price,
                        'has_offer' => isset($variantData['has_discount']) ? (bool)$variantData['has_discount'] : $variant->has_offer,
                        'price_before_discount' => $variantData['price_before_discount'] ?? $variant->price_before_discount,
                        'offer_end_date' => $variantData['offer_end_date'] ?? $variant->offer_end_date,
                    ]);

                    // Update stock data for this variant
                    if (isset($variantData['stocks']) && is_array($variantData['stocks'])) {
                        // Delete existing stocks for this variant
                        $variant->stocks()->delete();

                        // Create new stock entries
                        foreach ($variantData['stocks'] as $stockData) {
                            if (!empty($stockData['region_id']) && isset($stockData['quantity'])) {
                                $variant->stocks()->create([
                                    'region_id' => $stockData['region_id'],
                                    'stock' => $stockData['quantity']
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}

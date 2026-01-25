<?php

namespace Modules\CatalogManagement\app\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

/**
 * Products Import for Vendor Products
 * 
 * Structure:
 * - products: Bank products (catalog)
 * - vendor_products: Vendor's products with SKU
 * - vendor_product_variants: Variants with SKU and price
 * - vendor_product_variant_stocks: Stock per region
 * - occasions: Occasions (admin only, optional)
 * - occasion_products: Products in occasions (admin only, optional)
 */
class ProductsImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public array $productMap  = []; // excel_product_id -> db_product_id
    public array $vendorProductMap = []; // excel_product_id -> db_vendor_product_id
    public array $variantMap  = []; // excel_variant_id -> db_vendor_product_variant_id
    public array $occasionMap = []; // excel_occasion_id -> db_occasion_id
    public array $errors = []; // Import errors
    public array $productsWithVariants = []; // Track products that have_varient = yes
    
    protected bool $isAdmin;

    public function __construct(bool $isAdmin = false)
    {
        $this->isAdmin = $isAdmin;
    }

    public function sheets(): array
    {
        $sheets = [
            'products'          => new ProductsSheetImport($this->productMap, $this->vendorProductMap, $this->errors, $this->productsWithVariants, $this->isAdmin),
            'images'            => new ImagesSheetImport($this->productMap, $this->errors, $this->isAdmin),
            'variants'          => new VariantsSheetImport($this->vendorProductMap, $this->variantMap, $this->errors, $this->isAdmin),
            'variant_stock'     => new VariantStockSheetImport($this->variantMap, $this->errors, $this->isAdmin),
        ];

        // Occasions sheets are optional for admin imports
        // If the sheets don't exist in the Excel file, they will be skipped
        if ($this->isAdmin) {
            $sheets['occasions'] = new OccasionsSheetImport($this->occasionMap, $this->errors, $this->isAdmin);
            $sheets['occasion_products'] = new OccasionProductsSheetImport($this->occasionMap, $this->variantMap, $this->errors, $this->isAdmin);
        }
        
        return $sheets;
    }

    public function onUnknownSheet($sheetName)
    {
        // Silently skip unknown sheets - this allows occasions sheets to be optional
        // No action needed, just implement the method to satisfy SkipsUnknownSheets
    }

    /**
     * Get count of imported products
     */
    public function getImportedCount(): int
    {
        return count($this->productMap);
    }

    /**
     * Get import errors from all sheets
     */
    public function getErrors(): array
    {
        // After all sheets are processed, validate products with variants
        $this->validateProductsWithVariants();
        
        return $this->errors;
    }

    /**
     * Validate that products with have_varient = yes have variants and variant stock
     * 
     * Note: This validation is now optional. Products can have have_varient = yes
     * but still use the variants sheet for simple products (without variant_configuration_id)
     * to manage pricing and stock.
     */
    protected function validateProductsWithVariants(): void
    {
        // Validation removed: Products with have_varient = yes are no longer required
        // to have entries in the variants sheet. They can be simple products that
        // use the variants sheet for pricing/stock management, or complex products
        // with actual variant configurations.
    }
}

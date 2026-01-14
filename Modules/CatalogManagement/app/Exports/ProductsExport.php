<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Products Export for Vendor Products
 * 
 * Structure matches import:
 * - products: Product data with SKU
 * - images: Product images
 * - variants: Product variants with pricing
 * - variant_stock: Stock per region
 */
class ProductsExport implements WithMultipleSheets
{
    protected bool $isAdmin;
    protected array $filters;
    protected bool $includeOccasions;

    public function __construct(bool $isAdmin = false, array $filters = [], bool $includeOccasions = false)
    {
        $this->isAdmin = $isAdmin;
        $this->filters = $filters;
        $this->includeOccasions = $includeOccasions;
    }

    public function sheets(): array
    {
        $sheets = [
            new ProductsSheetExport($this->isAdmin, $this->filters),
            new ImagesSheetExport($this->isAdmin, $this->filters),
            new VariantsSheetExport($this->isAdmin, $this->filters),
            new VariantStockSheetExport($this->isAdmin, $this->filters),
        ];

        // Occasions sheets are optional (only if explicitly requested)
        if ($this->isAdmin && $this->includeOccasions) {
            $sheets[] = new OccasionsSheetExport($this->isAdmin, $this->filters);
            $sheets[] = new OccasionProductsSheetExport($this->isAdmin, $this->filters);
        }
        
        return $sheets;
    }
}

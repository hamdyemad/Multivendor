<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

/**
 * Sheet: variants
 * Exports product variants with pricing
 */
class VariantsSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected bool $isAdmin;
    protected $vendorProducts;
    protected array $productIdMapping;

    public function __construct(bool $isAdmin = false, $vendorProducts = null, array $productIdMapping = [])
    {
        $this->isAdmin = $isAdmin;
        $this->vendorProducts = $vendorProducts;
        $this->productIdMapping = $productIdMapping;
    }

    public function collection()
    {
        // Extract all variants from vendor products
        $variants = new Collection();
        
        foreach ($this->vendorProducts as $vendorProduct) {
            foreach ($vendorProduct->variants as $variant) {
                $variants->push($variant);
            }
        }
        
        return $variants;
    }

    public function headings(): array
    {
        return [
            'product_id',
            'sku',
            'variant_configuration_id',
            'price',
            'has_discount',
            'price_before_discount',
            'discount_end_date',
            'tax_id',
        ];
    }

    public function map($variant): array
    {
        // Use the incremental index from the mapping
        $productId = $this->productIdMapping[$variant->vendor_product_id] ?? $variant->vendor_product_id;
        
        // Determine if has discount
        $hasDiscount = !empty($variant->price_before_discount) && $variant->price_before_discount > $variant->price;
        
        return [
            $productId,
            $variant->sku ?? '',
            $variant->variant_configuration_id ?? '',
            $variant->price ?? 0,
            $hasDiscount ? 'yes' : 'no',
            $variant->price_before_discount ?? '',
            $variant->offer_end_date ? $variant->offer_end_date->format('Y-m-d') : '',
            $variant->tax_id ?? '',
        ];
    }

    public function title(): string
    {
        return 'variants';
    }
}

<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

/**
 * Sheet: variant_stock
 * Exports variant stock per region
 */
class VariantStockSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
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
        // Extract all variant stocks from vendor products
        $stocks = new Collection();
        
        foreach ($this->vendorProducts as $vendorProduct) {
            foreach ($vendorProduct->variants as $variant) {
                foreach ($variant->stocks as $stock) {
                    $stocks->push($stock);
                }
            }
        }
        
        return $stocks;
    }

    public function headings(): array
    {
        return [
            'variant_sku',
            'region_id',
            'stock',
        ];
    }

    public function map($stock): array
    {
        return [
            $stock->vendorProductVariant->sku ?? '',
            $stock->region_id,
            $stock->quantity ?? 0,
        ];
    }

    public function title(): string
    {
        return 'variant_stock';
    }
}

<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Modules\CatalogManagement\app\Models\OccasionProduct;

/**
 * Sheet: occasion_products
 * Exports products in occasions (admin only)
 */
class OccasionProductsSheetExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected bool $isAdmin;
    protected array $filters;

    public function __construct(bool $isAdmin = false, array $filters = [])
    {
        $this->isAdmin = $isAdmin;
        $this->filters = $filters;
    }

    public function query()
    {
        return OccasionProduct::with(['occasion', 'vendorProductVariant'])
            ->orderBy('occasion_id')
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'occasion_id',
            'variant_id',
            'special_price',
        ];
    }

    public function map($occasionProduct): array
    {
        return [
            $occasionProduct->occasion_id,
            $occasionProduct->vendor_product_variant_id,
            $occasionProduct->special_price ?? '',
        ];
    }

    public function title(): string
    {
        return 'occasion_products';
    }
}

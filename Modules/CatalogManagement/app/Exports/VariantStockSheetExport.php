<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use Illuminate\Support\Facades\Auth;

/**
 * Sheet: variant_stock
 * Exports variant stock per region
 */
class VariantStockSheetExport implements FromQuery, WithHeadings, WithMapping, WithTitle
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
        $query = VendorProductVariantStock::with([
            'vendorProductVariant.vendorProduct',
            'region'
        ]);

        // Filter by vendor if not admin
        if (!$this->isAdmin) {
            $vendorId = Auth::user()->vendor?->id;
            if ($vendorId) {
                $query->whereHas('vendorProductVariant.vendorProduct', function($q) use ($vendorId) {
                    $q->where('vendor_id', $vendorId);
                });
            }
        }

        // Apply additional filters
        if (!empty($this->filters['vendor_id'])) {
            $query->whereHas('vendorProductVariant.vendorProduct', function($q) {
                $q->where('vendor_id', $this->filters['vendor_id']);
            });
        }

        return $query->orderBy('vendor_product_variant_id')->orderBy('region_id');
    }

    public function headings(): array
    {
        return [
            'variant_id',
            'region_id',
            'quantity',
        ];
    }

    public function map($stock): array
    {
        return [
            $stock->vendor_product_variant_id,
            $stock->region_id,
            $stock->quantity ?? 0,
        ];
    }

    public function title(): string
    {
        return 'variant_stock';
    }
}

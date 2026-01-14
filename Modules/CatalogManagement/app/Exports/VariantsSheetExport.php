<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Illuminate\Support\Facades\Auth;

/**
 * Sheet: variants
 * Exports product variants with pricing
 */
class VariantsSheetExport implements FromQuery, WithHeadings, WithMapping, WithTitle
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
        $query = VendorProductVariant::with([
            'vendorProduct',
            'variantConfiguration.key'
        ]);

        // Filter by vendor if not admin
        if (!$this->isAdmin) {
            $vendorId = Auth::user()->vendor?->id;
            if ($vendorId) {
                $query->whereHas('vendorProduct', function($q) use ($vendorId) {
                    $q->where('vendor_id', $vendorId);
                });
            }
        }

        // Apply additional filters
        if (!empty($this->filters['vendor_id'])) {
            $query->whereHas('vendorProduct', function($q) {
                $q->where('vendor_id', $this->filters['vendor_id']);
            });
        }

        return $query->orderBy('vendor_product_id')->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'id',
            'product_id',
            'variant_sku',
            'variant_configuration_id',
            'price',
            'price_before_discount',
            'offer_end_date',
            'tax_id',
        ];
    }

    public function map($variant): array
    {
        return [
            $variant->id,
            $variant->vendor_product_id,
            $variant->variant_sku ?? '',
            $variant->variant_configuration_id ?? '',
            $variant->price ?? 0,
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

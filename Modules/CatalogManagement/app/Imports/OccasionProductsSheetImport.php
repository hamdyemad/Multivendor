<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\Occasion;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\OccasionProduct;

/**
 * Sheet: occasion_products
 * Links VendorProductVariants to Occasions with special prices (admin only)
 */
class OccasionProductsSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function __construct(
        protected array &$occasionMap,
        protected array &$variantMap,
        protected array &$importErrors = [],
        protected bool $isAdmin = false
    ) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $excelOccasionId = (int)($row['occasion_id'] ?? 0);
            $variantSku = trim((string)($row['variant_sku'] ?? ''));

            $validator = Validator::make($row->toArray(), [
                'occasion_id' => 'required|integer|min:1',
                'variant_sku' => 'required|string|max:255',
                'special_price' => 'nullable|numeric|min:0',
                'position' => 'nullable|integer|min:0',
            ], [
                'occasion_id.required' => __('validation.required', ['attribute' => 'occasion_id']),
                'variant_sku.required' => __('validation.required', ['attribute' => 'variant_sku']),
                'special_price.numeric' => __('validation.numeric', ['attribute' => 'special_price']),
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'occasion_products',
                    'row' => $index + 2,
                    'occasion_id' => $excelOccasionId,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($excelOccasionId <= 0 || $variantSku === '') {
                $this->importErrors[] = [
                    'sheet' => 'occasion_products',
                    'row' => $index + 2,
                    'occasion_id' => $excelOccasionId,
                    'errors' => [__('catalogmanagement::product.invalid_occasion_or_variant_id')]
                ];
                continue;
            }

            if (!isset($this->occasionMap[$excelOccasionId])) {
                $this->importErrors[] = [
                    'sheet' => 'occasion_products',
                    'row' => $index + 2,
                    'occasion_id' => $excelOccasionId,
                    'errors' => [__('catalogmanagement::product.occasion_not_found_or_skipped')]
                ];
                continue;
            }

            if (!isset($this->variantMap[$variantSku])) {
                $this->importErrors[] = [
                    'sheet' => 'occasion_products',
                    'row' => $index + 2,
                    'sku' => $variantSku,
                    'errors' => [__('catalogmanagement::product.variant_not_found_or_skipped')]
                ];
                continue;
            }

            $dbOccasionId = $this->occasionMap[$excelOccasionId];
            $dbVariantId = $this->variantMap[$variantSku];

            $occasion = Occasion::find($dbOccasionId);
            $variant = VendorProductVariant::find($dbVariantId);

            if (!$occasion || !$variant) {
                continue;
            }

            OccasionProduct::updateOrCreate(
                [
                    'occasion_id' => $dbOccasionId,
                    'vendor_product_variant_id' => $dbVariantId
                ],
                [
                    'special_price' => !empty($row['special_price']) ? $this->normalizeDecimal($row['special_price']) : null,
                    'position' => (int)($row['position'] ?? 0)
                ]
            );
        }
    }

    private function normalizeDecimal($value): float
    {
        return (float)($value ?? 0);
    }
}

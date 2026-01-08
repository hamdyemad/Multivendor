<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;

/**
 * Sheet: variants
 * Creates VendorProductVariant entries
 */
class VariantsSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected array $variantSkus = [];

    public function __construct(
        protected array &$vendorProductMap,
        protected array &$variantMap,
        protected array &$importErrors = [],
        protected bool $isAdmin = false
    ) {}

    public function collection(Collection $rows)
    {
        $rowCounter = 0;
        foreach ($rows as $index => $row) {
            $rowCounter++;
            $excelProductId = (int)($row['product_id'] ?? 0);
            $sku = $this->normalizeSku($row['sku'] ?? '');

            $validator = Validator::make($row->toArray(), [
                'product_id' => 'required|integer|min:1',
                'sku' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'variant_configuration_id' => 'nullable|integer|exists:variants_configurations,id',
                'has_offer' => 'nullable|in:0,1,true,false,yes,no',
                'price_before_discount' => 'nullable|numeric|min:0',
                'offer_end_date' => 'nullable|date',
            ], [
                'product_id.required' => __('validation.required', ['attribute' => 'product_id']),
                'product_id.integer' => __('validation.integer', ['attribute' => 'product_id']),
                'sku.required' => __('validation.required', ['attribute' => 'sku']),
                'price.required' => __('validation.required', ['attribute' => 'price']),
                'price.numeric' => __('validation.numeric', ['attribute' => 'price']),
                'variant_configuration_id.integer' => __('validation.integer', ['attribute' => 'variant_configuration_id']),
                'variant_configuration_id.exists' => __('validation.exists', ['attribute' => 'variant_configuration_id']),
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($excelProductId <= 0 || $sku === '') {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => [__('catalogmanagement::product.invalid_product_variant_or_sku')]
                ];
                continue;
            }

            // Check for duplicate SKU in Excel
            if (isset($this->variantSkus[$sku])) {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => [__('catalogmanagement::product.duplicate_variant_sku_in_excel', ['row' => $this->variantSkus[$sku]])]
                ];
                continue;
            }

            if (!isset($this->vendorProductMap[$excelProductId])) {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => [__('catalogmanagement::product.product_not_found_or_skipped')]
                ];
                continue;
            }

            $vendorProductId = $this->vendorProductMap[$excelProductId];
            $vendorProduct = VendorProduct::find($vendorProductId);
            
            if (!$vendorProduct) {
                continue;
            }

            // Check if variant SKU already exists (unique across all variants)
            $existingVariant = VendorProductVariant::where('sku', $sku)->first();
                
            if ($existingVariant) {
                $this->importErrors[] = [
                    'sheet' => 'variants',
                    'row' => $index + 2,
                    'sku' => $sku,
                    'errors' => [__('catalogmanagement::product.variant_sku_already_exists')]
                ];
                continue;
            }

            $this->variantSkus[$sku] = $index + 2;

            $hasOffer = $this->normalizeYesNo($row['has_offer'] ?? '0') === 'yes';

            $variant = VendorProductVariant::create([
                'vendor_product_id' => $vendorProductId,
                'variant_configuration_id' => !empty($row['variant_configuration_id']) ? (int)$row['variant_configuration_id'] : null,
                'sku' => $sku,
                'price' => $this->normalizeDecimal($row['price'] ?? 0),
                'has_offer' => $hasOffer,
                'price_before_discount' => $hasOffer ? $this->normalizeDecimal($row['price_before_discount'] ?? 0) : 0,
                'offer_end_date' => $hasOffer && !empty($row['offer_end_date']) ? $row['offer_end_date'] : null,
            ]);

            // Map by SKU instead of Excel ID
            $this->variantMap[$sku] = (int)$variant->id;
        }
    }

    private function normalizeYesNo($value): string
    {
        $v = strtolower(trim((string)$value));
        return in_array($v, ['1', 'true', 'yes', 'y'], true) ? 'yes' : 'no';
    }

    private function normalizeSku($value): string
    {
        $sku = trim((string)$value);
        $sku = preg_replace('/\s+/', ' ', $sku);
        return trim($sku);
    }

    private function normalizeDecimal($value): float
    {
        return (float)($value ?? 0);
    }
}

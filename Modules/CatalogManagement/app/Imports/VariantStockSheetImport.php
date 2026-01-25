<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;

/**
 * Sheet: variant_stock
 * Creates VendorProductVariantStock entries
 */
class VariantStockSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected array $variantsWithStock = [];
    protected array $processedStockByVariant = []; // Track which stock entries were processed for each variant

    public function __construct(
        protected array &$variantMap,
        protected array &$importErrors = [],
        protected bool $isAdmin = false
    ) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $variantSku = trim((string)($row['variant_sku'] ?? ''));
            $regionId = (int)($row['region_id'] ?? 0);
            $quantity = (int)($row['stock'] ?? 0);
            
            // Normalize variant_sku in the row data for validation
            $rowData = $row->toArray();
            $rowData['variant_sku'] = $variantSku;

            $validator = Validator::make($rowData, [
                'variant_sku' => 'required|string|max:255',
                'region_id' => 'required|integer|exists:regions,id',
                'stock' => 'required|integer|min:0',
            ], [
                'variant_sku.required' => __('validation.required', ['attribute' => 'variant_sku']),
                'variant_sku.string' => __('validation.string', ['attribute' => 'variant_sku']),
                'region_id.required' => __('validation.required', ['attribute' => 'region_id']),
                'region_id.exists' => __('validation.exists', ['attribute' => 'region_id']),
                'stock.required' => __('validation.required', ['attribute' => 'stock']),
                'stock.integer' => __('validation.integer', ['attribute' => 'stock']),
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'variant_stock',
                    'row' => $index + 2,
                    'sku' => $variantSku,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($variantSku === '' || $regionId <= 0) {
                $this->importErrors[] = [
                    'sheet' => 'variant_stock',
                    'row' => $index + 2,
                    'sku' => $variantSku,
                    'errors' => [__('catalogmanagement::product.invalid_variant_or_region_id')]
                ];
                continue;
            }

            if (!isset($this->variantMap[$variantSku])) {
                $this->importErrors[] = [
                    'sheet' => 'variant_stock',
                    'row' => $index + 2,
                    'sku' => $variantSku,
                    'errors' => [__('catalogmanagement::product.variant_not_found_or_skipped')]
                ];
                continue;
            }

            $dbVariantId = $this->variantMap[$variantSku];
            $variant = VendorProductVariant::find($dbVariantId);
            
            if (!$variant) {
                continue;
            }

            // Track this variant as having stock processed
            if (!isset($this->processedStockByVariant[$dbVariantId])) {
                $this->processedStockByVariant[$dbVariantId] = [];
            }

            $stock = VendorProductVariantStock::updateOrCreate(
                [
                    'vendor_product_variant_id' => $dbVariantId,
                    'region_id' => $regionId
                ],
                [
                    'quantity' => $quantity
                ]
            );

            // Track this stock entry as processed
            $this->processedStockByVariant[$dbVariantId][] = $stock->id;
            $this->variantsWithStock[$variantSku] = true;
        }

        // Validate that all variants have stock entries
        $this->validateVariantsHaveStock();
        
        // After processing all rows, delete stock entries that weren't in the Excel file
        $this->deleteUnprocessedStock();
    }

    protected function validateVariantsHaveStock(): void
    {
        foreach ($this->variantMap as $variantSku => $dbVariantId) {
            if (!isset($this->variantsWithStock[$variantSku])) {
                $this->importErrors[] = [
                    'sheet' => 'variant_stock',
                    'row' => '-',
                    'sku' => $variantSku,
                    'errors' => [__('catalogmanagement::product.variant_has_no_stock_entries')]
                ];
            }
        }
    }
    
    /**
     * Delete stock entries that exist in the database but weren't in the Excel file
     * This ensures the Excel file is the source of truth for stock
     */
    private function deleteUnprocessedStock(): void
    {
        foreach ($this->processedStockByVariant as $variantId => $processedStockIds) {
            // Get all existing stock entries for this variant
            $existingStocks = VendorProductVariantStock::where('vendor_product_variant_id', $variantId)->get();
            
            foreach ($existingStocks as $stock) {
                // If this stock entry wasn't processed (not in Excel), delete it
                if (!in_array($stock->id, $processedStockIds)) {
                    $stock->delete();
                }
            }
        }
    }
}

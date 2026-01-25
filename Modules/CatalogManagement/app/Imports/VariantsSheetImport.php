<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use App\Models\ActivityLog;

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
                'has_discount' => 'nullable|in:0,1,true,false,yes,no',
                'price_before_discount' => 'nullable|numeric|min:0',
                'discount_end_date' => 'nullable|date',
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

            // Check if variant SKU already exists - if so, update instead of creating new
            $existingVariant = VendorProductVariant::where('sku', $sku)->first();
                
            if ($existingVariant) {
                // For vendors, only allow updating variants of their own products
                if (!$this->isAdmin) {
                    $existingVendorProduct = $existingVariant->vendorProduct;
                    if ($existingVendorProduct && $existingVendorProduct->vendor_id != $vendorProduct->vendor_id) {
                        $this->importErrors[] = [
                            'sheet' => 'variants',
                            'row' => $index + 2,
                            'sku' => $sku,
                            'errors' => [__('catalogmanagement::product.variant_sku_belongs_to_another_vendor')]
                        ];
                        continue;
                    }
                }

                // Check if the variant belongs to the correct product
                // If product_id in Excel matches the existing variant's product, update it
                // Otherwise, this is a conflict - SKU exists but for a different product
                if ($existingVariant->vendor_product_id != $vendorProductId) {
                    // SKU exists but belongs to a different product
                    // For now, we'll update the variant to belong to the new product
                    // This allows moving variants between products via import
                    $existingVariant->vendor_product_id = $vendorProductId;
                }

                // Store old data for activity log
                $oldVariantData = $existingVariant->toArray();

                // Update existing variant
                $hasDiscount = $this->normalizeYesNo($row['has_discount'] ?? '0') === 'yes';
                $existingVariant->update([
                    'vendor_product_id' => $vendorProductId, // Update product association
                    'variant_configuration_id' => !empty($row['variant_configuration_id']) ? (int)$row['variant_configuration_id'] : $existingVariant->variant_configuration_id,
                    'price' => $this->normalizeDecimal($row['price'] ?? $existingVariant->price),
                    'has_discount' => $hasDiscount,
                    'price_before_discount' => $hasDiscount ? $this->normalizeDecimal($row['price_before_discount'] ?? 0) : 0,
                    'discount_end_date' => $hasDiscount && !empty($row['discount_end_date']) ? $row['discount_end_date'] : null,
                ]);

                // Log activity for variant update
                $this->logBulkActivity('updated', $existingVariant, $oldVariantData, $existingVariant->fresh()->toArray());

                // Map to existing ID
                $this->variantMap[$sku] = (int)$existingVariant->id;
                $this->variantSkus[$sku] = $index + 2;
                continue;
            }

            $this->variantSkus[$sku] = $index + 2;

            $hasDiscount = $this->normalizeYesNo($row['has_discount'] ?? '0') === 'yes';

            $variant = VendorProductVariant::create([
                'vendor_product_id' => $vendorProductId,
                'variant_configuration_id' => !empty($row['variant_configuration_id']) ? (int)$row['variant_configuration_id'] : null,
                'sku' => $sku,
                'price' => $this->normalizeDecimal($row['price'] ?? 0),
                'has_discount' => $hasDiscount,
                'price_before_discount' => $hasDiscount ? $this->normalizeDecimal($row['price_before_discount'] ?? 0) : 0,
                'discount_end_date' => $hasDiscount && !empty($row['discount_end_date']) ? $row['discount_end_date'] : null,
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

    /**
     * Log activity for bulk import operations
     */
    private function logBulkActivity(string $action, $model, array $oldData = [], array $newData = []): void
    {
        try {
            $modelName = class_basename($model);
            $identifier = $model->id;
            
            $descriptionKeys = [
                'created' => 'activity_log.created_model',
                'updated' => 'activity_log.updated_model',
            ];

            $properties = [];
            if ($action === 'updated' && !empty($oldData) && !empty($newData)) {
                // Get only changed values
                $changes = array_diff_assoc($newData, $oldData);
                $oldValues = array_intersect_key($oldData, $changes);
                
                if (!empty($changes)) {
                    $properties = [
                        'old' => $oldValues,
                        'new' => $changes,
                        'source' => 'bulk_upload',
                    ];
                }
            } elseif ($action === 'created') {
                $properties = [
                    'source' => 'bulk_upload',
                ];
            }

            // Only log if there are actual changes or it's a create action
            if ($action === 'created' || !empty($properties['new'])) {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'description_key' => $descriptionKeys[$action] ?? null,
                    'description_params' => [
                        'model' => $modelName,
                        'identifier' => $identifier,
                    ],
                    'properties' => $properties,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'country_id' => session('country_id'),
                ]);
            }
        } catch (\Exception $e) {
            // Silent fail - don't break import for logging errors
            \Illuminate\Support\Facades\Log::error('Bulk import activity log error: ' . $e->getMessage());
        }
    }
}

<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Http\Resources\Api\TaxResource;
use Modules\CatalogManagement\app\Http\Resources\Api\OccasionResource;
use Modules\CatalogManagement\app\Http\Resources\Api\SimpleBundleResource;
use Modules\CatalogManagement\app\Models\BundleProduct;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $limit = $this->limitation();
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'product' => CartProductResource::make($this->vendorProductVariant),
            'type' => $this->type,
            'bundle' => ($this->bundle && $this->type === "bundle") ? new SimpleBundleResource($this->bundle) : null,
            'occasion' => ($this->occasion && $this->type === "occasion") ? new OccasionResource($this->occasion) : null,
            'limitation' => $limit[0],
            'min' => $limit[1],
            'price' => round($this->price(), 2),
            'taxes' => TaxResource::collection($this->vendorProduct->taxes),
        ];
    }

    /**
     * Get limitation based on cart type
     */
    private function limitation()
    {
        if ($this->type === 'bundle') {
            $bundleProduct = BundleProduct::where(function ($query) {
                $query->where('bundle_id', $this->bundle_id)
                    ->where('vendor_product_variant_id', $this->vendor_product_variant_id);
            })->first();
            return [
                $bundleProduct?->limitation_quantity,
                $bundleProduct?->min_quantity
            ];
        }


        return [$this->vendorProductVariant->max_per_order, 0];
    }

    private function price()
    {
        if ($this->type === 'bundle' && $this->bundle_id) {
            // Query database directly to get bundle product price
            $bundleProduct = BundleProduct::where('bundle_id', $this->bundle_id)
                ->where('vendor_product_variant_id', $this->vendor_product_variant_id)
                ->first();
            return $bundleProduct?->price ?? $this->vendorProductVariant->price ?? 0;
        }

        if ($this->type === 'occasion' && $this->occasion_id) {
            // Query database directly to get occasion product price
            $occasionProduct = \Modules\CatalogManagement\app\Models\OccasionProduct::where('occasion_id', $this->occasion_id)
                ->where('vendor_product_variant_id', $this->vendor_product_variant_id)
                ->first();
            return $occasionProduct?->special_price ?? $this->vendorProductVariant->price ?? 0;
        }
        return $this->vendorProductVariant->price ?? 0;
    }
}

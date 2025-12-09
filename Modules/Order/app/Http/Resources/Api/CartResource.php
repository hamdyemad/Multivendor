<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Http\Resources\Api\TaxResource;

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
            // 'bundle' => ($this->bundle && $this->type === "bundle") ? new SimpleBundleResource($this->bundle) : null,
            // 'occasion' => ($this->occasion && $this->type === "occasion") ? new SimpleOccasionResource($this->occasion) : null,
            'limitation' => $limit[0],
            'min' => $limit[1],
            'price' => $this->vendorProductVariant->price ?? 0,
            'taxes' => TaxResource::make($this->vendorProduct->tax),
        ];
    }

    /**
     * Get limitation based on cart type
     */
    private function limitation()
    {
        // if ($this->type === 'bundle') {
        //     return [
        //         $this->bundle->bundleProducts->where('variant_id', $this->vendor_product_variant_id)->first()?->limitation,
        //         $this->bundle->bundleProducts->where('variant_id', $this->vendor_product_variant_id)->first()?->minimum
        //     ];
        // }


        return [$this->vendorProductVariant->max_per_order, 0];
    }
}

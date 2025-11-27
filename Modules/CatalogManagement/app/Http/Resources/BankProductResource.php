<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'configuration_type' => $this->configuration_type,
            'brand' => $this->brand ? $this->brand->name : '',
            'department' => $this->department ? $this->department->name : '',
            'category' => $this->category ? $this->category->name : '',
            'sub_category' => $this->subCategory ? $this->subCategory->name : '',
            'image' => $this->mainImage
                ? asset('storage/' . $this->mainImage->path)
                : '',
            'variants' => BankProductVariantResource::collection($this->variants),
        ];
    }
}

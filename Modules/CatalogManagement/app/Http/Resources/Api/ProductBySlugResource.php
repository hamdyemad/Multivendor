<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightCategoryApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightDepartmentApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightSubCategoryApiResource;
use Modules\Vendor\app\Http\Resources\Api\LightVendorResource;

class ProductBySlugResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this['product'];
        $vendorProducts = $this['vendorProducts'];
        $reviews = $this['reviews'];

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'brand' => $product->brand?->name,
            'category' => new LightCategoryApiResource($product->category),
            'department' => new LightDepartmentApiResource($product->department),
            'sub_category' => new LightSubCategoryApiResource($product->subCategory),
            'reviews' => $reviews,
            'vendors' => $vendorProducts->map(function($vendorProduct) {
                return [
                    'vendor' => new LightVendorResource($vendorProduct->vendor),
                    'vendor_product' => new VendorProductResource($vendorProduct),
                ];
            })->values()
        ];
    }
}

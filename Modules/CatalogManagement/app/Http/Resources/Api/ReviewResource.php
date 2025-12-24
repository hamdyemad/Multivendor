<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\Customer\app\Transformers\CustomerApiResource;
use Modules\Vendor\app\Http\Resources\Api\VendorApiResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Check if reviewable_type contains VendorProduct (handles different namespace formats)
        $isProduct = str_contains($this->reviewable_type, 'VendorProduct');
        
        // Load the reviewable resource
        $reviewable = null;
        if ($isProduct) {
            $product = VendorProduct::with(['product.mainImage', 'product.brand', 'product.department', 'product.category', 'product.subCategory', 'vendor', 'tax'])
                ->withCount('reviews')
                ->withAvg('reviews', 'star')
                ->find($this->reviewable_id);
            $reviewable = $product ? new VendorProductResource($product) : null;
        } else {
            $vendor = \Modules\Vendor\app\Models\Vendor::withoutCountryFilter()
                ->with(['translations', 'country', 'logo', 'banner'])
                ->withCount('reviews')
                ->withAvg('reviews', 'star')
                ->find($this->reviewable_id);
            $reviewable = $vendor ? new VendorApiResource($vendor) : null;
        }
        return [
            'id' => $this->id,
            'reviewable_id' => $this->reviewable_id,
            'reviewable_type' => $isProduct ? "products" : "vendors",
            'customer_id' => $this->customer_id,
            'customer' => new CustomerApiResource($this->customer),
            'review' => $this->review,
            'star' => $this->star,
            'reviewable' => $reviewable,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

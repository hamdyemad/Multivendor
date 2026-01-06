<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\SystemSetting\app\Resources\CurrencyResource;
use Modules\CatalogManagement\app\Http\Resources\Api\TaxResource;
use App\Helpers\PointsHelper;

class SimpleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Calculate points based on minimum variant price
        $price = $this->variants?->min('price') ?? 0;
        $points = PointsHelper::calculatePoints((float) $price);

        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'product_id' => $this->product_id,
            'slug' => $this->product->slug,
            'points' => $points,
            'sku' => $this->sku,
            'star' => $this->average_rating ?? 0,
            'num_of_user_review' => $this->reviews_count ?? 0,
            'limitation' => $this->max_per_order,
            'status' => $this->is_featured ? __('catalogmanagement::product.featured') : __('catalogmanagement::product.active'),
            'image' => formatImage($this->product->mainImage),
            'name' => $this->product->title,
            'details' => $this->product->details,
            'summary' => $this->product->summary,
            'instructions' => $this->product->instructions,
            'features' => $this->product->features,
            'extras' => $this->product->extra_description,
            'matrial' => $this->product->material,
            'video_link' => $this->product->video_link,
            'number_of_sale' => $this->sales,
            'views' => $this->views,
            'stock' => $this->total_stock ?? 0,
            'remaining_stock' => $this->remaining_stock ?? 0,
            'is_fav' => false,
            'configuration_type' => $this->product->configuration_type,
            'tags' => $this->product->tags_array,
            'currency' => CurrencyResource::make($this->product->currency),
            'meta_description' => $this->product->meta_description,
            'meta_keywords' => $this->product->meta_keywords ?? [],
            'taxes' => TaxResource::collection($this->whenLoaded('taxes')),
            'category' => [
                'id' => $this->product->category_id,
                'name' => $this->product->category->name ?? null,
            ],
            'department' => [
                'id' => $this->product->department_id,
                'name' => $this->product->department->name ?? null,
            ],
            'sub_category' => [
                'id' => $this->product->sub_category_id,
                'name' => $this->product->subCategory->name ?? null,
            ],
            'variants' => VendorProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}

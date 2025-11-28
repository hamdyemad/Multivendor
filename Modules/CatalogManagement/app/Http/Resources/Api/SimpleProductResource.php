<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => $this->getProductImage(),
            'name' => $this->title,
            'slug' => $this->slug,
            'points' => $this->points ?? 0,
            'sku' => $this->sku,
            'details' => $this->details,
            'summary' => $this->summary,
            'instructions' => $this->instructions,
            'features' => $this->features,
            'extras' => $this->extra_description,
            'star' => $this->average_rating ?? 0,
            'num_of_user_review' => $this->reviews_count ?? 0,
            'number_of_sale' => $this->sales ?? 0,
            'video_link' => null,
            'stock' => $this->getTotalStock(),
            'views' => $this->views,
            'matrial' => $this->material,
            'shipping' => null,
            'status' => $this->is_active ? 'Active' : 'Inactive',
            'limitation' => 2000,
            'is_fav' => false,
            'size_color_type' => $this->getConfigurationType(),
        ];
    }

    /**
     * Get product image from first attachment
     */
    private function getProductImage(): ?string
    {
        if ($this->relationLoaded('attachments') && $this->attachments->count() > 0) {
            $image = $this->attachments->first();
            return url(asset('storage/' . $image->url));
        }
        return null;
    }

    /**
     * Get total stock from variants
     */
    private function getTotalStock(): int
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->sum('stock') ?? 0;
        }
        return 0;
    }

    /**
     * Get configuration type
     */
    private function getConfigurationType(): string
    {
        if ($this->configuration_type === 'with_variants') {
            return 'with_variants';
        }
        return 'without_any';
    }
}

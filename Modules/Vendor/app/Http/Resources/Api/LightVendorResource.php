<?php

namespace Modules\Vendor\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LightVendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'logo' => formatImage($this->logo),
            'active' => (bool) $this->active,
            'star' => round($this->reviews_avg_star ?? $this->average_rating ?? 0, 1),
            'num_of_user_review' => $this->reviews_count ?? 0,
        ];
    }
}

<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferExpireDateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $endDate = $this->resource instanceof Carbon
            ? $this->resource
            : Carbon::parse($this->resource);
        $now = now();

        if (!$endDate || $endDate <= $now) {
            return [
                'days' => 0,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'expired' => true,
            ];
        }

        $diff = $endDate->diff($now);

        return [
            'days' => $diff->days,
            'hours' => $diff->h,
            'minutes' => $diff->i,
            'seconds' => $diff->s,
            'expired' => false,
        ];
    }
}

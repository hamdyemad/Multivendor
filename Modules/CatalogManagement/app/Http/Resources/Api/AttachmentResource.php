<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'url' => $this->url,
            'alt_text' => $this->alt_text,
        ];
    }
}

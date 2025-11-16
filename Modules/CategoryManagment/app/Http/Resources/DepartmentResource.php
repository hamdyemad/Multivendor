<?php

namespace Modules\CategoryManagment\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($request->select2){
            return [
                'id' => $this->id,
                'name' => $this->getTranslation('name', app()->getLocale()),
            ];
        }
        return [
            'id' => $this->id,
            'image' => ($this->image) ? Storage::disk('public')->url($this->image) : '',
            'name' => $this->getTranslation('name', app()->getLocale()) ?? 'N/A',
            'description' => $this->getTranslation('description', app()->getLocale()) ?? '',
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

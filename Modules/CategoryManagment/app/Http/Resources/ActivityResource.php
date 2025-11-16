<?php

namespace Modules\CategoryManagment\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Use fallback logic for activity name
        $activityName = $this->getTranslation('name', app()->getLocale())
            ?: $this->getTranslation('name', 'en')
            ?: $this->getTranslation('name', 'ar')
            ?: 'Activity #' . $this->id;

        $activityDescription = $this->getTranslation('description', app()->getLocale())
            ?: $this->getTranslation('description', 'en')
            ?: $this->getTranslation('description', 'ar')
            ?: '';

        if($request->select2){
            return [
                'id' => $this->id,
                'name' => $this->getTranslation('name', app()->getLocale()),
            ];
        }

        return [
            'id' => $this->id,
            'name' => $activityName,
            'slug' => $this->slug,
            'description' => $activityDescription,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

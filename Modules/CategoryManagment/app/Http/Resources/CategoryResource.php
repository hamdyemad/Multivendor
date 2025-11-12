<?php

namespace Modules\CategoryManagment\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $this->getProperLocale();
        
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', $locale) ?? '',
            'description' => $this->getTranslation('description', $locale) ?? '',
            'image' => ($this->image) ? Storage::disk('public')->url($this->image) : '',
            'active' => $this->active,
            'department' => $this->whenLoaded('department', function () {
                return new DepartmentResource($this->department);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get proper locale, handling Accept-Language header issues
     */
    private function getProperLocale()
    {
        // Check if locale is sent via custom header
        $request = request();
        if ($request && $request->header('X-App-Locale')) {
            $headerLang = $request->header('X-App-Locale');
            // Ensure it's one of our supported locales
            if (in_array($headerLang, ['ar', 'en'])) {
                return $headerLang;
            }
        }
        
        $appLocale = app()->getLocale();
        
        // If app locale contains comma (multiple locales), extract the first one
        if (strpos($appLocale, ',') !== false) {
            $locales = explode(',', $appLocale);
            $appLocale = trim($locales[0]);
        }
        
        // Remove quality values (e.g., "en-US;q=0.9" becomes "en-US")
        if (strpos($appLocale, ';') !== false) {
            $appLocale = explode(';', $appLocale)[0];
        }
        
        // Map common locale variations to our supported locales
        if (strpos($appLocale, 'ar') === 0) {
            return 'ar';
        } elseif (strpos($appLocale, 'en') === 0) {
            return 'en';
        }
        
        // Default to English if we can't determine
        return 'en';
    }
}

<?php

namespace Modules\CatalogManagement\app\Http\Requests;

use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;

class VariantConfigurationKeyRequest extends FormRequest
{

    public function __construct(
        protected LanguageService $languageService
    ) {
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'parent_key_id' => 'nullable|exists:variants_configurations_keys,id',
        ];

        // Get all languages and add validation for each language's translations
        $languages = $this->languageService->getAll();
        
        foreach ($languages as $language) {
            $rules["translations.{$language->id}.name"] = 'required|string|max:255';
        }

        return $rules;
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'parent_key_id' => __('variantkey.parent_key'),
        ];

        return $attributes;
    }
}

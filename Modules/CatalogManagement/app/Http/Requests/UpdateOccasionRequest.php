<?php

namespace Modules\CatalogManagement\app\Http\Requests;

class UpdateOccasionRequest extends OccasionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only vendors can update occasions
        return auth()->check() && in_array(auth()->user()->user_type_id, [3, 4]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = parent::rules();

        // For API, vendor_id is not required in request (taken from auth user)
        unset($rules['vendor_id']);

        // For updates, make most fields optional
        $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        $rules['variants'] = 'nullable|array';
        $rules['translations'] = 'nullable|array';

        return $rules;
    }
}

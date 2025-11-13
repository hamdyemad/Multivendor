<?php

namespace Modules\Vendor\app\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;
use Modules\Vendor\app\Models\Vendor;

class VendorRequest extends FormRequest
{
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
        $vendor = $this->resolveVendor();
        $isUpdate = !is_null($vendor);
        
        // Logo validation: required on create, optional on update if logo exists
        $logoRule = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        if ($isUpdate && $vendor->logo) {
            $logoRule = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        }
        
        // Banner validation: required on create, optional on update if banner exists
        $bannerRule = 'required|image|mimes:jpeg,png,jpg,gif|max:4096';
        if ($isUpdate && $vendor->banner) {
            $bannerRule = 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096';
        }
        
        // Email validation: unique except for current vendor's user
        $emailRule = 'required|email|max:255|unique:users,email';
        if ($isUpdate && $vendor->user) {
            $emailRule = 'required|email|max:255|unique:users,email,' . $vendor->user->id;
        }
        
        // Password validation: required on create, optional on update
        $passwordRule = 'required|string|min:8|confirmed';
        if ($isUpdate) {
            $passwordRule = 'nullable|string|min:8|confirmed';
        }
        
        return [
            // Vendor Information - Translations (using language IDs)
            'translations' => 'required|array|min:1',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string|max:500',
            'translations.*.meta_keywords' => 'nullable|string|max:500',
            
            // Files
            'logo' => $logoRule,
            'banner' => $bannerRule,
            
            // Relations
            'country_id' => 'required|exists:countries,id',
            'activity_ids' => 'required|array|min:1',
            'activity_ids.*' => 'required|exists:activities,id',
            
            // Status
            'active' => 'nullable|boolean',
            
            // Vendor Type
            'type' => 'required|in:product,booking,product_booking',
            
            // Commission
            'commission' => 'required|numeric|min:0|max:100',
            
            // Documents (using language IDs) - Required for vendor creation, optional for update
            'documents' => $isUpdate ? 'nullable|array' : 'required|array|min:1',
            'documents.*.translations' => 'required_with:documents|array',
            'documents.*.translations.1.name' => 'required_with:documents|string|max:255', // English (ID: 1)
            'documents.*.translations.2.name' => 'required_with:documents|string|max:255', // Arabic (ID: 2)
            'documents.*.file' => $isUpdate ? 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120' : 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            
            // Account
            'email' => $emailRule,
            'password' => $passwordRule,
        ];
    }

    /**
     * Resolve vendor from route (supports model binding or ID)
     */
    protected function resolveVendor(): ?Vendor
    {
        $param = $this->route('vendor');
        if ($param instanceof Vendor) {
            return $param;
        }

        return is_numeric($param)
            ? Vendor::with(['logo', 'banner', 'user'])->find($param)
            : null;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'translations.*.name' => __('vendor::vendor.name'),
            'translations.*.description' => __('vendor::vendor.description'),
            'logo' => __('vendor::vendor.logo'),
            'banner' => __('vendor::vendor.banner'),
            'country_id' => __('vendor::vendor.country'),
            'activity_id' => __('common.activity'),
            'meta_title' => __('vendor::vendor.meta_title'),
            'meta_description' => __('vendor::vendor.meta_description'),
            'meta_keywords' => __('vendor::vendor.meta_keywords'),
            'documents.*.translations.*.name' => __('vendor::vendor.document_name'),
            'documents.*.file' => __('vendor::vendor.document_file'),
            'email' => __('vendor::vendor.email'),
            'password' => __('vendor::vendor.password'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [
            'translations.required' => __('vendor::vendor.at_least_one_translation_required'),
            'country_id.required' => __('vendor::vendor.please_select_country'),
            'country_id.exists' => __('vendor::vendor.selected_country_invalid'),
            'activity_ids.required' => __('vendor::vendor.please_select_activity'),
            'activity_ids.min' => __('vendor::vendor.please_select_at_least_one_activity'),
            'activity_ids.*.exists' => __('vendor::vendor.selected_activity_invalid'),
            'email.required' => __('vendor::vendor.email_required'),
            'email.email' => __('vendor::vendor.email_valid'),
            'email.unique' => __('vendor::vendor.email_already_registered'),
            'password.required' => __('vendor::vendor.password_required'),
            'password.min' => __('vendor::vendor.password_min_8'),
            'password.confirmed' => __('vendor::vendor.password_confirmation_mismatch'),
            'logo.required' => __('vendor::vendor.logo_required'),
            'logo.image' => __('vendor::vendor.logo_must_be_image'),
            'logo.mimes' => __('vendor::vendor.logo_file_types'),
            'logo.max' => __('vendor::vendor.logo_max_size'),
            'banner.required' => __('vendor::vendor.banner_required'),
            'banner.image' => __('vendor::vendor.banner_must_be_image'),
            'banner.mimes' => __('vendor::vendor.banner_file_types'),
            'banner.max' => __('vendor::vendor.banner_max_size'),
            'documents.required' => __('vendor::vendor.documents_required'),
            'documents.min' => __('vendor::vendor.at_least_one_document_required'),
            'documents.*.translations.required' => __('vendor::vendor.document_name_required'),
            'documents.*.translations.1.name.required_with' => __('vendor::vendor.document_name_required_english'),
            'documents.*.translations.2.name.required_with' => __('vendor::vendor.document_name_required_arabic'),
            'documents.*.file.required' => __('vendor::vendor.document_file_required'),
            'documents.*.file.mimes' => __('vendor::vendor.document_file_types'),
            'documents.*.file.max' => __('vendor::vendor.document_max_size'),
        ];

        // Add custom messages for vendor name translations with language names
        $languages = Language::all();
        foreach ($languages as $language) {
            $messages["translations.{$language->id}.name.required"] = 
                __('vendor::vendor.name_required_for_language', ['language' => $language->name]);
        }

        // Add custom messages for document translations with language names
        foreach ($languages as $language) {
            $messages["documents.*.translations.{$language->id}.name.required_with"] = 
                __('vendor::vendor.document_name_required_for_language', ['language' => $language->name]);
        }

        return $messages;
    }
}

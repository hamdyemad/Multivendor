<?php

namespace App\Http\Requests\AdminManagement;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;
use App\Models\User;

class AdminRequest extends FormRequest
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
        $admin = $this->resolveAdmin();
        $isUpdate = !is_null($admin);
        
        // Email validation: unique except for current admin
        $emailRule = 'required|email|max:255|unique:users,email';
        if ($isUpdate && $admin) {
            $emailRule = 'required|email|max:255|unique:users,email,' . $admin->id;
        }
        
        // Password validation: required on create, optional on update
        $passwordRule = 'required|string|min:8|confirmed';
        if ($isUpdate) {
            $passwordRule = 'nullable|string|min:8|confirmed';
        }
        
        return [
            // Admin Information - Translations (using language IDs)
            'translations' => 'required|array|min:1',
            'translations.*.name' => 'required|string|max:255',
            
            // Status
            'active' => 'nullable|boolean',
            'block' => 'nullable|boolean',
            
            // Roles
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'required|exists:roles,id',
            
            // Account
            'email' => $emailRule,
            'password' => $passwordRule,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Resolve user from route (supports model binding or ID)
     */
    protected function resolveAdmin(): ?User
    {
        $param = $this->route('admin');
        if ($param instanceof User) {
            return $param;
        }

        return is_numeric($param)
            ? User::find($param)
            : null;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'translations.*.name' => __('admin.name'),
            'email' => __('admin.email'),
            'password' => __('admin.password'),
            'role_ids' => __('admin.roles'),
            'role_ids.*' => __('admin.role'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [
            'translations.required' => __('admin.at_least_one_translation_required'),
            'email.required' => __('admin.email_required'),
            'email.email' => __('admin.email_valid'),
            'email.unique' => __('admin.email_already_registered'),
            'password.required' => __('admin.password_required'),
            'password.min' => __('admin.password_min_8'),
            'password.confirmed' => __('admin.password_confirmation_mismatch'),
            'role_ids.required' => __('admin.roles_required'),
            'role_ids.array' => __('admin.roles_must_be_array'),
            'role_ids.min' => __('admin.at_least_one_role_required'),
            'role_ids.*.required' => __('admin.role_required'),
            'role_ids.*.exists' => __('admin.role_invalid'),
        ];

        // Add custom messages for admin name translations with language names
        $languages = Language::all();
        foreach ($languages as $language) {
            $messages["translations.{$language->id}.name.required"] = 
                __('admin.name_required_for_language', ['language' => $language->name]);
        }

        return $messages;
    }
}

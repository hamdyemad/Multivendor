<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserType;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminRepository
{
    /**
     * Get admins query with filters and sorting
     * Filters based on logged-in user:
     * - Super Admin: sees all system admins
     * - Vendor: sees only their own users
     */
    public function getAdminsQuery(array $filters = [])
    {
        $query = User::with(['roles', 'translations'])->filter($filters);
        return $query;
    }

    /**
     * Get user by ID
     */
    public function getAdminById(int $id)
    {
        return User::with(['roles', 'roles.translations', 'translations'])->findOrFail($id);
    }

    /**
     * Create a new user (admin or vendor user)
     */
    public function createAdmin(array $data)
    {
        return DB::transaction(function () use ($data) {
            $currentUser = Auth::user();
            
            // Determine user_type_id and vendor_id based on logged-in user
            if ($currentUser->user_type_id == UserType::SUPER_ADMIN_TYPE) {
                // Super admin creates system admins
                $userTypeId = UserType::ADMIN_TYPE;
                $vendorId = null;
            }  else if($currentUser->user_type_id == UserType::ADMIN_TYPE) {
                // Admin creates system admins
                $userTypeId = UserType::ADMIN_TYPE;
                $vendorId = null;
            }  elseif ($currentUser->user_type_id == UserType::VENDOR_TYPE) {
                // Vendor creates vendor users
                $userTypeId = UserType::VENDOR_USER_TYPE;
                $vendorId = $currentUser->id;
            }else {
                throw new \Exception('Unauthorized to create users');
            }
            
            // Create user
            $user = User::create([
                'uuid' => \Str::uuid(),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type_id' => $userTypeId,
                'vendor_id' => $vendorId,
                'active' => $data['active'] ?? true,
                'block' => $data['block'] ?? false,
            ]);

            // Assign roles to user
            if (!empty($data['role_ids'])) {
                $user->roles()->sync($data['role_ids']);
            }

            // Store translations
            $this->storeTranslations($user, $data);

            return $user;
        });
    }

    /**
     * Update user
     */
    public function updateAdmin(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $user = User::findOrFail($id);

            $updateData = [];
            
            // Update email if provided
            if (!empty($data['email'])) {
                $updateData['email'] = $data['email'];
            }
            
            // Update password if provided
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }
            
            // Update active status
            $updateData['active'] = $data['active'] ?? true;
            
            // Update block status
            $updateData['block'] = $data['block'] ?? false;
            
            // Update user
            $user->update($updateData);

            // Update roles
            if (isset($data['role_ids'])) {
                $user->roles()->sync($data['role_ids']);
            }

            // Update translations
            $this->storeTranslations($user, $data);

            return $user;
        });
    }

    /**
     * Delete user
     */
    public function deleteAdmin(int $id)
    {
        return DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);
            
            // Delete user translations
            $user->translations()->delete();
            
            // Detach roles
            $user->roles()->detach();
            
            // Delete user (soft delete)
            $user->delete();
            
            return true;
        });
    }

    /**
     * Store translations for user
     */
    protected function storeTranslations(User $user, array $data)
    {
        // Delete existing translations
        $user->translations()->delete();

        // Handle translations array from form
        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $languageId => $fields) {
                // Get language
                $language = Language::find($languageId);
                if (!$language) {
                    continue;
                }

                // Store name translation
                if (!empty($fields['name'])) {
                    $user->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $fields['name'],
                    ]);
                }
            }
        }
    }
}

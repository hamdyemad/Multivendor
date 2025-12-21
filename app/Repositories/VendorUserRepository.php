<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserType;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class VendorUserRepository
{
    /**
     * Get vendor users query with filters
     */
    public function getVendorUsersQuery(array $filters = [])
    {
        $query = User::with(['roles', 'translations', 'vendorById'])
            ->where('user_type_id', UserType::VENDOR_USER_TYPE)
            ->filter($filters);

        $currentUser = Auth::user();
        if ($currentUser->user_type_id == UserType::VENDOR_TYPE) {
            $query->where('vendor_id', $currentUser->id);
        } elseif (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        return $query;
    }

    /**
     * Get user by ID
     */
    public function getVendorUserById(int $id)
    {
        return User::with(['roles', 'roles.translations', 'translations', 'vendorById'])
            ->where('user_type_id', UserType::VENDOR_USER_TYPE)
            ->findOrFail($id);
    }

    /**
     * Create a new vendor user
     */
    public function createVendorUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create user
            $user = User::create([
                'uuid' => \Str::uuid(),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type_id' => UserType::VENDOR_USER_TYPE,
                'vendor_id' => $data['vendor_id'],
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
     * Update vendor user
     */
    public function updateVendorUser(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $user = User::where('user_type_id', UserType::VENDOR_USER_TYPE)->findOrFail($id);

            $updateData = [
                'vendor_id' => $data['vendor_id']
            ];
            
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
     * Delete vendor user
     */
    public function deleteVendorUser(int $id)
    {
        return DB::transaction(function () use ($id) {
            $user = User::where('user_type_id', UserType::VENDOR_USER_TYPE)->findOrFail($id);
            
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
     * Change status
     */
    public function changeStatus(int $id, $status, $type)
    {
        $user = User::where('user_type_id', UserType::VENDOR_USER_TYPE)->findOrFail($id);
        
        if ($type == 'block') {
            $user->update(['block' => $status]);
        } else {
            $user->update(['active' => $status]);
        }
        
        return $user;
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

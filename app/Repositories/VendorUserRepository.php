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
            ->where('id', '!=', Auth::id()) // Exclude current user
            ->filter($filters);

        $currentUser = Auth::user();
        
        // If current user is a vendor, filter by their vendor_id
        if ($currentUser->isVendor()) {
            // Get vendor from vendorByUser (owner) or vendorById (employee)
            $vendor = $currentUser->vendorByUser ?? $currentUser->vendorById;
            if ($vendor) {
                $query->where('vendor_id', $vendor->id);
            }
        } elseif (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }
        
        // Filter by country: show vendor users for current country OR system users (null country_id)
        $countryCode = request()->route('countryCode') ?? session('country_code');
        $countryCode = strtoupper($countryCode);
        $currentCountryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
        
        if ($currentCountryId) {
            $query->where(function($q) use ($currentCountryId) {
                $q->where('country_id', $currentCountryId)
                  ->orWhereNull('country_id');
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get user by ID
     */
    public function getVendorUserById(int $id)
    {
        $query = User::with(['roles', 'roles.translations', 'translations', 'vendorById'])
            ->where('user_type_id', UserType::VENDOR_USER_TYPE);
        
        // If current user is a vendor, only allow access to users in their vendor
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->isVendor()) {
            $vendor = $currentUser->vendorByUser ?? $currentUser->vendorById;
            if ($vendor) {
                $query->where('vendor_id', $vendor->id);
            }
        }
        
        return $query->findOrFail($id);
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
                'image' => isset($data['image']) ? $data['image']->store('vendor_users', 'public') : null,
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
            $query = User::where('user_type_id', UserType::VENDOR_USER_TYPE);
            
            // If current user is a vendor, only allow access to users in their vendor
            $currentUser = Auth::user();
            if ($currentUser && $currentUser->isVendor()) {
                $vendor = $currentUser->vendorByUser ?? $currentUser->vendorById;
                if ($vendor) {
                    $query->where('vendor_id', $vendor->id);
                }
            }
            
            $user = $query->findOrFail($id);

            $updateData = [];

            (isset($data['vendor_id'])) ? $updateData['vendor_id'] = $data['vendor_id'] : null;
            
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
            
            // Update image
            if (isset($data['image'])) {
                // Delete old image if exists
                if ($user->image) {
                    \Storage::disk('public')->delete($user->image);
                }
                $updateData['image'] = $data['image']->store('vendor_users', 'public');
            }
            
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
            $query = User::where('user_type_id', UserType::VENDOR_USER_TYPE);
            
            // If current user is a vendor, only allow access to users in their vendor
            $currentUser = Auth::user();
            if ($currentUser && $currentUser->isVendor()) {
                $vendor = $currentUser->vendorByUser ?? $currentUser->vendorById;
                if ($vendor) {
                    $query->where('vendor_id', $vendor->id);
                }
            }
            
            $user = $query->findOrFail($id);
            
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
        $query = User::where('user_type_id', UserType::VENDOR_USER_TYPE);
        
        // If current user is a vendor, only allow access to users in their vendor
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->isVendor()) {
            $vendor = $currentUser->vendorByUser ?? $currentUser->vendorById;
            if ($vendor) {
                $query->where('vendor_id', $vendor->id);
            }
        }
        
        $user = $query->findOrFail($id);
        
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
        // Delete existing name translations
        $user->translations()->where('lang_key', 'name')->delete();

        // Handle translations array from form
        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $languageId => $fields) {
                // Get language
                $language = Language::find($languageId);
                if (!$language) {
                    continue;
                }

                // Store name translation
                if (isset($fields['name']) && $fields['name'] !== '') {
                    $user->translations()->create([
                        'translatable_type' => 'App\Models\User',
                        'translatable_id' => $user->id,
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $fields['name'],
                    ]);
                }
            }
        }
    }
}

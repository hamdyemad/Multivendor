<?php

namespace App\Repositories\AdminManagement;

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
    public function getAdminsQuery(array $filters = [], $orderBy = null, string $orderDirection = 'desc')
    {
        $query = User::with(['roles', 'translations']);

        // Filter based on logged-in user type
        $currentUser = Auth::user();
        
        if ($currentUser->user_type_id == UserType::SUPER_ADMIN_TYPE) {
            // Super admin sees all admin users (user_type_id = 2, vendor_id = null)
            $query->where('user_type_id', UserType::ADMIN_TYPE)
                  ->whereNull('vendor_id');
        } elseif ($currentUser->user_type_id == UserType::VENDOR_TYPE) {
            // Vendor sees only users they created (with their vendor_id)
            $query->where('vendor_id', $currentUser->id);
        } else {
            // Other user types shouldn't access this
            $query->whereRaw('1 = 0'); // Return empty result
        }

        // Search filter
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('translations', function ($query) use ($searchTerm) {
                    $query->where('lang_key', 'name')
                        ->where('lang_value', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Active status filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Role filter
        if (!empty($filters['role_id'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('roles.id', $filters['role_id']);
            });
        }

        // Date range filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }
        
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Apply sorting
        if ($orderBy) {
            if (is_array($orderBy) && isset($orderBy['lang_id'])) {
                // Sort by translation
                $languageId = $orderBy['lang_id'];
                $query->leftJoin('translations', function ($join) use ($languageId) {
                    $join->on('users.id', '=', 'translations.translatable_id')
                        ->where('translations.translatable_type', '=', User::class)
                        ->where('translations.lang_id', '=', $languageId)
                        ->where('translations.lang_key', '=', 'name');
                })
                ->select('users.*')
                ->orderBy('translations.lang_value', $orderDirection);
            } else {
                // Sort by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        } else {
            $query->latest();
        }

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
            } elseif ($currentUser->user_type_id == UserType::VENDOR_TYPE) {
                // Vendor creates vendor users
                $userTypeId = UserType::VENDOR_USER_TYPE;
                $vendorId = $currentUser->id;
            } else {
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

            // Assign role to user
            if (!empty($data['role_id'])) {
                $user->roles()->sync([$data['role_id']]);
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

            // Update role
            if (isset($data['role_id'])) {
                $user->roles()->sync([$data['role_id']]);
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

<?php

namespace App\Repositories;

use App\Interfaces\RoleRepositoryInterface;
use App\Models\Permession;
use App\Models\Role;
use App\Models\User;
use App\Models\UserType;
use App\Services\LanguageService;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{

    public function __construct(protected LanguageService $languageService)
    {

    }
    /**
     * Get all roles with their permissions
     */
    public function getAll($filter = [], $per_page = 10)
    {
        $query = Role::filter($filter)->latest();
        if(auth()->user()->user_type_id == UserType::SUPER_ADMIN_TYPE) {
            $query->superAdminShowRoles();
        } else if(auth()->user()->user_type_id == UserType::ADMIN_TYPE) {
            $query->adminShowRoles();
        } else if(in_array(auth()->user()->user_type_id, [UserType::VENDOR_TYPE, UserType::VENDOR_USER_TYPE])) {
            $query->vendorShowRoles();
        }
        return $per_page = 0 ? $query->get() : $query->paginate($per_page);
    }

    public function getRolesQuery($filter = []) {
        $query = Role::filter($filter)->latest();
        return $query;
    }

    /**
     * Get grouped permissions
     */
    public function getGroupedPermissions(): Collection
    {
        // Load permissions with their translations
        $permissions = Permession::with('translations');

        if(auth()->user()->user_type_id == UserType::VENDOR_TYPE) {
            $permissions = $permissions->where('type', 'other');
        }

        $permissions = $permissions->get();

        // Group by translated group_by field
        return $permissions->groupBy(function($permission) {
            return $permission->getTranslation('group_by', app()->getLocale()) ?? 'Other';
        });
    }


    /**
     * Find a role by ID
     */
    public function findById(int $id): ?Role
    {
        return Role::with('permessions')->find($id);
    }

    /**
     * Create a new role
     */
    public function create(array $data)
    {
        // Create role with type
        $role = Role::create([
            'type' => $data['type'] ?? 'other'
        ]);

        // Add translations for all languages
        if (isset($data['translations']) && is_array($data['translations'])) {
            foreach ($data['translations'] as $languageId => $fields) {
                // Get language
                $language = $this->languageService->getById($languageId);
                if (!$language) {
                    continue;
                }

                // Store name translation
                if (!empty($fields['name'])) {
                    $role->setTranslation('name', $language->code, $fields['name']);
                }
            }
        }

        // Sync permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $this->syncPermissions($role, $data['permissions']);
        }

        return $role;
    }

    /**
     * Update a role
     */
    public function update(Role $role, array $data)
    {
        // Get all languages
        $languages = $this->languageService->getAll();
        // Update translations for all languages (no name column to update)
        foreach ($languages as $language) {
            if (isset($data['name_' . $language->code]) && !empty($data['name_' . $language->code])) {
                $role->setTranslation(
                    'name',
                    $language->code,
                    $data['name_' . $language->code]
                );
            }
        }
        // Sync permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $this->syncPermissions($role, $data['permissions']);
        } else {
            // Clear all permissions if none provided
            $this->syncPermissions($role, []);
        }
        return $role;
    }

    /**
     * Delete a role
     */
    public function delete(Role $role)
    {
        // Detach all permissions before deleting
        $role->permessions()->detach();
        // Delete all translations
        $role->translations()->delete();
        return $role->delete();
    }

    /**
     * Sync permissions for a role
     */
    public function syncPermissions(Role $role, array $permissionIds): void
    {
        $role->permessions()->sync($permissionIds);
    }

    /**
     * Set translation for a role
     */
    public function setTranslation(Role $role, string $key, string $value, string $locale): void
    {
        $role->setTranslation($key, $value, $locale);
    }

    public function getVendorRole()
    {
        $role = Role::where('type', Role::VENDOR_ROLE_TYPE)->first();

        if (!$role) {
            throw new \Exception('Vendor role not found. Please ensure a vendor role exists in the database with type "vendor".');
        }

        return $role;
    }
}

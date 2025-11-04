<?php

namespace App\Services;

use App\Interfaces\RoleRepositoryInterface;
use App\Models\Role;
use App\Models\Language;
use App\Models\Permession;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    public function __construct(
        public RoleRepositoryInterface $roleRepositoryInterface,
        protected LanguageService $languageService
        )
    {
    }

    /**
     * Get all roles with permissions
     */
    public function getAllRoles($filter = [], $per_page = 10)
    {
        return $this->roleRepositoryInterface->getAll($filter, $per_page);
    }

    public function getRolesQuery($filter = [])
    {
        return $this->roleRepositoryInterface->getRolesQuery($filter);
    }

    public function getDataTable($data) {
        $draw = $data['draw'];
        $start = $data['start'];
        $length = $data['length'];
        
        // Get search value from custom parameter or DataTables default
        $searchValue = $data['search'];
        if (is_array($searchValue)) {
            $searchValue = $searchValue['value'] ?? '';
        }
        
        $orderColumnIndex = $data['orderColumnIndex'];
        $orderDirection = $data['orderDirection'];

        // Get languages
        $languages = $this->languageService->getAll();

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'created_date_from' => $data['created_date_from'],
            'created_date_to' => $data['created_date_to']
        ];

        // Get total records before filtering
        $totalRecords = $this->getRolesQuery()->count();
        $baseQuery = $this->getRolesQuery($filters);
        // Get filtered count (clone query to avoid mutation)
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();
        $query = $baseQuery;
        
        // Clear existing orders to prevent conflicts with latest() in base query
        $query->reorder();
        
        // Apply sorting
        // Check if sorting by name column (columns 1 to count($languages))
        if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
            // Get the language for this column
            $languageIndex = $orderColumnIndex - 1;
            $selectedLanguage = $languages->values()->get($languageIndex);
            
            // Join with translations table to sort by translated name
            $query->leftJoin('translations as trans_sort', function($join) use ($selectedLanguage) {
                $join->on('roles.id', '=', 'trans_sort.translatable_id')
                     ->where('trans_sort.translatable_type', '=', 'App\\Models\\Role')
                     ->where('trans_sort.lang_key', '=', 'name')
                     ->where('trans_sort.lang_id', '=', $selectedLanguage->id);
            })
            ->orderBy('trans_sort.lang_value', $orderDirection)
            ->select('roles.*'); // Select only roles columns to avoid conflicts
        } else {
            // Build column map for non-translation columns
            $orderColumns = [
                0 => 'id',
                (count($languages) + 1) => 'id', // permissions (not directly sortable)
                (count($languages) + 2) => 'created_at',
            ];

            if (isset($orderColumns[$orderColumnIndex])) {
                $query->orderBy($orderColumns[$orderColumnIndex], $orderDirection);
            }
        }

        // Apply pagination
        $perPage = $data['length'];
        $page = $data['page'];
        $roles = $query->with(['permessions', 'translations'])->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $data = [];
        foreach ($roles as $index => $role) {
            $row = [];
            
            // Row number with pagination offset
            $row[] = ($roles->currentPage() - 1) * $roles->perPage() + $index + 1;

            // Role name for each language
            foreach ($languages as $language) {
                $name = $role->getTranslation('name', $language->code) ?? '-';
                $row[] = '<div class="userDatatable-content" ' . ($language->rtl ? 'dir="rtl"' : '') . '>
                            <strong>' . e($name) . '</strong>
                          </div>';
            }

            // Permissions
            $permissionsCount = $role->permessions->count();
            $permissionsHtml = '<div class="userDatatable-content">
                                    <span class="badge badge-primary" style="border-radius: 6px; padding: 6px 12px;">
                                        <i class="uil uil-shield-check me-1"></i>' . $permissionsCount . ' ' . trans('roles.permissions') . '
                                    </span>
                                </div>';
            $row[] = $permissionsHtml;

            // Created at
            $row[] = '<div class="userDatatable-content">' . e($role->created_at->format('Y-m-d H:i')) . '</div>';

            // Actions
            $actionsHtml = '<ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                                <li>
                                    <a href="' . route('admin.admin-management.roles.show', $role->id) . '" class="view" title="' . e(trans('common.view')) . '">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="' . route('admin.admin-management.roles.edit', $role->id) . '" class="edit" title="' . e(trans('common.edit')) . '">
                                        <i class="uil uil-edit"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" 
                                       class="remove" 
                                       title="' . e(trans('common.delete')) . '"
                                       data-bs-toggle="modal" 
                                       data-bs-target="#modal-delete-role"
                                       data-item-id="' . $role->id . '"
                                       data-item-name="' . e($role->name) . '">
                                        <i class="uil uil-trash-alt"></i>
                                    </a>
                                </li>
                            </ul>';
            $row[] = $actionsHtml;

            $data[] = $row;
        }

        return [
            'dataPaginated' => $roles,
            'data' => $data,
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords,
        ];
    }

    /**
     * Get a single role by ID
     */
    public function getRoleById(int $id): ?Role
    {
        return $this->roleRepositoryInterface->findById($id);
    }

    /**
     * Get grouped permissions
     */
    public function getGroupedPermissions(): Collection
    {
        return $this->roleRepositoryInterface->getGroupedPermissions();
    }

    /**
     * Create a new role
     */
    public function createRole(array $data)
    {        
        return $this->roleRepositoryInterface->create($data);
    }

    /**
     * Update an existing role
     */
    public function updateRole(Role $role, array $data)
    {
        $this->roleRepositoryInterface->update($role, $data);
    }

    /**
     * Delete a role
     */
    public function deleteRole(Role $role)
    {
        $this->roleRepositoryInterface->delete($role);
    }

    /**
     * Assign permissions to a role
     */
    public function assignPermissions(Role $role, array $permissionIds): void
    {
        $this->roleRepositoryInterface->syncPermissions($role, $permissionIds);
    }

    /**
     * Add permissions to a role
     */
    public function addPermissions(Role $role, array $permissionIds): void
    {
        $currentPermissions = $role->permessions->pluck('id')->toArray();
        $allPermissions = array_unique(array_merge($currentPermissions, $permissionIds));
        
        $this->roleRepositoryInterface->syncPermissions($role, $allPermissions);
    }

    /**
     * Remove permissions from a role
     */
    public function removePermissions(Role $role, array $permissionIds): void
    {
        $currentPermissions = $role->permessions->pluck('id')->toArray();
        $remainingPermissions = array_diff($currentPermissions, $permissionIds);
        
        $this->roleRepositoryInterface->syncPermissions($role, $remainingPermissions);
    }

    public function getVendorRole(): Role
    {
        return $this->roleRepositoryInterface->getVendorRole();
    }
}

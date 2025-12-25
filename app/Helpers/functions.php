<?php

use App\Models\Attachment;
use App\Models\Language;
use App\Models\Permession;
use App\Models\Role;
use App\Models\User;
use App\Models\UserType;

/**
 * Get Status Meta
 *
 * @param string $status_key
 * @return array Status
 */
function get_status_meta($status_key = '')
{
    $metas = [
        'active'   => [
            'label' => 'Active',
            'class' => 'success',
        ],
        'inactive' => [
            'label' => 'Inactive',
            'class' => 'warning',
        ],
        'blocked'  => [
            'label' => 'Blocked',
            'class' => 'danger',
        ],
    ];

    if (empty($status_key)) {
        return $metas;
    }

    if (in_array($status_key, array_keys($metas))) {
        return $metas[$status_key];
    }

    return [];
}

/**
 * Get Status Class
 *
 * @param string $status_key
 * @return string Status Class
 */
function get_status_class($status_key = '')
{

    $status_meta = get_status_meta($status_key);

    if (empty($status_meta['class'])) {
        return '';
    }

    return $status_meta['class'];
}

/**
 * Get Status label
 *
 * @param string $status_key
 * @return string Status label
 */
function get_status_label($status_key = '')
{
    $status_meta = get_status_meta($status_key);

    if (empty($status_meta['label'])) {
        return '';
    }

    return $status_meta['label'];
}


function permessions_reset()
{
    // Empty the permessions table
    Permession::query()->forceDelete();
    
    // Get permissions from config
    $permissionsConfig = config('permissions');
    
    foreach ($permissionsConfig as $moduleKey => $moduleData) {
        $moduleName = $moduleData['name'];
        $moduleType = $moduleData['type'] ?? 'admin'; // Get module type
        $moduleIcon = $moduleData['icon'] ?? 'uil-setting'; // Get module icon
        $subModules = $moduleData['sub_modules'] ?? [];
        
        foreach ($subModules as $subModuleKey => $subModuleData) {
            $subModuleName = $subModuleData['name'];
            $permissions = $subModuleData['permissions'] ?? [];
            
            foreach ($permissions as $action => $permissionData) {
                $key = $permissionData['key'];
                $permissionName = $permissionData['name'];
                $permissionType = $permissionData['type'] ?? $moduleType; // Use permission type or fallback to module type
                
                // Determine color based on action/keyword
                $color = 'bg-secondary';
                $actionLower = strtolower($action);
                if (in_array($actionLower, ['create', 'store', 'add'])) {
                    $color = 'bg-success';
                } elseif (in_array($actionLower, ['edit', 'update'])) {
                    $color = 'bg-warning';
                } elseif (in_array($actionLower, ['delete', 'destroy', 'remove', 'trash'])) {
                    $color = 'bg-danger';
                } elseif (in_array($actionLower, ['read', 'index', 'show', 'view'])) {
                    $color = 'bg-info';
                }
                
                // Create permission with all fields
                Permession::create([
                    'type' => $permissionType,
                    'module' => $moduleKey,
                    'sub_module' => $subModuleKey,
                    'module_icon' => $moduleIcon,
                    'color' => $color,
                    'key' => $key,
                    'name_en' => $permissionName['en'] ?? $key,
                    'name_ar' => $permissionName['ar'] ?? $key,
                ]);
            }
        }
    }
}

function permession_maker($key, $type = 'other', $translations = [])
{
    // Get languages
    $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

    $permession = Permession::query()->where('key', $key)->first();
    if ($permession) {
        $permession->delete();
    }
    $permissions = [
        ['key' => $key, 'type' => $type, 'translations' => $translations],
    ];

    foreach ($permissions as $permissionData) {
        // Create or update the permission
        $permission = Permession::create(
            [
                'type' => $permissionData['type'] ?? 'other',
                'key' => $permissionData['key']
            ]
        );

        // Add translations if available and languages exist
        if ($languages->isNotEmpty() && isset($permissionData['translations'])) {
            foreach ($permissionData['translations']['name'] as $locale => $value) {
                $permission->setTranslation('name', $locale, $value);
            }
            foreach ($permissionData['translations']['group_by'] as $locale => $value) {
                $permission->setTranslation('group_by', $locale, $value);
            }
        }
    }
}

function roles_reset()
{
    // Get languages
    $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

    Role::query()->forceDelete();

    // Define roles with translations
    $rolesData = [
        [
            'type' => 'super_admin',
            'is_system_protected' => true,
            'translations' => [
                'name' => ['en' => 'Super Admin Eramo', 'ar' => 'سوبر ادمن ايرامو'],
            ]
        ],
        [
            'type' => 'admin',
            'is_system_protected' => true,
            'translations' => [
                'name' => ['en' => 'Admin', 'ar' => 'مسؤول'],
            ],
        ],
        [
            'type' => 'vendor',
            'is_system_protected' => true,
            'translations' => [
                'name' => ['en' => 'Vendor', 'ar' => 'تاجر'],
            ],
        ],
        [
            'type' => 'vendor_user',
            'is_system_protected' => true,
            'translations' => [
                'name' => ['en' => 'Vendor User', 'ar' => 'مستخدم مورد'],
            ],
        ],
    ];

    foreach ($rolesData as $roleData) {
        // Create or update the role
        $role = Role::create([
            'type' => $roleData['type'],
            'is_system_protected' => $roleData['is_system_protected'] ?? false,
        ]);

        // Add translations if available and languages exist
        if ($languages->isNotEmpty() && isset($roleData['translations'])) {
            foreach ($roleData['translations']['name'] as $locale => $value) {
                $role->setTranslation('name', $locale, $value);
            }
        }

        // Assign permissions based on role type
        if (isset($roleData['type'])) {
            if ($roleData['type'] == 'super_admin') {
                // Super admin gets all permissions
                $permissions = Permession::all();
                $role->permessions()->sync($permissions->pluck('id'));

                // Assign role to super admin user
                $super_admin = User::where('user_type_id', UserType::SUPER_ADMIN_TYPE)->first();
                if ($super_admin) {
                    $super_admin->roles()->sync([$role->id]);
                }
            } else if ($roleData['type'] == 'admin') {
                // Admin gets all permissions with type = 'admin' or 'all'
                $permissions = Permession::whereIn('type', ['admin', 'all'])->get();
                $role->permessions()->sync($permissions->pluck('id'));
            } else if ($roleData['type'] == 'vendor') {
                // Vendor gets all permissions with type = 'vendor' or 'all'
                $permissions = Permession::whereIn('type', ['vendor', 'all'])->get();
                $role->permessions()->sync($permissions->pluck('id'));
            } else if ($roleData['type'] == 'vendor_user') {
                // Vendor User gets all permissions with type = 'vendor' or 'all'
                $permissions = Permession::whereIn('type', ['vendor', 'all'])->get();
                $role->permessions()->sync($permissions->pluck('id'));
            }
        }
    }
}

function preview($path)
{
    $fullPath = public_path('storage/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mime = mime_content_type($fullPath);

    // Inline display (no download)
    return response()->file($fullPath, [
        'Content-Type' => $mime,
        'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"'
    ]);
}

function truncateString($string, $length = 15, $append = '...') {
    if (!isset($string) || $string === '') {
        return "";
    }

    // Ensure UTF-8
    $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');

    if (mb_strlen($string, 'UTF-8') > $length) {
        return mb_substr($string, 0, $length, 'UTF-8') . $append;
    }

    return $string;
}

function formatImage($imagePath): ?string
{
    if (!$imagePath) {
        return '';
    }

    if ($imagePath instanceof Attachment) {
        return url(asset('storage/' . $imagePath->path));
    }

    return url(asset('storage/' . $imagePath));
}

/**
 * Generate route URL with country code prefix
 *
 * @param string $name
 * @param array $parameters
 * @param bool $absolute
 * @return string
 */
function routeWithCountryCode($name, $parameters = [], $absolute = true): string
{
    $countryCode = strtolower(session('country_code') ?? 'us');

    // Add country code as first parameter
    $parameters = array_merge(['countryCode' => $countryCode], $parameters);

    return route($name, $parameters, $absolute);
}

/**
 * Get current country code from session or default
 *
 * @return string
 */
function getCountryCode(): string
{
    return session('country_code') ?? 'eg';
}

/**
 * Get currency symbol for the current country
 *
 * @return string
 */
function currency(): string
{
    try {
        $countryCode = session('country_code') ?? 'eg';
        $country = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->first();

        if ($country && $country->currency) {
            return $country->currency->symbol;
        }

        return 'EGP'; // Default fallback
    } catch (\Exception $e) {
        return 'EGP'; // Fallback in case of error
    }
}

function current_country()
{
    try {
        $countryCode = session('country_code');
        if($countryCode) {
            $country = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->first();
        } else {
            $country = \Modules\AreaSettings\app\Models\Country::default()->first();
        }
        if ($country) {
            return $country;
        }
        return '';
    } catch (\Exception $e) {
        return null; // Fallback in case of error
    }
}

function isAdmin() {
    $user = auth()->user();
    $user_type_id = $user->user_type_id ?? null;
    if (in_array($user_type_id, \App\Models\UserType::adminIds())) {
        return true;
    } else {
        return false;
    }
}

function isVendor() {
    $user = auth()->user();
    $user_type_id = $user->user_type_id ?? null;
    if (in_array($user_type_id, \App\Models\UserType::vendorIds())) {
        return true;
    } else {
        return false;
    }
}

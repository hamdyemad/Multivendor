<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permession;
use App\Models\Language;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        

        // Get languages
        $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

        Role::query()->delete();

        // Define roles with translations
        $rolesData = [
            [
                'type' => 'admin',
                'translations' => [
                    'name' => ['en' => 'Super Admin Eramo', 'ar' => 'سوبر ادمن ايرامو'],
                ]
            ],
            [
                'type' => 'other',
                'translations' => [
                    'name' => ['en' => 'Admin Eramo', 'ar' => 'ادمن ايرامو'],
                ],
                'permissions' => []
            ],
        ];

        foreach ($rolesData as $roleData) {
            // Create or update the role
            $role = Role::create([
                'type' => $roleData['type']
            ]);
            
            // Add translations if available and languages exist
            if ($languages->isNotEmpty() && isset($roleData['translations'])) {
                foreach ($roleData['translations']['name'] as $locale => $value) {
                    $role->setTranslation('name', $locale, $value);
                }
            }
            
            // Assign permissions based on role type
            if(isset($roleData['type']) && $roleData['type'] == 'admin') {
                // Super admin gets all permissions
                $permissions = Permession::all();
                $role->permessions()->sync($permissions->pluck('id'));
                
                // Assign role to super admin user
                $super_admin = User::where('user_type_id', UserType::SUPER_ADMIN_TYPE)->first();
                if ($super_admin) {
                    $super_admin->roles()->sync([$role->id]);
                }
            } else {
                // Other roles get specific permissions
                if (isset($roleData['permissions']) && !empty($roleData['permissions'])) {
                    $permissions = Permession::whereIn('key', $roleData['permissions'])->get();
                    $role->permessions()->sync($permissions->pluck('id'));
                }
            }
        }
    }
}

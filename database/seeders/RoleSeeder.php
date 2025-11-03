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

        // Create Role For The Super Admin
        $rolesData = [
            [
                'type' => 'admin',
                'translations' => [
                    'name' => ['en' => 'Super Admin Eramo', 'ar' => 'سوبر ادمن ايرامو'],
                ]
            ],
        ];

        // Define roles with translations
        $rolesData = [
            [
                'type' => 'other',
                'translations' => [
                    'name' => ['en' => 'Admin Eramo', 'ar' => 'ادمن ايرامو'],
                ]
            ],
        ];

        foreach ($rolesData as $roleData) {
            // Create or update the role
            $role = Role::updateOrCreate([]);
            // Add translations if available and languages exist
            if ($languages->isNotEmpty() && isset($roleData['translations'])) {
                foreach ($roleData['translations']['name'] as $locale => $value) {
                    $role->setTranslation('name', $locale, $value);
                }
            }
            if(isset($role['type']) && $role['type'] == 'admin') {
                $permissions = Permession::all();
                $role->permessions()->sync($permissions->pluck('id'));
                $super_admin = User::where('user_type_id', UserType::SUPER_ADMIN_TYPE)->first();
                $super_admin->roles()->sync($role);
            } else {
                $permissions = Permession::whereIn('key', $roleData['permissions'])->get();
            }
        }
    }
}

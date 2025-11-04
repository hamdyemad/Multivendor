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
        roles_reset();
    }
}

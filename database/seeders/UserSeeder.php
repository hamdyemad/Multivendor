<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use withTrashed to include soft-deleted records, then force delete them
        User::withTrashed()->forceDelete();
        
        // Create super admin user
        $super_admin_data = [
            'uuid' => \Str::uuid(),
            'email' => 'super_admin@gmail.com',
            'user_type_id' => UserType::SUPER_ADMIN_TYPE,
            'password' => Hash::make('123456789'),
        ];
        
        User::create($super_admin_data);
    }
}

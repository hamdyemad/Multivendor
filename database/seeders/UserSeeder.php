<?php

namespace Database\Seeders;

use App\Models\User;
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
        $user = User::where('email', 'super_admin@gmail.com')->first();
        $data = [
                'uuid' => \Str::uuid(),
                'user_type_id' => 1,
                'password' => Hash::make('123456789'),
        ];
        if($user) {
            $user->update($data);
        } else {
            User::create($data);
        }
    }
}

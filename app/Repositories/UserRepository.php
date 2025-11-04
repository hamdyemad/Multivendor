<?php

namespace App\Repositories;

use App\Actions\UserAction;
use App\Interfaces\UserInterface;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserInterface {

    public function __construct(public UserAction $userAction)
    {

    }

    public function login($request) {
        return $this->userAction->login($request);
    }

    public function forgetPassword($request) {
        return $this->userAction->forgetPassword($request);
    }

    public function resetPassword($request, $user) {
        return $this->userAction->resetPassword($request, $user);
    }

    public function logout() {
        return $this->userAction->logout();
    }
    

    public function createVendorAccount($data) {
        // Create user account
        $user = User::create([
            'uuid' => \Str::uuid(),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type_id' => UserType::VENDOR_TYPE, // Vendor type,
            'active' => $data['active'],
        ]);
        return $user;
    }
    public function updateVendorAccount($data) {
        $user = User::find($data['id'])->update([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'active' => $data['active'],
        ]);
        return $user;
    }


}

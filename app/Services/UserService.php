<?php

namespace App\Services;

use App\Interfaces\UserInterface;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class UserService {

    public function __construct(public UserInterface $userInterface)
    {

    }

    public function login($request) {
        return $this->userInterface->login($request);
    }

    public function forgetPassword($request) {
        return $this->userInterface->forgetPassword($request);
    }

    public function resetPassword($request, $user) {
        return $this->userInterface->resetPassword($request, $user);
    }

    public function logout() {
        return $this->userInterface->logout();
    }

    public function createVendorAccount($data) {
        return $this->userInterface->createVendorAccount($data);
    }
    public function updateVendorAccount($data) {
        return $this->userInterface->updateVendorAccount($data);
    }

}

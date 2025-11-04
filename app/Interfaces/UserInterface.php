<?php

namespace App\Interfaces;


interface UserInterface {
    public function login($cred);
    public function forgetPassword($cred);
    public function resetPassword($cred, $user);
    public function logout();
    public function createVendorAccount($data);
    public function updateVendorAccount($data);
}

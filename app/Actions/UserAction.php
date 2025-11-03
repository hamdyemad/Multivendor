<?php

namespace App\Actions;

use App\Interfaces\UserInterface;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Models\UserType;
use App\Traits\Res;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserAction {

    use Res;
    public function login($request) {
        $remember = $request->filled('remember');
        $user = User::where('email', $request->email)->first();
        
        if($user) {
            // Check if user account is inactive
            if(!$user->active) {
                return $this->sendData(__('auth.account_not_activated'), false);
            }
            
            // Check if user account is blocked
            if($user->block) {
                return $this->sendData(__('auth.account_blocked'), false);
            }
        }
        
        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password], $remember)){
            return $this->sendData('',true);
        }else{
            return $this->sendData(__('auth.invalid_credentials'), false);
        }
    }

    public function forgetPassword($request) {
        $user = User::where('email', $request->email)->first();
        try {
            $reset_code = rand(100000, 900000);
            $data = [
                'email' => $user->email,
                'code' => $reset_code
            ];
            $user->reset_code = Hash::make($reset_code);
            $user->reset_code_timestamp = Carbon::now()->addMinutes(15);
            $user->save();
            Mail::to("$user->email")->send(new ResetPasswordMail($data));
        } catch(Exception $e) {
        }
        return $this->sendData(__('auth.reset password sent to your email please check it'),true, ['uuid' => $user->uuid]);
    }

    public function resetPassword($request, $uuid) {
        $user = User::where('uuid', $uuid)->first();
        if (!$user) {
            return $this->sendData(__('auth.Email Not Exists'),false);
        }
        if (!Hash::check($request->reset_code, $user->reset_code)) {
            return $this->sendData(__('auth.The provided reset code is invalid.'),false);
        }
        if (Carbon::now()->gt($user->reset_code_timestamp)) {
            return $this->sendData(__('auth.reset code is ignored please reset again'),false);
        }
        $user->update([
            'password' => Hash::make($request->password),
            'reset_code' => null,
            'reset_code_timestamp' => null,
        ]);

        return $this->sendData(__('auth.your password changed success please login'),true);
    }

    public function logout() {
        Auth::logout();
        return $this->sendData(__('auth.logout success'),true);

    }

}

<?php

namespace App\Actions;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Traits\Res;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserAction {

    use Res, LogsActivity;

    public function login($request) {
        $remember = $request->filled('remember');
        $user = User::where('email', $request->email)->first();

        if($user) {
            // Check if user account is inactive
            if(!$user->active()) {
                try {
                    $this->logActivityForUser(
                        user: $user,
                        action: 'login_failed',
                        descriptionKey: 'activity_log.login_failed_inactive',
                        descriptionParams: [],
                        model: $user,
                        properties: ['email' => $user->email]
                    );
                } catch (\Exception $e) {
                    // Ignore logging errors
                }
                return $this->sendData(__('auth.account_not_activated'), false);
            }

            // Check if user account is blocked
            if(!$user->blocked()) {
                try {
                    $this->logActivityForUser(
                        user: $user,
                        action: 'login_failed',
                        descriptionKey: 'activity_log.login_failed_blocked',
                        descriptionParams: [],
                        model: $user,
                        properties: ['email' => $user->email]
                    );
                } catch (\Exception $e) {
                    // Ignore logging errors
                }
                return $this->sendData(__('auth.account_blocked'), false);
            }
        }

        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password], $remember)){
            // Log successful login
            try {
                $this->logActivity(
                    action: 'login',
                    descriptionKey: 'activity_log.login_success',
                    descriptionParams: [],
                    model: $user,
                    properties: ['email' => $user->email]
                );
            } catch (\Exception $e) {
                // Ignore logging errors
            }

            return $this->sendData('',true);
        }else{
            // Log failed login attempt - invalid credentials
            if ($user) {
                try {
                    $this->logActivityForUser(
                        user: $user,
                        action: 'login_failed',
                        descriptionKey: 'activity_log.login_failed_credentials',
                        descriptionParams: [],
                        model: $user,
                        properties: ['email' => $user->email]
                    );
                } catch (\Exception $e) {
                    // Ignore logging errors
                }
            }

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

            // Log password reset request
            $this->logActivityForUser(
                user: $user,
                action: 'password_reset_requested',
                descriptionKey: 'activity_log.password_reset_sent',
                properties: ['email' => $user->email],
                model: $user,
            );
        } catch(Exception $e) {
            // Log failed password reset request
            $this->logActivityForUser(
                user: $user,
                action: 'password_reset_failed',
                descriptionKey: 'activity_log.password_reset_email_failed',
                properties: ['error' => $e->getMessage()],
                model: $user,
            );
        }
        return $this->sendData(__('auth.reset password sent to your email please check it'),true, ['uuid' => $user->uuid]);
    }

    public function resetPassword($request, $uuid) {
        $user = User::where('uuid', $uuid)->first();
        if (!$user) {
            return $this->sendData(__('auth.Email Not Exists'),false);
        }

        if (!Hash::check($request->reset_code, $user->reset_code)) {
            $this->logActivityForUser(
                user: $user,
                action: 'password_reset_failed',
                descriptionKey: 'activity_log.password_reset_invalid_code',
                descriptionParams: [],
                model: $user,
                properties: ['email' => $user->email]
            );
            return $this->sendData(__('auth.The provided reset code is invalid.'),false);
        }

        if (Carbon::now()->gt($user->reset_code_timestamp)) {
            $this->logActivityForUser(
                user: $user,
                action: 'password_reset_failed',
                descriptionKey: 'activity_log.password_reset_expired_code',
                descriptionParams: [],
                model: $user,
                properties: ['email' => $user->email]
            );
            return $this->sendData(__('auth.reset code is ignored please reset again'),false);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_code' => null,
            'reset_code_timestamp' => null,
        ]);

        // Log successful password reset
        $this->logActivityForUser(
            user: $user,
            action: 'password_reset_success',
            descriptionKey: 'activity_log.password_reset_completed',
            descriptionParams: [],
            model: $user,
            properties: ['email' => $user->email]
        );

        return $this->sendData(__('auth.your password changed success please login'),true);
    }

    public function logout() {
        // Log logout before actually logging out
        $this->logActivity(
            action: 'logout',
            descriptionKey: 'activity_log.logout_success',
            descriptionParams: [],
            model: Auth::user(),
            properties: ['email' => Auth::user()->email]
        );

        Auth::logout();
        return $this->sendData(__('auth.logout success'),true);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\Request;
use Auth;
use App\Services\UserService;

class AuthController extends Controller {

    public function __construct(public UserService $userService)
    {

    }

    /**
     * Display login of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function login(){
        $data = [
            'title' => "Login",
            'description' => "Some description for the page",
        ];
        return view('auth.login', $data);
    }


    /**
     * make the user able to login
     *
     * @return
     */
    public function authenticate(LoginRequest $request){
        $res = $this->userService->login($request);
        if(isset($res) && $res['status']) {
            // Get country code from session or default
            $countryCode = strtolower(session('country_code', 'sa'));
            $locale = app()->getLocale() ?: 'en';

            // Build the dashboard URL directly to avoid duplication
            $dashboardUrl = "/{$locale}/{$countryCode}/admin/dashboard";

            return redirect()->intended($dashboardUrl)->with('success','Welcome back !');
        } else {
            return redirect()->route('login')->with('message', $res['message']);
        }
    }

    /**
     * make the user able to logout
     *
     * @return
     */
    public function logout(){
        $res = $this->userService->logout();
        if(isset($res) && $res['status']) {
            return redirect()->route('login')->with('success',$res['message']);
        }
    }


    /**
     * Display forget password of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function forgetPasswordView(Request $request){
        $data = [
            'title' => "Forget Password",
            'description' => "Some description for the page",
        ];
        return view('auth.forget_password', $data);
    }

    /**
     * Display forget password of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function forgetPassword(ForgetPasswordRequest $request){
        $res = $this->userService->forgetPassword($request);
        if(isset($res) && $res['status']) {
            return redirect(route('forgetPassword.reset', ['user' => $res['data']['uuid']]))->with('success', $res['message']);
        }
    }

    /**
     * Display forget password of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function resetPasswordView(Request $request, $uuid){
        $data = [
            'title' => "Reset Password",
            'description' => "Some description for the page",
            'user' => $uuid,
        ];
        return view('auth.reset_password', $data);
    }

    /**
     * Display forget password of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function resetPassword(ResetPasswordRequest $request, $uuid){
        $res = $this->userService->resetPassword($request, $uuid);
        if(isset($res)) {
            if(isset($res) && $res['status']) {
                return redirect(route('login'))->with('success', $res['message']);
            } else {
                return redirect(back())->with('error', $res['message']);
            }
        }
    }

}

<?php

namespace Modules\Customer\app\Http\Controllers;

use Modules\Customer\app\Services\Api\CustomerAuthService;
use Illuminate\Http\Request;

class EmailVerificationController
{
    public function __construct(private CustomerAuthService $authService)
    {
    }

    /**
     * Verify email token and redirect to landing page
     */
    public function verify($token)
    {
        $result = $this->authService->verifyEmailToken($token);

        if (!$result) {
            return redirect()->route('landing')->with('error', __('Invalid or expired verification link.'));
        }

        return redirect()->route('landing')->with('success', __('Email verified successfully!'));
    }

    /**
     * Store verification via form submission
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $result = $this->authService->verifyEmailToken($request->token);

        if (!$result) {
            return redirect()->route('landing')->with('error', __('Invalid or expired verification link.'));
        }

        return redirect()->route('landing')->with('success', __('Email verified successfully!'));
    }
}

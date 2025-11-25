<?php

namespace Modules\Customer\app\Http\Controllers;

use Modules\Customer\app\Services\Api\CustomerAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        Log::info('EmailVerificationController::verify() called', ['token' => substr($token, 0, 10) . '...']);

        $result = $this->authService->verifyEmailToken($token);

        Log::info('Verification result', ['result' => $result]);

        if (!$result) {
            Log::warning('Email verification failed', ['token' => substr($token, 0, 10) . '...']);
            return redirect()->route('landing')->with('error', __('Invalid or expired verification link.'));
        }

        Log::info('Email verification successful', ['token' => substr($token, 0, 10) . '...']);
        return redirect()->route('landing')->with('success', __('Email verified successfully!'));
    }

    /**
     * Store verification via form submission
     */
    public function store(Request $request)
    {
        Log::info('EmailVerificationController::store() called', ['token' => substr($request->token, 0, 10) . '...']);

        $request->validate([
            'token' => 'required|string',
        ]);

        $result = $this->authService->verifyEmailToken($request->token);

        Log::info('Verification result', ['result' => $result]);

        if (!$result) {
            Log::warning('Email verification failed', ['token' => substr($request->token, 0, 10) . '...']);
            return redirect()->route('landing')->with('error', __('Invalid or expired verification link.'));
        }

        Log::info('Email verification successful', ['token' => substr($request->token, 0, 10) . '...']);
        return redirect()->route('landing')->with('success', __('Email verified successfully!'));
    }
}

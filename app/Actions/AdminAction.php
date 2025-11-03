<?php

namespace App\Actions;

use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UserInterface;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Services\LanguageService;
use App\Traits\Res;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected RoleRepositoryInterface $roleRepositoryInterface
        )
    {
        
    }

    
}

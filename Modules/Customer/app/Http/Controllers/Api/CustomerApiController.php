<?php

namespace Modules\Customer\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\Customer\app\Http\Requests\Api\UpdateProfileRequest;
use Modules\Customer\app\Services\Api\CustomerApiService;
use Modules\Customer\app\Http\Requests\Api\ChangeLanguageRequest;
use Modules\Customer\app\Transformers\CustomerApiResource;

class CustomerApiController extends Controller
{
    use Res;

    public function __construct(protected CustomerApiService $customerService)
    {}

    /**
     * Get customer profile
     */
    public function profile(Request $request)
    {
        $customer = $this->customerService->getProfile($request->user());

        if(!$customer) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }

        return $this->sendRes(
            config('responses.profile_retrieved')[app()->getLocale()],
            true,
            CustomerApiResource::make($customer)
        );
    }

    /**
     * Update customer profile
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $validated = $request->validated();
        $customer = $this->customerService->updateProfile($request->user(), $validated);

        return $this->sendRes(
            config('responses.profile_updated_success')[app()->getLocale()],
            true,
            CustomerApiResource::make($customer)
        );
    }

    public function changeLanguage(ChangeLanguageRequest $request)
    {
        $validated = $request->validated();
        $customer = $this->customerService->changeLanguage($request->user(), $validated['lang']);

        return $this->sendRes(
            config('responses.language_changed')[app()->getLocale()],
            true,
        );
    }
}

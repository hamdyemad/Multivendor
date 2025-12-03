<?php

namespace Modules\Customer\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\Customer\app\Http\Requests\Api\UpdateProfileRequest;
use Modules\Customer\app\Services\Api\CustomerApiService;
use Modules\Customer\app\Http\Requests\Api\ChangeLanguageRequest;
use Modules\Customer\app\Transformers\CustomerApiResource;
use Modules\Customer\app\Repositories\Api\CustomerApiRepository;
use Modules\Customer\app\Transformers\AddressResource;

class CustomerApiController extends Controller
{
    use Res;

    public function __construct(
        protected CustomerApiService $customerService,
        protected CustomerApiRepository $repository
    ) {}

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

    /**
     * Get all customers (for order creation - public endpoint)
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'active' => true,
        ];

        $customers = $this->repository->getAllCustomers($filters, 100);

        return $this->sendRes(
            'Customers retrieved successfully',
            true,
            CustomerApiResource::collection($customers->items()),
            [],
            200
        );
    }

    /**
     * Get customer addresses (for order creation - public endpoint)
     */
    public function getAddresses($customerId)
    {
        $customer = $this->repository->getCustomerWithAddresses($customerId);

        if (!$customer) {
            return $this->sendRes(
                'Customer not found',
                false,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            'Customer addresses retrieved successfully',
            true,
            AddressResource::collection($customer->addresses),
            [],
            200
        );
    }

    /**
     * Get authenticated customer's addresses
     */
    public function myAddresses(Request $request)
    {
        $customer = $request->user();
        $addresses = $customer->addresses()->get();

        return $this->sendRes(
            'Your addresses retrieved successfully',
            true,
            AddressResource::collection($addresses),
            [],
            200
        );
    }
}

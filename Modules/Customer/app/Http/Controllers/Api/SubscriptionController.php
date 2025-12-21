<?php

namespace Modules\Customer\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Modules\Customer\app\Http\Requests\Api\StoreSubscriptionRequest;
use Modules\Customer\app\Models\Subscription;

class SubscriptionController extends Controller
{
    use Res;

    /**
     * Store a new subscription.
     */
    public function store(StoreSubscriptionRequest $request)
    {
        $validated = $request->validated();

        $subscription = Subscription::create([
            'email' => $validated['email'],
            'customer_id' => $validated['customer_id'] ?? auth()->id(),
        ]);

        return $this->sendRes(
            config('responses.subscribed_successfully')[app()->getLocale()],
            true,
            $subscription
        );
    }
}

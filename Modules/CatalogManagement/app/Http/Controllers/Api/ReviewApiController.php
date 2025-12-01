<?php

namespace Modules\CatalogManagement\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\DTOs\ReviewFilterDTO;
use Modules\CatalogManagement\app\Http\Requests\Api\StoreReviewRequest;
use Modules\CatalogManagement\app\Http\Resources\Api\ReviewResource;
use Modules\CatalogManagement\app\Services\Api\ReviewService;

class ReviewApiController extends Controller
{
    use Res;

    public function __construct(
        private ReviewService $reviewService
    ) {}

    /**
     * Store a new review (authenticated customers only)
     */
    public function store(StoreReviewRequest $request, $reviewableId, $reviewableType)
    {
        $data = $request->validated();
        if($reviewableType != 'products' && $reviewableType != 'vendors')
        {
            return $this->sendRes(
                config('responses.invalid_reviewable_type')[app()->getLocale()],
                true,
                [],
                [],
                404
            );
        }

        $review = $this->reviewService->createReview($data, $reviewableId, $reviewableType);

        if(!$review)
        {
            $message = $reviewableType == "products" ? config('responses.product_not_found')[app()->getLocale()] : config('responses.vendor_not_found')[app()->getLocale()];
            return $this->sendRes(
                $message,
                true,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            config('responses.review_sent_successfully')[app()->getLocale()],
            true,
            new ReviewResource($review),
            [],
            201
        );
    }

    /**
     * Get reviews for a specific vendor product (approved only)
     */
    public function getByReviewable(Request $request, $reviewableType, $reviewableId)
    {
        $dto = ReviewFilterDTO::fromRequest($request);
        $dto->reviewable_id = $reviewableId;
        $dto->reviewable_type = $reviewableType == "products" ? "VendorProduct" : "Vendor";
        $dto->status = 'approved';

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }


        $reviews = $this->reviewService->getReviews($dto);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            ReviewResource::collection($reviews),
            []
        );
    }

    /**
     * Get customer's reviews
     */
    public function getCustomerReviews(Request $request)
    {
        $customerId = auth('sanctum')->id();
        $dto = ReviewFilterDTO::fromRequest($request);
        $dto->customer_id = $customerId;

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $reviews = $this->reviewService->getReviewsByCustomer($dto);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            ReviewResource::collection($reviews),
            []
        );
    }

}

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
    public function store(StoreReviewRequest $request, $vendorProductId)
    {
        $data = $request->validated();

        $review = $this->reviewService->createReview($data, $vendorProductId);

        if(!$review)
        {
            return $this->sendRes(
                config('responses.product_not_found')[app()->getLocale()],
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
    public function getByVendorProduct(Request $request, $vendorProductId)
    {
        $dto = ReviewFilterDTO::fromRequest($request);
        $dto->vendor_product_id = $vendorProductId;
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

        $reviews = $this->reviewService->getReviewsByVendorProduct($dto);

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

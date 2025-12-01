<?php

namespace Modules\CatalogManagement\app\Services\Api;

use Modules\CatalogManagement\app\DTOs\ReviewFilterDTO;
use Modules\CatalogManagement\app\Interfaces\Api\ReviewRepositoryInterface;
use Modules\Vendor\app\DTOs\VendorFilterDTO;
use Modules\Vendor\app\Services\Api\VendorApiService;

class ReviewService
{
    public function __construct(
        private ReviewRepositoryInterface $reviewRepository,
        private ProductApiService $productService,
        private VendorApiService $vendorService,
    ) {}

    /**
     * Create a new review
     */
    public function createReview(array $data, string $reviewableId, string $reviewableType)
    {
        $data['customer_id'] = auth('sanctum')->id();
        $data['status'] = 'pending';

        if($reviewableType == 'products')
        {
            $result = $this->productService->findProduct($reviewableId);
        } elseif ($reviewableType == "vendors") {
            $dto = new VendorFilterDTO();
            $result = $this->vendorService->find($dto, $reviewableId);
        } else {
            return false;
        }

        if(!$result)
        {
            return false;
        }

        $data["reviewable_id"] = $result->id;
        $data["reviewable_type"] = class_basename($result);
        return $this->reviewRepository->createReview($data);
    }

    /**
     * Get reviews for a specific vendor product
     */
    public function getReviews(ReviewFilterDTO $dto)
    {
        return $this->reviewRepository->getAllReviews($dto);
    }

    /**
     * Get reviews by a specific customer
     */
    public function getReviewsByCustomer(ReviewFilterDTO $dto)
    {
        return $this->reviewRepository->getAllReviews($dto);
    }

}

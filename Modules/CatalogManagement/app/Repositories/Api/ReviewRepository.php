<?php

namespace Modules\CatalogManagement\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\DTOs\ReviewFilterDTO;
use Modules\CatalogManagement\app\Interfaces\Api\ReviewRepositoryInterface;
use Modules\CatalogManagement\app\Models\Review;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function __construct(
        private IsPaginatedAction $isPaginatedAction
    ) {}

    /**
     * Create a new review
     */
    public function createReview(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Review::create($data);
        });
    }

    /**
     * Get all reviews with filters and pagination
     */
    public function getAllReviews(ReviewFilterDTO $dto)
    {
        $filters = $dto->toArray();

        $query = Review::query()
            ->with(['vendorProduct', 'customer'])
            ->filter($filters);

        // Apply sorting
        $sortBy = $dto->sort_by ?? 'created_at';
        $sortType = $dto->sort_type ?? 'desc';
        $query->orderBy($sortBy, $sortType);

        // Handle pagination
        $paginated = $dto->paginated != null ? $dto->paginated : null;
        return $this->isPaginatedAction->handle($query, $dto->per_page, $paginated);
    }

}

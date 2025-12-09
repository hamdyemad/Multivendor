<?php

namespace Modules\CatalogManagement\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\CatalogManagement\app\Actions\BrandQueryAction;
use Modules\CatalogManagement\app\DTOs\BrandFilterDTO;
use Modules\CatalogManagement\app\Interfaces\Api\BrandApiRepositoryInterface;

class BrandApiRepository implements BrandApiRepositoryInterface
{
    public function __construct(protected BrandQueryAction $query, protected IsPaginatedAction $paginated) {}

    /**
     * Get all Brands with filters and pagination
     */
    public function getAllBrands(BrandFilterDTO $dto)
    {
        $filters = $dto->toArray();
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $dto->per_page, $dto->paginated);
        return $result;
    }

    /**
     * Get Brand by ID
     */
    public function find(BrandFilterDTO $dto, $id)
    {
        $filters = $dto->toArray();
        return $this->query->handle($filters)->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->firstOrFail();
    }

}

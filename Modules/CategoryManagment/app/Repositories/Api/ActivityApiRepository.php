<?php

namespace Modules\CategoryManagment\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\CategoryManagment\app\Actions\ActivityQueryAction;
use Modules\CategoryManagment\app\DTOs\ActivityFilterDTO;
use Modules\CategoryManagment\app\Interfaces\Api\ActivityApiRepositoryInterface;

class ActivityApiRepository implements ActivityApiRepositoryInterface
{

    public function __construct(protected ActivityQueryAction $query, protected IsPaginatedAction $paginated){}
    /**
     * Get all activities with filters and pagination
     */
    public function getAllActivities(ActivityFilterDTO $dto)
    {
        $filters = $dto->toArray();
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $dto->paginated, $dto->per_page);
        return $result;
    }


    /**
     * Get activity by ID
     */
    public function find(ActivityFilterDTO $dto, $id)
    {
        $filters = $dto->toArray();
        return $this->query->handle($filters)->with('activeDepartments')->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->firstOrFail();
    }

}

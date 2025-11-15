<?php

namespace Modules\CategoryManagment\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\CategoryManagment\app\Actions\ActivityQueryAction;
use Modules\CategoryManagment\app\Interfaces\Api\ActivityApiRepositoryInterface;

class ActivityApiRepository implements ActivityApiRepositoryInterface
{

    public function __construct(protected ActivityQueryAction $query, protected IsPaginatedAction $paginated){}
    /**
     * Get all activities with filters and pagination
     */
    public function getAllActivities(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }


    /**
     * Get activity by ID
     */
    public function find(array $filters = [], $id)
    {
        return $this->query->handle($filters)->with('activeDepartments')->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->first();
    }

}

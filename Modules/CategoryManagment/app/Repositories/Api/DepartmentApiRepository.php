<?php

namespace Modules\CategoryManagment\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\CategoryManagment\app\Actions\DepartmentQueryAction;
use Modules\CategoryManagment\app\Interfaces\Api\DepartmentApiRepositoryInterface;

class DepartmentApiRepository implements DepartmentApiRepositoryInterface
{

    public function __construct(protected DepartmentQueryAction $query, protected IsPaginatedAction $paginated){}
    /**
     * Get all Departments with filters and pagination
     */
    public function getAllDepartments(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }


    /**
     * Get Department by ID
     */
    public function find(array $filters = [], $id)
    {
        return $this->query->handle($filters)->with('activeCategories')->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->firstOrFail();
    }

}

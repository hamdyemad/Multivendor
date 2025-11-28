<?php

namespace Modules\CategoryManagment\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\CategoryManagment\app\Actions\DepartmentQueryAction;
use Modules\CategoryManagment\app\DTOs\DepartmentFilterDTO;
use Modules\CategoryManagment\app\Interfaces\Api\DepartmentApiRepositoryInterface;

class DepartmentApiRepository implements DepartmentApiRepositoryInterface
{

    public function __construct(protected DepartmentQueryAction $query, protected IsPaginatedAction $paginated){}
    /**
     * Get all Departments with filters and pagination
     */
    public function getAllDepartments(DepartmentFilterDTO $dto)
    {
        $filters = $dto->toArray();
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $dto->paginated, $dto->per_page);
        return $result;
    }


    /**
     * Get Department by ID
     */
    public function find(DepartmentFilterDTO $dto, $id)
    {
        $filters = $dto->toArray();
        return $this->query->handle($filters)->with('activeCategories')->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->firstOrFail();
    }

    /**
     * Get departments by brand ID or slug
     */
    public function getDepartmentsByBrand($brandId)
    {
        return $this->query->handle([])
            ->byBrand($brandId)
            ->distinct()
            ->get();
    }

}

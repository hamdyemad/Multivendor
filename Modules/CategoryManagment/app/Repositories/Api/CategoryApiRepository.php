<?php

namespace Modules\CategoryManagment\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\CategoryManagment\app\Actions\CategoryQueryAction;
use Modules\CategoryManagment\app\Interfaces\Api\CategoryApiRepositoryInterface;

class CategoryApiRepository implements CategoryApiRepositoryInterface
{

    public function __construct(protected CategoryQueryAction $query, protected IsPaginatedAction $paginated){}
    /**
     * Get all Categories with filters and pagination
     */
    public function getAllCategories(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }


    /**
     * Get Category by ID
     */
    public function find(array $filters = [], $id)
    {
        return $this->query->handle($filters)->with(['activeSubs', 'department'])->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->firstOrFail();
    }

    /**
     * Get categories by department ID or slug
     */
    public function getCategoriesByDepartment($departmentId)
    {
        return $this->query->handle([])
            ->byDepartment($departmentId)
            ->with('department')
            ->get();
    }

}

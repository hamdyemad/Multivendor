<?php

namespace Modules\CategoryManagment\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\CategoryManagment\app\Actions\SubCategoryQueryAction;
use Modules\CategoryManagment\app\Interfaces\Api\SubCategoryApiRepositoryInterface;

class SubCategoryApiRepository implements SubCategoryApiRepositoryInterface
{

    public function __construct(protected SubCategoryQueryAction $query, protected IsPaginatedAction $paginated){}
    /**
     * Get all Categories with filters and pagination
     */
    public function getAllSubCategories(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }


    /**
     * Get SubCategory by ID
     */
    public function find(array $filters = [], $id)
    {
        return $this->query->handle($filters)->with('category')->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->firstOrFail();
    }

    /**
     * Get sub-categories by category ID or slug
     */
    public function getSubCategoriesByCategory($categoryId)
    {
        return $this->query->handle([])
            ->byMain($categoryId)
            ->with('category')
            ->get();
    }

    /**
     * Get sub-category by ID or slug
     */
    public function getSubCategoryById($subCategoryId)
    {
        $subCategory = $this->query->handle([])
            ->byIdOrSlug($subCategoryId)
            ->with('category')
            ->first();

        return $subCategory ? collect([$subCategory]) : collect();
    }

}

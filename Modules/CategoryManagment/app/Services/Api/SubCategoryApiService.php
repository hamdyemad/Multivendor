<?php

namespace Modules\CategoryManagment\app\Services\Api;

use Modules\CategoryManagment\app\Interfaces\Api\SubCategoryApiRepositoryInterface;

class SubCategoryApiService
{
    protected $SubCategoryRepository;

    public function __construct(SubCategoryApiRepositoryInterface $SubCategoryRepository)
    {
        $this->SubCategoryRepository = $SubCategoryRepository;
    }

    /**
     * Get all SubCategories with filters and pagination
     */
    public function getAllSubCategories(array $filters = [])
    {
        return $this->SubCategoryRepository->getAllSubCategories($filters);
    }

    /**
     * Get SubCategory by ID
     */
    public function find(array $filters = [],$id)
    {
        return $this->SubCategoryRepository->find($filters, $id);
    }
}

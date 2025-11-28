<?php

namespace Modules\CategoryManagment\app\Services\Api;

use Modules\CategoryManagment\app\DTOs\CategoryFilterDTO;
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
    public function getAllSubCategories(CategoryFilterDTO $dto)
    {
        return $this->SubCategoryRepository->getAllSubCategories($dto);
    }

    /**
     * Get SubCategory by ID
     */
    public function find(CategoryFilterDTO $dto, $id)
    {
        return $this->SubCategoryRepository->find($dto, $id);
    }
}

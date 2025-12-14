<?php

namespace Modules\CategoryManagment\app\Services\Api;

use Modules\CategoryManagment\app\DTOs\CategoryFilterDTO;
use Modules\CategoryManagment\app\DTOs\DepartmentFilterDTO;
use Modules\CategoryManagment\app\Interfaces\Api\CategoryApiRepositoryInterface;
use Modules\CategoryManagment\app\Interfaces\Api\DepartmentApiRepositoryInterface;
use Modules\CategoryManagment\app\Interfaces\Api\SubCategoryApiRepositoryInterface;

class CategoryApiService
{
    public function __construct(
        protected CategoryApiRepositoryInterface $CategoryRepository,
        protected DepartmentApiRepositoryInterface $DepartmentRepository,
        protected SubCategoryApiRepositoryInterface $SubCategoryRepository,
    ) {}

    /**
     * Get all categories with filters and pagination
     */
    public function getAllCategories(CategoryFilterDTO $dto)
    {
        return $this->CategoryRepository->getAllCategories($dto);
    }

    /**
     * Get Category by ID
     */
    public function find(CategoryFilterDTO $dto, $id)
    {
        return $this->CategoryRepository->find($dto, $id);
    }

    /**
     * Get categories by filters (handles department, category, and sub-category hierarchy)
     */
    public function getCategoriesByFilters(array $filters)
    {
        // If department_id is provided, return main categories for that department
        if (!empty($filters['department_id'])) {
            $dto = new CategoryFilterDTO(department_id: $filters['department_id']);
            return $this->CategoryRepository->getAllCategories($dto);
        }

        // If main_category_id is provided, return sub-categories
        if (!empty($filters['main_category_id'])) {
            return $this->SubCategoryRepository->getSubCategoriesByCategory($filters['main_category_id']);
        }

        // If sub_category_id is provided, return the sub-category itself
        if (!empty($filters['sub_category_id'])) {
            return collect();
        }

        // If brand_id only, return all departments that have products from this brand
        if (!empty($filters['brand_id'])) {
            return $this->DepartmentRepository->getDepartmentsByBrand($filters['brand_id']);
        }

        // Return all active departments
        $dto = new DepartmentFilterDTO();
        return $this->DepartmentRepository->getAllDepartments($dto);
    }


    public function getCategoriesByIds(array $filters)
    {
        if (!empty($filters['main_category_id'])) {
            $dto = new CategoryFilterDTO();
            return $this->CategoryRepository->find($dto, $filters['main_category_id']);
        }

        if (!empty($filters['sub_category_id'])) {
            $dto = new CategoryFilterDTO();
            return $this->SubCategoryRepository->find($dto, $filters['sub_category_id']);
        }

        if (!empty($filters['department_id'])) {
            $dto = new DepartmentFilterDTO();
            return $this->DepartmentRepository->find($dto, $filters['department_id']);
        }

        // If brand_id only, return all departments that have products from this brand
        if (!empty($filters['brand_id'])) {
            return $this->DepartmentRepository->getDepartmentsByBrand($filters['brand_id']);
        }

        return [];
    }
}

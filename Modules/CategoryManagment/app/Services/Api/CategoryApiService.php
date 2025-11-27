<?php

namespace Modules\CategoryManagment\app\Services\Api;

use Modules\CategoryManagment\app\Interfaces\Api\CategoryApiRepositoryInterface;
use Modules\CategoryManagment\app\Interfaces\Api\DepartmentApiRepositoryInterface;
use Modules\CategoryManagment\app\Interfaces\Api\SubCategoryApiRepositoryInterface;

class CategoryApiService
{
    protected $CategoryRepository;
    protected $DepartmentRepository;
    protected $SubCategoryRepository;

    public function __construct(
        CategoryApiRepositoryInterface $CategoryRepository,
        DepartmentApiRepositoryInterface $DepartmentRepository,
        SubCategoryApiRepositoryInterface $SubCategoryRepository
    ) {
        $this->CategoryRepository = $CategoryRepository;
        $this->DepartmentRepository = $DepartmentRepository;
        $this->SubCategoryRepository = $SubCategoryRepository;
    }

    /**
     * Get all categories with filters and pagination
     */
    public function getAllCategories(array $filters = [])
    {
        return $this->CategoryRepository->getAllCategories($filters);
    }

    /**
     * Get Category by ID
     */
    public function find(array $filters = [], $id)
    {
        return $this->CategoryRepository->find($filters, $id);
    }

    /**
     * Get categories by filters (handles department, category, and sub-category hierarchy)
     */
    public function getCategoriesByFilters(array $filters)
    {
        // If department_id is provided, return main categories for that department
        if (!empty($filters['department_id'])) {
            return $this->CategoryRepository->getCategoriesByDepartment($filters['department_id']);
        }

        // If main_category_id is provided, return sub-categories
        if (!empty($filters['main_category_id'])) {
            return $this->SubCategoryRepository->getSubCategoriesByCategory($filters['main_category_id']);
        }

        // If sub_category_id is provided, return the sub-category itself
        if (!empty($filters['sub_category_id'])) {
            return $this->SubCategoryRepository->getSubCategoryById($filters['sub_category_id']);
        }

        // If brand_id only, return all departments that have products from this brand
        if (!empty($filters['brand_id'])) {
            return $this->DepartmentRepository->getDepartmentsByBrand($filters['brand_id']);
        }

        // Return all active departments
        return $this->DepartmentRepository->getAllDepartments(['active' => true]);
    }
}

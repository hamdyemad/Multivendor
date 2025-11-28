<?php

namespace Modules\CategoryManagment\app\Interfaces\Api;

use Modules\CategoryManagment\app\DTOs\CategoryFilterDTO;

interface CategoryApiRepositoryInterface
{
    /**
     * Get all activities with filters and pagination
     */
    public function getAllCategories(CategoryFilterDTO $filters);

    /**
     * Get activity by ID
     */
    public function find(CategoryFilterDTO $filters, $id);

    /**
     * Get categories by department ID or slug
     */
    public function getCategoriesByDepartment($departmentId);
}

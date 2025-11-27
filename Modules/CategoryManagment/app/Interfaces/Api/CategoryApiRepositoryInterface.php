<?php

namespace Modules\CategoryManagment\app\Interfaces\Api;

interface CategoryApiRepositoryInterface
{
    /**
     * Get all activities with filters and pagination
     */
    public function getAllCategories(array $filters = []);

    /**
     * Get activity by ID
     */
    public function find(array $filters = [], $id);

    /**
     * Get categories by department ID or slug
     */
    public function getCategoriesByDepartment($departmentId);
}

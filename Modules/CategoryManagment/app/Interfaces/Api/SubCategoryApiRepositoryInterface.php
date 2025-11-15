<?php

namespace Modules\CategoryManagment\app\Interfaces\Api;

interface SubCategoryApiRepositoryInterface
{
    /**
     * Get all activities with filters and pagination
     */
    public function getAllSubCategories(array $filters = []);

    /**
     * Get activity by ID
     */
    public function find(array $filters = [], $id);
}

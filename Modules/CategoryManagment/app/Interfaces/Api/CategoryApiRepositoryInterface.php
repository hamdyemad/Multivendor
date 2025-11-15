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
}

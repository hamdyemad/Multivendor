<?php

namespace Modules\CategoryManagment\app\Interfaces\Api;

interface DepartmentApiRepositoryInterface
{
    /**
     * Get all activities with filters and pagination
     */
    public function getAllDepartments(array $filters = []);

    /**
     * Get activity by ID
     */
    public function find(array $filters = [], $id);
}

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

    /**
     * Get departments by brand ID or slug
     */
    public function getDepartmentsByBrand($brandId);
}

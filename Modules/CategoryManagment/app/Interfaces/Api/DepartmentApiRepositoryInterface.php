<?php

namespace Modules\CategoryManagment\app\Interfaces\Api;

use Modules\CategoryManagment\app\DTOs\DepartmentFilterDTO;

interface DepartmentApiRepositoryInterface
{
    /**
     * Get all activities with filters and pagination
     */
    public function getAllDepartments(DepartmentFilterDTO $filters);

    /**
     * Get activity by ID
     */
    public function find(DepartmentFilterDTO $filters, $id);

    /**
     * Get departments by brand ID or slug
     */
    public function getDepartmentsByBrand($brandId);
}

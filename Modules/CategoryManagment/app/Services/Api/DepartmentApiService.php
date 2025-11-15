<?php

namespace Modules\CategoryManagment\app\Services\Api;

use Modules\CategoryManagment\app\Interfaces\Api\DepartmentApiRepositoryInterface;

class DepartmentApiService
{
    protected $DepartmentRepository;

    public function __construct(DepartmentApiRepositoryInterface $DepartmentRepository)
    {
        $this->DepartmentRepository = $DepartmentRepository;
    }

    /**
     * Get all Departments with filters and pagination
     */
    public function getAllDepartments(array $filters = [])
    {
        return $this->DepartmentRepository->getAllDepartments($filters);
    }

    /**
     * Get Department by ID
     */
    public function find(array $filters = [],$id)
    {
        return $this->DepartmentRepository->find($filters, $id);
    }
}

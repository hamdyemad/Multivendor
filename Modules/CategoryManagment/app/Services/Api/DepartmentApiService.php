<?php

namespace Modules\CategoryManagment\app\Services\Api;

use Modules\CategoryManagment\app\DTOs\DepartmentFilterDTO;
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
    public function getAllDepartments(DepartmentFilterDTO $dto)
    {
        return $this->DepartmentRepository->getAllDepartments($dto);
    }

    /**
     * Get Department by ID
     */
    public function find(DepartmentFilterDTO $dto, $id)
    {
        return $this->DepartmentRepository->find($dto, $id);
    }
}

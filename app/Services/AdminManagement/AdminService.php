<?php

namespace App\Services\AdminManagement;

use App\Repositories\AdminManagement\AdminRepository;

class AdminService
{
    public function __construct(protected AdminRepository $adminRepository)
    {
    }

    public function getAdminsQuery(array $filters = [], $orderBy = null, string $orderDirection = 'desc')
    {
        return $this->adminRepository->getAdminsQuery($filters, $orderBy, $orderDirection);
    }

    public function getAdminById(int $id)
    {
        return $this->adminRepository->getAdminById($id);
    }

    public function createAdmin(array $data)
    {
        return $this->adminRepository->createAdmin($data);
    }

    public function updateAdmin(int $id, array $data)
    {
        return $this->adminRepository->updateAdmin($id, $data);
    }

    public function deleteAdmin(int $id)
    {
        return $this->adminRepository->deleteAdmin($id);
    }
}

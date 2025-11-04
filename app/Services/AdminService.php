<?php

namespace App\Services;

use App\Repositories\AdminRepository;

class AdminService
{
    public function __construct(protected AdminRepository $adminRepository)
    {
    }

    public function getAdminsQuery(array $filters = [])
    {
        return $this->adminRepository->getAdminsQuery($filters);
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

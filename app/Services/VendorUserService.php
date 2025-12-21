<?php

namespace App\Services;

use App\Repositories\VendorUserRepository;

class VendorUserService
{
    public function __construct(protected VendorUserRepository $vendorUserRepository)
    {
    }

    public function getVendorUsersQuery(array $filters = [])
    {
        return $this->vendorUserRepository->getVendorUsersQuery($filters);
    }

    public function getVendorUserById(int $id)
    {
        return $this->vendorUserRepository->getVendorUserById($id);
    }

    public function createVendorUser(array $data)
    {
        return $this->vendorUserRepository->createVendorUser($data);
    }

    public function updateVendorUser(int $id, array $data)
    {
        return $this->vendorUserRepository->updateVendorUser($id, $data);
    }

    public function deleteVendorUser(int $id)
    {
        return $this->vendorUserRepository->deleteVendorUser($id);
    }

    public function changeStatus(int $id, $status, $type)
    {
        return $this->vendorUserRepository->changeStatus($id, $status, $type);
    }
}

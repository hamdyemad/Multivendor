<?php

namespace Modules\Refund\app\Services;

use Modules\Refund\app\Interfaces\RefundRequestRepositoryInterface;
use Modules\Refund\app\Services\RefundNotificationService;

class RefundRequestService
{
    protected $repository;
    protected $notificationService;

    public function __construct(
        RefundRequestRepositoryInterface $repository,
        RefundNotificationService $notificationService
    ) {
        $this->repository = $repository;
        $this->notificationService = $notificationService;
    }

    public function getAllRefunds(array $filters, int $perPage = 15)
    {
        return $this->repository->getAllPaginated($filters, $perPage);
    }

    public function getRefundById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function createRefund(array $data, $user)
    {
        // Create refund through repository (observer will send notifications)
        return $this->repository->createRefundWithVendorSplit($data, $user);
    }

    public function updateRefundStatus(int $id, array $data, $user)
    {
        return $this->repository->updateRefundStatus($id, $data, $user);
    }

    public function cancelRefund(int $id, $user)
    {
        return $this->repository->cancelRefund($id, $user);
    }

    public function getStatistics(array $filters)
    {
        return $this->repository->getStatistics($filters);
    }

    public function canUserAccessRefund(int $id, $user): bool
    {
        return $this->repository->canUserAccessRefund($id, $user);
    }
}

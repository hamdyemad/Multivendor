<?php

namespace Modules\CategoryManagment\app\Services\Api;

use Modules\CategoryManagment\app\DTOs\ActivityFilterDTO;
use Modules\CategoryManagment\app\Interfaces\Api\ActivityApiRepositoryInterface;

class ActivityApiService
{
    protected $activityRepository;

    public function __construct(ActivityApiRepositoryInterface $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * Get all activities with filters and pagination
     */
    public function getAllActivities(ActivityFilterDTO $dto)
    {
        return $this->activityRepository->getAllActivities($dto);
    }

    /**
     * Get activity by ID
     */
    public function find(ActivityFilterDTO $dto, $id)
    {
        return $this->activityRepository->find($dto, $id);
    }
}

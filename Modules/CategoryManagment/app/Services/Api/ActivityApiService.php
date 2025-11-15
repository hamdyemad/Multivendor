<?php

namespace Modules\CategoryManagment\app\Services\Api;

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
    public function getAllActivities(array $filters = [])
    {
        return $this->activityRepository->getAllActivities($filters);
    }

    /**
     * Get activity by ID
     */
    public function find(array $filters = [],$id)
    {
        return $this->activityRepository->find($filters, $id);
    }
}

<?php

namespace Modules\CategoryManagment\app\Interfaces\Api;

use Modules\CategoryManagment\app\DTOs\ActivityFilterDTO;

interface ActivityApiRepositoryInterface
{
    /**
     * Get all activities with filters and pagination
     */
    public function getAllActivities(ActivityFilterDTO $filters);

    /**
     * Get activity by ID
     */
    public function find(ActivityFilterDTO $filters, $id);
}

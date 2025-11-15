<?php

namespace Modules\CategoryManagment\app\Interfaces\Api;

interface ActivityApiRepositoryInterface
{
    /**
     * Get all activities with filters and pagination
     */
    public function getAllActivities(array $filters = []);

    /**
     * Get activity by ID
     */
    public function find(array $filters = [], $id);
}

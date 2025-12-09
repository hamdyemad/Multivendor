<?php

namespace Modules\CategoryManagment\app\Interfaces;

interface ActivityRepositoryInterface
{
    /**
     * Get all activities with filters and pagination
     */
    public function getAllActivities(int $perPage, array $filters = []);

    /**
     * Get activities query for DataTables
     */
    public function getActivitiesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc');

    /**
     * Get activities query for Select2 AJAX
     */
    public function getAllActivitiesQuery(array $filters = []);

    /**
     * Get activity by ID
     */
    public function getActivityById($id);

    /**
     * Create a new activity
     */
    public function createActivity(array $data);

    /**
     * Update activity
     */
    public function updateActivity(int $id, array $data);

    /**
     * Delete activity
     */
    public function deleteActivity(int $id);

    /**
     * Get active activities
     */
    public function getActiveActivities();
}

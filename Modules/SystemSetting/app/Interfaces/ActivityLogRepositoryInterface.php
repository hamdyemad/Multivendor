<?php

namespace Modules\SystemSetting\app\Interfaces;

interface ActivityLogRepositoryInterface
{
    /**
     * Get all activity logs with filters and pagination
     */
    public function getAllActivityLogs(array $filters = [], ?int $perPage = 15);

    /**
     * Get activity logs query for DataTables
     */
    public function getActivityLogsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc');

    /**
     * Get activity log by ID
     */
    public function getActivityLogById(int $id);

    /**
     * Get activity logs by user ID
     */
    public function getActivityLogsByUser(int $userId);

    /**
     * Get activity logs by action
     */
    public function getActivityLogsByAction(string $action);

    /**
     * Get activity logs by date range
     */
    public function getActivityLogsByDateRange($startDate, $endDate);
}

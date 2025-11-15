<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Support\Facades\Log;

class ActivityLogService
{
    protected $activityLogRepository;

    public function __construct(ActivityLogRepositoryInterface $activityLogRepository)
    {
        $this->activityLogRepository = $activityLogRepository;
    }

    /**
     * Get all activity logs with filters and pagination
     */
    public function getAllActivityLogs(array $filters = [], ?int $perPage = 15)
    {
        try {
            return $this->activityLogRepository->getAllActivityLogs($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching activity logs: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get activity logs query for DataTables
     */
    public function getActivityLogsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->activityLogRepository->getActivityLogsQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching activity logs query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get activity log by ID
     */
    public function getActivityLogById(int $id)
    {
        try {
            return $this->activityLogRepository->getActivityLogById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching activity log: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get activity logs by user ID
     */
    public function getActivityLogsByUser(int $userId)
    {
        try {
            return $this->activityLogRepository->getActivityLogsByUser($userId);
        } catch (\Exception $e) {
            Log::error('Error fetching activity logs by user: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get activity logs by action
     */
    public function getActivityLogsByAction(string $action)
    {
        try {
            return $this->activityLogRepository->getActivityLogsByAction($action);
        } catch (\Exception $e) {
            Log::error('Error fetching activity logs by action: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get activity logs by date range
     */
    public function getActivityLogsByDateRange($startDate, $endDate)
    {
        try {
            return $this->activityLogRepository->getActivityLogsByDateRange($startDate, $endDate);
        } catch (\Exception $e) {
            Log::error('Error fetching activity logs by date range: ' . $e->getMessage());
            throw $e;
        }
    }
}

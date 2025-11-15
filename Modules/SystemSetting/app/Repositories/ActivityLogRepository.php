<?php

namespace Modules\SystemSetting\app\Repositories;

use App\Models\ActivityLog;
use Modules\SystemSetting\app\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Support\Facades\Log;

class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    /**
     * Get all activity logs with filters and pagination
     */
    public function getAllActivityLogs(array $filters = [], ?int $perPage = 15)
    {
        try {
            return $this->getActivityLogsQuery($filters)
                ->paginate($perPage);
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
            $query = ActivityLog::query();

            // Search filter
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('description_key', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('email', 'like', "%{$search}%");
                        });
                });
            }

            // User filter
            if (!empty($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }

            // Action filter
            if (!empty($filters['action'])) {
                $query->where('action', $filters['action']);
            }

            // Model filter
            if (!empty($filters['model'])) {
                $query->where('model', $filters['model']);
            }

            // Date range filters
            if (!empty($filters['created_date_from'])) {
                $query->whereDate('created_at', '>=', $filters['created_date_from']);
            }

            if (!empty($filters['created_date_to'])) {
                $query->whereDate('created_at', '<=', $filters['created_date_to']);
            }

            // Ordering
            if ($orderBy) {
                if (is_array($orderBy)) {
                    foreach ($orderBy as $column => $direction) {
                        $query->orderBy($column, $direction ?? $orderDirection);
                    }
                } else {
                    $query->orderBy($orderBy, $orderDirection);
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Include user relationship
            $query->with('user');

            return $query;
        } catch (\Exception $e) {
            Log::error('Error building activity logs query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get activity log by ID
     */
    public function getActivityLogById(int $id)
    {
        try {
            $log = ActivityLog::with('user')->find($id);
            if (!$log) {
                throw new \Exception('Activity log not found');
            }
            return $log;
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
            return ActivityLog::where('user_id', $userId)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
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
            return ActivityLog::where('action', $action)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
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
            return ActivityLog::whereBetween('created_at', [$startDate, $endDate])
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching activity logs by date range: ' . $e->getMessage());
            throw $e;
        }
    }
}

<?php

namespace Modules\SystemSetting\app\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\SystemSetting\app\Services\ActivityLogService;
use App\Services\LanguageService;

class ActivityLogAction
{
    public function __construct(
        protected ActivityLogService $activityLogService,
        protected LanguageService $languageService
    ) {}

    /**
     * Get datatable data for activity logs
     */
    public function getDatatableData(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);

        // Get search value from custom parameter or DataTables default
        $searchValue = $request->get('search');
        if (is_array($searchValue)) {
            $searchValue = $searchValue['value'] ?? '';
        }

        $orderColumnIndex = $request->get('order')[0]['column'] ?? 0;
        $orderDirection = $request->get('order')[0]['dir'] ?? 'asc';

        Log::info('DataTable Order:', [
            'column_index' => $orderColumnIndex,
            'direction' => $orderDirection
        ]);

        // Get filter parameters
        $userId = $request->get('user_id');
        $action = $request->get('action');
        $model = $request->get('model');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'user_id' => $userId,
            'action' => $action,
            'model' => $model,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        // Get total records before filtering
        $totalRecords = $this->activityLogService->getActivityLogsQuery([])->count();

        // Get activity logs with filters
        $baseQuery = $this->activityLogService->getActivityLogsQuery($filters);
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();

        // Prepare sorting parameters
        $orderBy = $this->prepareSorting($request, $orderColumnIndex, $orderDirection);

        // Get activity logs with sorting applied
        $sortedQuery = $this->activityLogService->getActivityLogsQuery($filters, $orderBy, $orderDirection);

        // Apply pagination
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $activityLogs = $sortedQuery->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $data = $this->formatDataForDataTables($activityLogs);

        return [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'current_page' => $activityLogs->currentPage(),
            'last_page' => $activityLogs->lastPage(),
            'per_page' => $activityLogs->perPage(),
            'total' => $activityLogs->total(),
            'from' => $activityLogs->firstItem(),
            'to' => $activityLogs->lastItem()
        ];
    }

    /**
     * Prepare sorting parameters
     */
    protected function prepareSorting(Request $request, int $orderColumnIndex, string $orderDirection)
    {
        $orderBy = null;
        $sortBy = $request->get('sort_by');

        if ($sortBy) {
            $orderBy = $sortBy;
        } else {
            // Default sorting by created_at
            $orderBy = 'created_at';
        }

        return $orderBy;
    }

    /**
     * Format activity logs data for DataTables
     */
    protected function formatDataForDataTables($activityLogs)
    {
        $data = [];

        foreach ($activityLogs as $log) {
            $data[] = [
                'id' => $log->id,
                'user_name' => $log->user ? $log->user->email : __('System'),
                'action' => __("activity_log.actions.{$log->action}"),
                'model' => __("activity_log.models." . class_basename($log->model ?? 'Unknown')),
                'description' => $log->translated_description,
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at,
                'created_at_formatted' => $log->created_at->diffForHumans(),
            ];
        }

        return $data;
    }
}

<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Actions\ActivityLogAction;
use Modules\SystemSetting\app\Services\ActivityLogService;
use App\Services\LanguageService;

class ActivityLogController extends Controller
{
    public function __construct(
        protected ActivityLogService $activityLogService,
        protected LanguageService $languageService,
        protected ActivityLogAction $activityLogAction
    )
    {
        $this->middleware('can:settings.logs.view')->only(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = [
            'title' => __('systemsetting::activity_log.activity_logs_management'),
        ];
        return view('systemsetting::activity-log.index', $data);
    }

    /**
     * Get activity logs data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $data = $this->activityLogAction->getDatatableData($request);
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $activityLog = $this->activityLogService->getActivityLogById($id);
            $data = [
                'activity_log' => $activityLog,
                'title' => __('systemsetting::activity_log.view_activity_log'),
            ];
            return view('systemsetting::activity-log.show', $data);
        } catch (\Exception $e) {
            return redirect()->route('admin.system-settings.activity-logs.index')
                ->with('error', __('Activity log not found'));
        }
    }
}

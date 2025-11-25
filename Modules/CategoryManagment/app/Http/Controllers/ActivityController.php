<?php

namespace Modules\CategoryManagment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Modules\CategoryManagment\app\Services\ActivityService;
use Modules\CategoryManagment\app\Http\Requests\ActivityRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CategoryManagment\app\Actions\ActivityAction;

class ActivityController extends Controller
{

    public function __construct(protected ActivityService $activityService,
    protected LanguageService $languageService,
    protected ActivityAction $activityAction)
    {
        $this->middleware('can:activities.index')->only(['index']);
        $this->middleware('can:activities.show')->only(['show']);
        $this->middleware('can:activities.create')->only(['create', 'store']);
        $this->middleware('can:activities.edit')->only(['edit', 'update', 'changeStatus']);
        $this->middleware('can:activities.delete')->only(['destroy']);
    }

    /**
     * Datatable endpoint for server-side processing
     */

    public function datatable(Request $request)
    {

        // Handle search parameter - could be string (custom input) or array (DataTables)
        $search = $request->get('search');
        if (is_array($search)) {
            $searchValue = $search['value'] ?? null;
        } else {
            $searchValue = $search;
        }

        $data = [
            'page' => $request->get('page', 1),
            'draw' => $request->get('draw', 1),
            'start' => $request->get('start', 0),
            'length' => $request->get('length', 10),
            'orderColumnIndex' => $request->get('order')[0]['column'] ?? 0,
            'orderDirection' => $request->get('order')[0]['dir'] ?? 'desc',
            'search' => $searchValue,
            'active' => $request->get('active'),
            'created_date_from' => $request->get('created_date_from'),
            'created_date_to' => $request->get('created_date_to'),
        ];

        try {
            $response = $this->activityAction->getDataTable($data);

            Log::info('Activity Datatable Response', [
                'data_count' => count($response['data']),
                'totalRecords' => $response['totalRecords'],
                'filteredRecords' => $response['filteredRecords']
            ]);

            return response()->json([
                'draw' => $data['draw'],
                'data' => $response['data'],
                'recordsTotal' => $response['totalRecords'],
                'recordsFiltered' => $response['filteredRecords'],
                'current_page' => $response['dataPaginated']->currentPage(),
                'last_page' => $response['dataPaginated']->lastPage(),
                'per_page' => $response['dataPaginated']->perPage(),
                'total' => $response['dataPaginated']->total(),
                'from' => $response['dataPaginated']->firstItem(),
                'to' => $response['dataPaginated']->lastItem()
            ]);
        } catch (\Exception $e) {
            Log::error('Activity Datatable Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'draw' => $data['draw'],
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function activitySearch(Request $request) {
        return $this->activityService->searchForSelect2($request->q, $request->page);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();
        return view('categorymanagment::activity.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        return view('categorymanagment::activity.form', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ActivityRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->activityService->createActivity($validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Activity created successfully'),
                    'redirect' => route('admin.category-management.activities.index')
                ]);
            }

            return redirect()->route('admin.category-management.activities.index')
                ->with('success', __('Activity created successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating activity: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating activity: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $activity = $this->activityService->getActivityById($id);
            $languages = $this->languageService->getAll();
            return view('categorymanagment::activity.view', compact('activity', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.activities.index')
                ->with('error', __('Activity not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $activity = $this->activityService->getActivityById($id);
            return view('categorymanagment::activity.form', compact('activity', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.activities.index')
                ->with('error', __('Activity not found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ActivityRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->activityService->updateActivity($id, $validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Activity updated successfully'),
                    'redirect' => route('admin.category-management.activities.index')
                ]);
            }

            return redirect()->route('admin.category-management.activities.index')
                ->with('success', __('Activity updated successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating activity: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating activity: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->activityService->deleteActivity($id);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Activity deleted successfully'),
                    'redirect' => route('admin.category-management.activities.index')
                ]);
            }

            return redirect()->route('admin.category-management.activities.index')
                ->with('success', __('Activity deleted successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error deleting activity: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.category-management.activities.index')
                ->with('error', __('Error deleting activity: ') . $e->getMessage());
        }
    }

    /**
     * Change the status of the specified activity.
     */
    public function changeStatus(Request $request, string $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:1,2'
            ]);

            $activity = $this->activityService->getActivityById($id);

            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'message' => __('categorymanagment::activity.activity_not_found')
                ], 404);
            }

            // Convert status: 1 = active (true), 2 = inactive (false)
            $newStatus = $request->status == 1;

            // Check if status is already set to the requested value
            if ($activity->active == $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => __('categorymanagment::activity.status_already_set')
                ]);
            }

            // Update the status
            $activity->active = $newStatus;
            $activity->save();

            Log::info('Activity status changed', [
                'activity_id' => $id,
                'new_status' => $newStatus,
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('categorymanagment::activity.status_changed_successfully'),
                'new_status' => $newStatus,
                'status_text' => $newStatus ? __('categorymanagment::activity.active') : __('categorymanagment::activity.inactive')
            ]);

        } catch (\Exception $e) {
            Log::error('Error changing activity status: ' . $e->getMessage(), [
                'activity_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('categorymanagment::activity.error_changing_status')
            ], 500);
        }
    }
}

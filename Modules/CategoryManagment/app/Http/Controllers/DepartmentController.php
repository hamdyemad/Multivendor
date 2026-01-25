<?php

namespace Modules\CategoryManagment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CategoryManagment\app\Http\Requests\DepartmentRequest;
use Modules\CategoryManagment\app\Services\DepartmentService;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Modules\CategoryManagment\app\Actions\DepartmentAction;

class DepartmentController extends Controller
{

    public function __construct(
        protected DepartmentService $departmentService,
        protected LanguageService $languageService,
        protected DepartmentAction $departmentAction
    )
    {
        $this->middleware('can:departments.index')->only(['index', 'show']);
        $this->middleware('can:departments.create')->only(['create', 'store']);
        $this->middleware('can:departments.edit')->only(['edit', 'update', 'reorder']);
        $this->middleware('can:departments.change-status')->only(['changeStatus']);
        $this->middleware('can:departments.delete')->only(['destroy']);
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable(Request $request)
    {
        $data = [
            'page' => $request->get('page', 1),
            'draw' => $request->get('draw', 1),
            'start' => $request->get('start', 0),
            'length' => $request->get('length', 10),
            'per_page' => $request->get('per_page', $request->get('length', 10)),
            'sort_column' => $request->get('sort_column', 'sort_number'),
            'sort_direction' => $request->get('sort_direction', 'asc'),
            'search' => $request->get('search'),
            'active' => $request->get('active'),
            'view_status' => $request->get('view_status'),
            'created_date_from' => $request->get('created_date_from'),
            'created_date_to' => $request->get('created_date_to'),
        ];

        Log::info('Department Controller - DataTable Request', [
            'sort_column' => $data['sort_column'],
            'sort_direction' => $data['sort_direction'],
        ]);

        try {
            $response = $this->departmentAction->getDataTable($data);

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
            return response()->json([
                'draw' => $data['draw'],
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $languages = $this->languageService->getAll();
            return view('categorymanagment::department.index', compact('languages'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', trans('categorymanagment::department.error_loading_departments'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($lang, $countryCode)
    {
        try {
            $languages = $this->languageService->getAll();
            return view('categorymanagment::department.form', compact('languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.departments.index')
                ->with('error', trans('categorymanagment::department.error_loading_form'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($lang, $countryCode, DepartmentRequest $request)
    {
        $validated = $request->validated();

        \Log::info('Validated Department Data:', $validated);

        try {
            $this->departmentService->createDepartment($validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('categorymanagment::department.department_created'),
                    'redirect' => route('admin.category-management.departments.index')
                ]);
            }

            return redirect()->route('admin.category-management.departments.index')
                ->with('success', trans('categorymanagment::department.department_created'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('categorymanagment::department.error_creating_department')
                ], 500);
            }

            return redirect()->back()
                ->with('error', trans('categorymanagment::department.error_creating_department'))
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $department = $this->departmentService->getDepartmentById($id);
            return view('categorymanagment::department.view', compact('department', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.departments.index')
                ->with('error', trans('categorymanagment::department.department_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $department = $this->departmentService->getDepartmentById($id);
            return view('categorymanagment::department.form', compact('department', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.departments.index')
                ->with('error', trans('categorymanagment::department.department_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($lang, $countryCode, DepartmentRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->departmentService->updateDepartment($id, $validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('categorymanagment::department.department_updated'),
                    'redirect' => route('admin.category-management.departments.index')
                ]);
            }

            return redirect()->route('admin.category-management.departments.index')
                ->with('success', trans('categorymanagment::department.department_updated'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('categorymanagment::department.error_updating_department'),
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', trans('categorymanagment::department.error_updating_department'))
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $countryCode, string $id)
    {
        try {
            $this->departmentService->deleteDepartment($id);

            return response()->json([
                'success' => true,
                'message' => trans('categorymanagment::department.department_deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('categorymanagment::department.error_deleting_department')
            ], 500);
        }
    }

    /**
     * Get departments by vendor for cascading dropdown
     */
    public function getDepartmentsByVendor(Request $request)
    {
        try {
            $vendorId = $request->get('vendor_id');

            if (!$vendorId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vendor ID is required'
                ], 400);
            }

            // Get all departments (you can add vendor filtering logic here if needed)
            $departments = $this->departmentService->getAllDepartments([], 0);

            $departmentsData = $departments->map(function($department) {
                return [
                    'id' => $department->id,
                    'name' => $department->getTranslation('name', app()->getLocale()) ?? $department->name
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $departmentsData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error loading departments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change the status of the specified department.
     */
    public function changeStatus($lang, $countryCode, Request $request, string $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:1,2'
            ]);

            $department = $this->departmentService->getDepartmentById($id);

            if (!$department) {
                return response()->json([
                    'success' => false,
                    'message' => __('categorymanagment::department.department_not_found')
                ], 404);
            }

            // Convert status: 1 = active (true), 2 = inactive (false)
            $newStatus = $request->status == 1;

            // Check if status is already set to the requested value
            if ($department->active == $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => __('categorymanagment::department.status_already_set')
                ]);
            }

            // Update the status
            $department->active = $newStatus;
            $department->save();

            Log::info('Department status changed', [
                'department_id' => $id,
                'new_status' => $newStatus,
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('categorymanagment::department.status_changed_successfully'),
                'new_status' => $newStatus,
                'status_text' => $newStatus ? __('categorymanagment::department.active') : __('categorymanagment::department.inactive')
            ]);

        } catch (\Exception $e) {
            Log::error('Error changing department status: ' . $e->getMessage(), [
                'department_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('categorymanagment::department.error_changing_status')
            ], 500);
        }
    }

    /**
     * Change the view status of the specified department.
     */
    public function changeViewStatus($lang, $countryCode, Request $request, string $id)
    {
        try {
            $request->validate([
                'view_status' => 'required|in:0,1'
            ]);

            $department = $this->departmentService->getDepartmentById($id);

            if (!$department) {
                return response()->json([
                    'success' => false,
                    'message' => __('categorymanagment::department.department_not_found')
                ], 404);
            }

            $newStatus = (bool) $request->view_status;
            $department->view_status = $newStatus;
            $department->save();

            Log::info('Department view_status changed', [
                'department_id' => $id,
                'new_view_status' => $newStatus,
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('categorymanagment::department.status_changed_successfully'),
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Error changing department view_status: ' . $e->getMessage(), [
                'department_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('categorymanagment::department.error_changing_status')
            ], 500);
        }
    }

    /**
     * Reorder departments by updating sort_number
     * Swaps sort numbers instead of renumbering all items
     */
    public function reorder($lang, $countryCode, Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|integer|exists:departments,id',
                'items.*.sort_number' => 'required|integer|min:0'
            ]);

            Log::info('Departments reorder request', [
                'items' => $request->items,
                'changed_by' => auth()->id()
            ]);

            $items = $request->items;

            // Simple approach: Just update each item with its new sort_number
            // If there's a conflict (two items with same sort_number), swap them
            foreach ($items as $item) {
                $departmentId = $item['id'];
                $newSortNumber = $item['sort_number'];
                
                // Get the department being moved
                $department = \Modules\CategoryManagment\app\Models\Department::find($departmentId);
                if (!$department) {
                    continue;
                }
                
                $oldSortNumber = $department->sort_number;
                
                // If sort number hasn't changed, skip
                if ($oldSortNumber == $newSortNumber) {
                    continue;
                }
                
                // Find if there's another department with the target sort_number
                $conflictingDepartment = \Modules\CategoryManagment\app\Models\Department::where('sort_number', $newSortNumber)
                    ->where('id', '!=', $departmentId)
                    ->first();
                
                if ($conflictingDepartment) {
                    // Swap: Give the conflicting department the old sort number
                    $conflictingDepartment->update(['sort_number' => $oldSortNumber]);
                    Log::info('Swapped department sort numbers', [
                        'department_1' => $departmentId,
                        'department_1_new_sort' => $newSortNumber,
                        'department_2' => $conflictingDepartment->id,
                        'department_2_new_sort' => $oldSortNumber
                    ]);
                }
                
                // Update the dragged department with new sort number
                $department->update(['sort_number' => $newSortNumber]);
            }

            Log::info('Departments reordered successfully');

            return response()->json([
                'success' => true,
                'message' => __('common.reorder_success')
            ]);

        } catch (\Exception $e) {
            Log::error('Error reordering departments: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('common.reorder_error')
            ], 500);
        }
    }
}

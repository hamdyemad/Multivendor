<?php

namespace Modules\CategoryManagment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CategoryManagment\app\Http\Requests\DepartmentRequest;
use Modules\CategoryManagment\app\Services\DepartmentService;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\CategoryManagment\app\Http\Resources\ActivityResource;
use Modules\CategoryManagment\app\Services\ActivityService;
use Modules\CategoryManagment\app\Actions\DepartmentAction;

class DepartmentController extends Controller
{

    public function __construct(
        protected DepartmentService $departmentService, 
        protected LanguageService $languageService,
        protected ActivityService $activityService,
        protected DepartmentAction $departmentAction
    )
    {
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
            'orderColumnIndex' => $request->get('orderColumnIndex', 0),
            'orderDirection' => $request->get('orderDirection', 'desc'),
            'search' => $request->get('search'),
            'active' => $request->get('active'),
            'created_date_from' => $request->get('created_date_from'),
            'created_date_to' => $request->get('created_date_to'),
        ];

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
    public function create()
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
    public function store(DepartmentRequest $request)
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
    public function show(string $id)
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
    public function edit(string $id)
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
    public function update(DepartmentRequest $request, string $id)
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
                    'message' => trans('categorymanagment::department.error_updating_department')
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
    public function destroy(string $id)
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
}

<?php

namespace Modules\CategoryManagment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CategoryManagment\app\Http\Requests\CategoryRequest;
use Modules\CategoryManagment\app\Services\CategoryService;
use Modules\CategoryManagment\app\Services\DepartmentService;
use Modules\CategoryManagment\app\Http\Resources\ActivityResource;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\Http\Resources\DepartmentResource;
use Modules\CategoryManagment\app\Actions\CategoryAction;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected DepartmentService $departmentService,
        protected LanguageService $languageService,
        protected CategoryAction $categoryAction
    )
    {
        $this->middleware('can:categories.index')->only(['index', 'show']);
        $this->middleware('can:categories.create')->only(['create', 'store']);
        $this->middleware('can:categories.edit')->only(['edit', 'update', 'reorder']);
        $this->middleware('can:categories.change-status')->only(['changeStatus']);
        $this->middleware('can:categories.delete')->only(['destroy']);
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable(Request $request)
    {
        try {
            // Get datatable data from action
            $result = $this->categoryAction->getDataTable($request->all());

            $dataPaginated = $result['dataPaginated'];

            return response()->json([
                'draw' => intval($request->input('draw', 1)), // Required for DataTables pagination
                'data' => $result['data'],
                'recordsTotal' => $result['totalRecords'],
                'recordsFiltered' => $result['filteredRecords'],
                'current_page' => $dataPaginated->currentPage(),
                'last_page' => $dataPaginated->lastPage(),
                'per_page' => $dataPaginated->perPage(),
                'total' => $dataPaginated->total(),
                'from' => $dataPaginated->firstItem(),
                'to' => $dataPaginated->lastItem()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading categories: ' . $e->getMessage()
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
            $departmentsCollection = $this->departmentService->getActiveDepartments();
            
            // Format for searchable-tags: only id and name
            $departments = $departmentsCollection->map(function($dept) {
                return [
                    'id' => $dept->id,
                    'name' => $dept->getTranslation('name', app()->getLocale()) ?? 'N/A',
                ];
            })->values()->toArray();
            
            return view('categorymanagment::category.index', compact('languages', 'departments'));
        } catch (\Exception $e) {
            \Log::error('Error loading categories: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('categorymanagment::category.error_loading_categories'));
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create($lang, $countryCode)
    {
        try {
            $departments = $this->departmentService->getActiveDepartments();
            $departments = DepartmentResource::collection($departments)->resolve();
            $languages = $this->languageService->getAll();
            return view('categorymanagment::category.form', compact('languages', 'departments'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.categories.index')
                ->with('error', trans('categorymanagment::category.error_loading_form'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($lang, $countryCode, CategoryRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->categoryService->createCategory($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('categorymanagment::category.category_created'),
                    'redirect' => route('admin.category-management.categories.index')
                ]);
            }

            return redirect()->route('admin.category-management.categories.index')
                ->with('success', trans('categorymanagment::category.category_created'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('categorymanagment::category.error_creating_category')
                ], 500);
            }

            return redirect()->back()
                ->with('error', trans('categorymanagment::category.error_creating_category'))
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
            $category = $this->categoryService->getCategoryById($id);
            return view('categorymanagment::category.view', compact('category', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.categories.index')
                ->with('error', trans('categorymanagment::category.category_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $departments = $this->departmentService->getActiveDepartments();
            $departments = DepartmentResource::collection($departments)->resolve();
            $category = $this->categoryService->getCategoryById($id);
            return view('categorymanagment::category.form', compact('category', 'languages', 'departments'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.categories.index')
                ->with('error', trans('categorymanagment::category.category_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($lang, $countryCode, CategoryRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->categoryService->updateCategory($id, $validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('categorymanagment::category.category_updated'),
                    'redirect' => route('admin.category-management.categories.index')
                ]);
            }

            return redirect()->route('admin.category-management.categories.index')
                ->with('success', trans('categorymanagment::category.category_updated'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating category')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Error updating category'))
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $countryCode, string $id)
    {
        try {
            $this->categoryService->deleteCategory($id);

            return response()->json([
                'success' => true,
                'message' => trans('categorymanagment::category.category_deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('categorymanagment::category.error_deleting_category')
            ], 500);
        }
    }

    /**
     * Change the status of the specified category.
     */
    public function changeStatus($lang, $countryCode, Request $request, string $id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:1,2'
        ]);

        try {
            $category = $this->categoryService->getCategoryById($id);

            // Get the new status from request (1 = active, 2 = inactive)
            $newStatus = $request->input('status') == '1';

            // Check if status is actually changing
            if ($category->active == $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => __('categorymanagment::category.status_already_set')
                ], 422);
            }

            // Update the category status
            $this->categoryService->updateCategory($id, ['active' => $newStatus]);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('categorymanagment::category.status_changed_successfully'),
                    'new_status' => $newStatus,
                    'status_text' => $newStatus ? __('categorymanagment::category.active') : __('categorymanagment::category.inactive'),
                    'status_class' => $newStatus ? 'badge-success' : 'badge-danger'
                ]);
            }

            return redirect()->route('admin.category-management.categories.index')
                ->with('success', __('categorymanagment::category.status_changed_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('categorymanagment::category.error_changing_status') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.category-management.categories.index')
                ->with('error', __('categorymanagment::category.error_changing_status') . ': ' . $e->getMessage());
        }
    }

    /**
     * Change the view status of the specified category.
     */
    public function changeViewStatus($lang, $countryCode, Request $request, string $id)
    {
        $request->validate([
            'view_status' => 'required|in:0,1'
        ]);

        try {
            $category = $this->categoryService->getCategoryById($id);
            $newStatus = (bool) $request->input('view_status');

            $this->categoryService->updateCategory($id, ['view_status' => $newStatus]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('categorymanagment::category.status_changed_successfully'),
                    'new_status' => $newStatus
                ]);
            }

            return redirect()->route('admin.category-management.categories.index')
                ->with('success', __('categorymanagment::category.status_changed_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('categorymanagment::category.error_changing_status') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.category-management.categories.index')
                ->with('error', __('categorymanagment::category.error_changing_status') . ': ' . $e->getMessage());
        }
    }

    /**
     * Reorder categories by updating sort_number
     * Reassigns all sort numbers sequentially to handle cases where all items have sort_number = 0
     */
    public function reorder($lang, $countryCode, Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|integer|exists:categories,id',
                'items.*.sort_number' => 'required|integer|min:0'
            ]);

            \Log::info('Categories reorder request', [
                'items' => $request->items,
                'changed_by' => auth()->id()
            ]);

            \DB::beginTransaction();

            // Get all categories in current order
            $allCategories = \Modules\CategoryManagment\app\Models\Category::orderBy('sort_number', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Get the dragged item info
            $items = $request->items;
            $draggedItem = $items[0]; // We only drag one item at a time
            $draggedId = $draggedItem['id'];
            $targetSortNumber = $draggedItem['sort_number'];

            // Find the dragged category in the collection
            $draggedCategory = $allCategories->firstWhere('id', $draggedId);
            if (!$draggedCategory) {
                throw new \Exception('Category not found');
            }

            // Remove the dragged category from collection
            $allCategories = $allCategories->reject(function($cat) use ($draggedId) {
                return $cat->id == $draggedId;
            })->values();

            // Find the target position in the collection
            $targetIndex = $allCategories->search(function($cat) use ($targetSortNumber) {
                return $cat->sort_number == $targetSortNumber;
            });

            // If target not found, insert at the end
            if ($targetIndex === false) {
                $targetIndex = $allCategories->count();
            }

            // Insert the dragged category at the target position
            $allCategories->splice($targetIndex, 0, [$draggedCategory]);

            // Reassign sort numbers sequentially
            $sortNumber = 1;
            foreach ($allCategories as $category) {
                if ($category->sort_number != $sortNumber) {
                    $category->update(['sort_number' => $sortNumber]);
                }
                $sortNumber++;
            }

            \DB::commit();

            \Log::info('Categories reordered successfully');

            return response()->json([
                'success' => true,
                'message' => __('common.reorder_success')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error reordering categories: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('common.reorder_error')
            ], 500);
        }
    }
}

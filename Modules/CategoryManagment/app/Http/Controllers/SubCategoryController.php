<?php

namespace Modules\CategoryManagment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CategoryManagment\app\Http\Requests\SubCategoryRequest;
use Modules\CategoryManagment\app\Services\SubCategoryService;
use Modules\CategoryManagment\app\Services\CategoryService;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CategoryManagment\app\Actions\SubCategoryAction;

class SubCategoryController extends Controller
{

    public function __construct(
        protected SubCategoryService $subCategoryService,
        protected CategoryService $categoryService,
        protected LanguageService $languageService,
        protected SubCategoryAction $subCategoryAction
    )
    {
        $this->middleware('can:sub-categories.index')->only(['index', 'datatable', 'show']);
        $this->middleware('can:sub-categories.create')->only(['create', 'store']);
        $this->middleware('can:sub-categories.edit')->only(['edit', 'update', 'reorder']);
        $this->middleware('can:sub-categories.delete')->only(['destroy']);
        $this->middleware('can:sub-categories.change-status')->only(['changeStatus']);
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable(Request $request)
    {
        try {
            // Get datatable data from action
            $result = $this->subCategoryAction->getDataTable($request->all());

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
                'error' => 'Error loading sub-categories: ' . $e->getMessage()
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
            $categoriesCollection = $this->categoryService->getActiveCategories();
            
            // Format specifically for searchable-tags: simple array of ['id' => ..., 'name' => ...]
            $categories = [];
            foreach ($categoriesCollection as $cat) {
                $categories[] = [
                    'id' => $cat->id,
                    'name' => $cat->getTranslation('name', app()->getLocale()) ?? 'N/A',
                ];
            }
            
            return view('categorymanagment::subcategory.index', compact('languages', 'categories'));
        } catch (\Exception $e) {
            \Log::error('Error loading sub-categories: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Error loading sub-categories'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($lang, $countryCode)
    {
        try {
            $languages = $this->languageService->getAll();
            $categories = $this->categoryService->getActiveCategories();
            return view('categorymanagment::subcategory.form', compact('languages', 'categories'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.subcategories.index')
                ->with('error', __('Error loading form'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($lang, $countryCode, SubCategoryRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->subCategoryService->createSubCategory($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Sub-category created successfully'),
                    'redirect' => route('admin.category-management.subcategories.index')
                ]);
            }

            return redirect()->route('admin.category-management.subcategories.index')
                ->with('success', __('Sub-category created successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating sub-category')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Error creating sub-category'))
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $countryCode, string $id)
    {
        $languages = $this->languageService->getAll();
        $subCategory = $this->subCategoryService->getSubCategoryById($id);
        try {
            return view('categorymanagment::subcategory.view', compact('subCategory', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.subcategories.index')
                ->with('error', __('Sub-category not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $categories = $this->categoryService->getActiveCategories();
            $subCategory = $this->subCategoryService->getSubCategoryById($id);
            return view('categorymanagment::subcategory.form', compact('subCategory', 'languages', 'categories'));
        } catch (\Exception $e) {
            return redirect()->route('admin.category-management.subcategories.index')
                ->with('error', __('Sub-category not found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($lang, $countryCode, SubCategoryRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->subCategoryService->updateSubCategory($id, $validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Sub-category updated successfully'),
                    'redirect' => route('admin.category-management.subcategories.index')
                ]);
            }

            return redirect()->route('admin.category-management.subcategories.index')
                ->with('success', __('Sub-category updated successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating sub-category')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Error updating sub-category'))
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $countryCode, string $id)
    {
        try {
            $this->subCategoryService->deleteSubCategory($id);

            return response()->json([
                'success' => true,
                'message' => __('Sub-category deleted successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting sub-category')
            ], 500);
        }
    }

    /**
     * Change the status of the specified sub-category.
     */
    public function changeStatus($lang, $countryCode, Request $request, string $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:1,2'
            ]);

            $subCategory = $this->subCategoryService->getSubCategoryById($id);

            if (!$subCategory) {
                return response()->json([
                    'success' => false,
                    'message' => __('categorymanagment::subcategory.subcategory_not_found')
                ], 404);
            }

            // Convert status: 1 = active (true), 2 = inactive (false)
            $newStatus = $request->status == 1;

            // Check if status is already set to the requested value
            if ($subCategory->active == $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => __('categorymanagment::subcategory.status_already_set')
                ]);
            }

            // Update the status
            $subCategory->active = $newStatus;
            $subCategory->save();

            Log::info('SubCategory status changed', [
                'subcategory_id' => $id,
                'new_status' => $newStatus,
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('categorymanagment::subcategory.status_changed_successfully'),
                'new_status' => $newStatus,
                'status_text' => $newStatus ? __('categorymanagment::subcategory.active') : __('categorymanagment::subcategory.inactive')
            ]);

        } catch (\Exception $e) {
            Log::error('Error changing sub-category status: ' . $e->getMessage(), [
                'subcategory_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('categorymanagment::subcategory.error_changing_status')
            ], 500);
        }
    }

    /**
     * Change the view status of the specified sub-category.
     */
    public function changeViewStatus($lang, $countryCode, Request $request, string $id)
    {
        try {
            $request->validate([
                'view_status' => 'required|in:0,1'
            ]);

            $subCategory = $this->subCategoryService->getSubCategoryById($id);

            if (!$subCategory) {
                return response()->json([
                    'success' => false,
                    'message' => __('categorymanagment::subcategory.subcategory_not_found')
                ], 404);
            }

            $newStatus = (bool) $request->view_status;
            $subCategory->view_status = $newStatus;
            $subCategory->save();

            Log::info('SubCategory view_status changed', [
                'subcategory_id' => $id,
                'new_view_status' => $newStatus,
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('categorymanagment::subcategory.status_changed_successfully'),
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Error changing sub-category view_status: ' . $e->getMessage(), [
                'subcategory_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('categorymanagment::subcategory.error_changing_status')
            ], 500);
        }
    }

    /**
     * Reorder subcategories by updating sort_number
    /**
     * Reorder subcategories by updating sort_number
     * Swaps sort numbers instead of renumbering all items
     */
    public function reorder($lang, $countryCode, Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|integer|exists:sub_categories,id',
                'items.*.sort_number' => 'required|integer|min:0'
            ]);

            Log::info('SubCategories reorder request', [
                'items' => $request->items,
                'changed_by' => auth()->id()
            ]);

            $items = $request->items;

            // Simple approach: Just update each item with its new sort_number
            // If there's a conflict (two items with same sort_number), swap them
            foreach ($items as $item) {
                $subCategoryId = $item['id'];
                $newSortNumber = $item['sort_number'];
                
                // Get the subcategory being moved
                $subCategory = \Modules\CategoryManagment\app\Models\SubCategory::find($subCategoryId);
                if (!$subCategory) {
                    continue;
                }
                
                $oldSortNumber = $subCategory->sort_number;
                
                // If sort number hasn't changed, skip
                if ($oldSortNumber == $newSortNumber) {
                    continue;
                }
                
                // Find if there's another subcategory with the target sort_number
                $conflictingSubCategory = \Modules\CategoryManagment\app\Models\SubCategory::where('sort_number', $newSortNumber)
                    ->where('id', '!=', $subCategoryId)
                    ->first();
                
                if ($conflictingSubCategory) {
                    // Swap: Give the conflicting subcategory the old sort number
                    $conflictingSubCategory->update(['sort_number' => $oldSortNumber]);
                    Log::info('Swapped subcategory sort numbers', [
                        'subcategory_1' => $subCategoryId,
                        'subcategory_1_new_sort' => $newSortNumber,
                        'subcategory_2' => $conflictingSubCategory->id,
                        'subcategory_2_new_sort' => $oldSortNumber
                    ]);
                }
                
                // Update the dragged subcategory with new sort number
                $subCategory->update(['sort_number' => $newSortNumber]);
            }

            Log::info('SubCategories reordered successfully');

            return response()->json([
                'success' => true,
                'message' => __('common.reorder_success')
            ]);

        } catch (\Exception $e) {
            Log::error('Error reordering subcategories: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('common.reorder_error')
            ], 500);
        }
    }
}

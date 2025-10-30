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
    protected $subCategoryService;
    protected $categoryService;
    protected $languageService;
    protected $subCategoryAction;

    public function __construct(
        SubCategoryService $subCategoryService, 
        CategoryService $categoryService, 
        LanguageService $languageService,
        SubCategoryAction $subCategoryAction
    )
    {
        $this->subCategoryService = $subCategoryService;
        $this->categoryService = $categoryService;
        $this->languageService = $languageService;
        $this->subCategoryAction = $subCategoryAction;
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
            $categories = $this->categoryService->getActiveCategories();
            return view('categorymanagment::subcategory.index', compact('languages', 'categories'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Error loading sub-categories'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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
    public function store(SubCategoryRequest $request)
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
    public function show(string $id)
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
    public function edit(string $id)
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
    public function update(SubCategoryRequest $request, string $id)
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
    public function destroy(string $id)
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
}

<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\CatalogManagement\app\Models\Tax;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\SubCategory;
use App\Models\Language;
use App\Services\LanguageService;
use Modules\CatalogManagement\app\Http\Resources\BrandResource;
use Modules\CatalogManagement\app\Http\Resources\TaxResource;
use Modules\CatalogManagement\app\Services\BrandService;
use Modules\CatalogManagement\app\Services\TaxService;
use Modules\CategoryManagment\app\Http\Resources\DepartmentResource;
use Modules\CategoryManagment\app\Http\Resources\CategoryResource;
use Modules\CategoryManagment\app\Http\Resources\SubCategoryResource;
use Modules\CategoryManagment\app\Services\DepartmentService;
use Modules\CategoryManagment\app\Services\CategoryService;
use Modules\CategoryManagment\app\Services\SubCategoryService;

class ProductController extends Controller
{

    public function __construct(
        protected LanguageService $languageService,
        protected BrandService $brandService,
        protected DepartmentService $departmentService,
        protected CategoryService $categoryService,
        protected SubCategoryService $subCategoryService,
        protected TaxService $taxService,
        )
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('catalogmanagement::product.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        $brands = $this->brandService->getAllBrands([], 0);
        $brands = BrandResource::collection($brands)->resolve();
        $departments = $this->departmentService->getAllDepartments([], 0);
        $departments = DepartmentResource::collection($departments)->resolve();
        $taxes = $this->taxService->getAllTaxes([], 0);
        $taxes = TaxResource::collection($taxes)->resolve();
        return view('catalogmanagement::product.form', compact('languages', 'brands', 'departments', 'taxes'));
    }
}

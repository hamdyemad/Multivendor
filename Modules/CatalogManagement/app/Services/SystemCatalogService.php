<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CategoryManagment\app\Services\DepartmentService;
use Modules\CategoryManagment\app\Services\CategoryService;
use Modules\CatalogManagement\app\Services\BrandService;
use Modules\CatalogManagement\app\Services\VariantsConfigurationService;
use Modules\AreaSettings\app\Services\RegionService;
use Modules\Vendor\app\Services\VendorService;

class SystemCatalogService
{
    public function __construct(
        protected DepartmentService $departmentService,
        protected CategoryService $categoryService,
        protected BrandService $brandService,
        protected VariantsConfigurationService $variantConfigService,
        protected RegionService $regionService,
        protected VendorService $vendorService
    ) {}

    /**
     * Get all system catalog data
     * - For admin users: includes vendors with logos
     * - For vendor users: excludes vendors section
     */
    public function getAllCatalogData()
    {
        // Get all departments with categories and subcategories for tree view
        $departments = $this->departmentService->getAllDepartments(['active' => 1], 0);
        
        // Load categories with subcategories for each department
        if ($departments) {
            $departments->load(['categories' => function($query) {
                $query->where('active', 1)->with(['translations', 'subs' => function($q) {
                    $q->where('active', 1)->with('translations');
                }]);
            }, 'translations']);
        }

        // Get all categories with subcategories (0 = no pagination, get all)
        $categories = $this->categoryService->getAllCategories(['active' => 1, 'parent_id' => null], 0);

        // Get all variant configurations
        $variants = $this->variantConfigService->getAll();
        
        // Load childrenRecursive relationship for variants
        if ($variants) {
            $variants->load('childrenRecursive.translations', 'childrenRecursive.key');
        }

        // Get all brands (0 = no pagination, get all)
        $brands = $this->brandService->getAllBrands([], 0);

        // Get all regions (0 = no pagination, get all)
        $regions = $this->regionService->getAllRegions(['active' => 1], 0);
        
        // Load city.country relationship for regions
        if ($regions) {
            $regions->load('city.country');
        }

        $data = compact(
            'departments',
            'categories',
            'variants',
            'brands',
            'regions'
        );

        // Add vendors for admin users only
        if (isAdmin()) {
            $vendors = $this->vendorService->getAllVendors(['active' => 1], 0);
            
            // Load logo attachment relationship
            if ($vendors) {
                $vendors->load('logo');
            }
            
            $data['vendors'] = $vendors;
        }

        return $data;
    }
}

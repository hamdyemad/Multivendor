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
    protected int $perPage = 20;

    public function __construct(
        protected DepartmentService $departmentService,
        protected CategoryService $categoryService,
        protected BrandService $brandService,
        protected VariantsConfigurationService $variantService,
        protected RegionService $regionService,
        protected VendorService $vendorService
    ) {}

    /**
     * Get paginated departments
     */
    public function getDepartments(array $filters = []): array
    {
        $perPage = $filters['per_page'] ?? $this->perPage;
        $searchFilters = ['active' => 1];
        
        if (!empty($filters['search'])) {
            $searchFilters['search'] = $filters['search'];
        }

        $paginated = $this->departmentService->getAllDepartments($searchFilters, $perPage);
        
        return [
            'data' => $paginated->map(fn($d) => [
                'id' => $d->id,
                'name_en' => $d->getTranslation('name', 'en'),
                'name_ar' => $d->getTranslation('name', 'ar'),
            ]),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ];
    }

    /**
     * Get paginated categories
     */
    public function getCategories(array $filters = []): array
    {
        $perPage = $filters['per_page'] ?? $this->perPage;
        $searchFilters = ['active' => 1];
        
        if (!empty($filters['search'])) {
            $searchFilters['search'] = $filters['search'];
        }

        $paginated = $this->categoryService->getAllCategories($searchFilters, $perPage);
        
        // Load relationships
        $paginated->load(['department.translations', 'subs' => fn($q) => $q->where('active', 1)->with('translations')]);
        
        return [
            'data' => $paginated->map(fn($c) => [
                'id' => $c->id,
                'name_en' => $c->getTranslation('name', 'en'),
                'name_ar' => $c->getTranslation('name', 'ar'),
                'department_id' => $c->department?->id,
                'department_name' => $c->department?->getTranslation('name', 'en'),
                'subs' => $c->subs->map(fn($s) => [
                    'id' => $s->id,
                    'name_en' => $s->getTranslation('name', 'en'),
                    'name_ar' => $s->getTranslation('name', 'ar'),
                ]),
            ]),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ];
    }

    /**
     * Get paginated variants
     */
    public function getVariants(array $filters = []): array
    {
        $perPage = $filters['per_page'] ?? $this->perPage;
        $searchFilters = [];
        
        if (!empty($filters['search'])) {
            $searchFilters['search'] = $filters['search'];
        }

        $paginated = $this->variantService->getAllPaginated($searchFilters, $perPage);
        
        return [
            'data' => $paginated->map(fn($v) => [
                'id' => $v->id,
                'name_en' => $v->getTranslation('name', 'en'),
                'name_ar' => $v->getTranslation('name', 'ar'),
                'key_name' => $v->key?->getTranslation('name', 'en'),
                'color' => $v->color,
            ]),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ];
    }

    /**
     * Get paginated brands
     */
    public function getBrands(array $filters = []): array
    {
        $perPage = $filters['per_page'] ?? $this->perPage;
        $searchFilters = [];
        
        if (!empty($filters['search'])) {
            $searchFilters['search'] = $filters['search'];
        }

        $paginated = $this->brandService->getAllBrands($searchFilters, $perPage);
        
        return [
            'data' => $paginated->map(fn($b) => [
                'id' => $b->id,
                'name_en' => $b->getTranslation('name', 'en'),
                'name_ar' => $b->getTranslation('name', 'ar'),
            ]),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ];
    }

    /**
     * Get paginated regions
     */
    public function getRegions(array $filters = []): array
    {
        $perPage = $filters['per_page'] ?? $this->perPage;
        $searchFilters = ['active' => 1];
        
        if (!empty($filters['search'])) {
            $searchFilters['search'] = $filters['search'];
        }

        $paginated = $this->regionService->getAllRegions($searchFilters, $perPage);
        
        // Load relationships
        $paginated->load('city.country');
        
        return [
            'data' => $paginated->map(fn($r) => [
                'id' => $r->id,
                'name_en' => $r->getTranslation('name', 'en'),
                'name_ar' => $r->getTranslation('name', 'ar'),
                'city_id' => $r->city?->id,
                'city_name' => $r->city?->name,
                'country_id' => $r->city?->country?->id,
                'country_name' => $r->city?->country?->name,
            ]),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ];
    }

    /**
     * Get paginated vendors (admin only)
     */
    public function getVendors(array $filters = []): array
    {
        $perPage = $filters['per_page'] ?? $this->perPage;
        $searchFilters = ['active' => 1];
        
        if (!empty($filters['search'])) {
            $searchFilters['search'] = $filters['search'];
        }

        $paginated = $this->vendorService->getAllVendors($searchFilters, $perPage);
        
        // Load logo relationship
        $paginated->load('logo');
        
        return [
            'data' => $paginated->map(fn($v) => [
                'id' => $v->id,
                'name_en' => $v->getTranslation('name', 'en'),
                'name_ar' => $v->getTranslation('name', 'ar'),
                'email' => $v->email,
                'phone' => $v->phone,
                'logo' => $v->logo?->path ? asset($v->logo->path) : null,
            ]),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ];
    }
}

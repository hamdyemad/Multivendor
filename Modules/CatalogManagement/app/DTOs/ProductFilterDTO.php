<?php

namespace Modules\CatalogManagement\app\DTOs;

use App\DTOs\FilterDTO;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\Region;
use Modules\AreaSettings\app\Models\SubRegion;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\SubCategory;
use Modules\Vendor\app\Models\Vendor;

class ProductFilterDTO extends FilterDTO
{
    private array $errors = [];

    public function __construct(
        public ?string $search = null,
        public ?string $brand_id = null,
        public ?string $department_id = null,
        public ?string $category_id = null,
        public ?string $sub_category_id = null,
        public ?string $vendor_id = null,
        public ?bool $has_discount = null,
        public ?float $min_price = null,
        public ?float $max_price = null,
        public ?bool $featured = null,
        public ?string $sort_by = null,
        public ?string $sort_type = null,
        public ?string $country_id = null,
        public ?string $city_id = null,
        public ?string $region_id = null,
        public ?string $subregion_id = null,
        public ?int $per_page = null,
        public ?string $paginated = null,
        public ?int $limit = null,
        public ?int $rate = null,
    ) {}

    /**
     * Create DTO from HTTP request
     */
    public static function fromRequest($request): self
    {
        return new self(
            search: $request->input('search'),
            brand_id: $request->input('brand_id'),
            department_id: $request->input('department_id'),
            category_id: $request->input('category_id'),
            sub_category_id: $request->input('sub_category_id'),
            vendor_id: $request->input('vendor_id'),
            has_discount: $request->boolean('has_discount', null),
            min_price: $request->input('min_price') ? (float) $request->input('min_price') : null,
            max_price: $request->input('max_price') ? (float) $request->input('max_price') : null,
            featured: $request->boolean('featured', null),
            sort_by: $request->input('sort_by'),
            sort_type: $request->input('sort_type'),
            country_id: $request->input('country_id', null),
            city_id: $request->input('city_id', null),
            region_id: $request->input('region_id', null),
            subregion_id: $request->input('subregion_id', null),
            per_page: $request->integer('per_page', 15),
            paginated: $request->input('paginated', null),
            limit: $request->integer('limit', null),
            rate: $request->integer('rate', null)
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'brand_id' => $this->brand_id,
            'department_id' => $this->department_id,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'vendor_id' => $this->vendor_id,
            'has_discount' => $this->has_discount,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'featured' => $this->featured,
            'sort_by' => $this->sort_by,
            'sort_type' => $this->sort_type,
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'region_id' => $this->region_id,
            'subregion_id' => $this->subregion_id,
            'rate' => $this->rate,
            'limit' => $this->limit,
            'paginated' => $this->paginated,
        ], fn($value) => $value !== null);
    }

    public function validate(): bool
    {
        $this->errors = [];

        if ($this->min_price !== null && $this->min_price < 0) {
            $this->errors['min_price'][] = __('validation.min_price_positive');
        }

        if ($this->max_price !== null && $this->max_price < 0) {
            $this->errors['max_price'][] = __('validation.max_price_positive');
        }

        if ($this->min_price !== null && $this->max_price !== null && $this->min_price >= $this->max_price) {
            $this->errors['min_price'][] = __('validation.min_price_max_price');
        }

        if ($this->sort_by && !in_array($this->sort_by, ['created_at', 'name', 'price', 'rating', 'views', 'sales'])) {
            $this->errors['sort_by'][] = __('validation.sort_by_invalid');
        }

        if ($this->sort_type && !in_array($this->sort_type, ['asc', 'desc'])) {
            $this->errors['sort_type'][] = __('validation.sort_type_invalid');
        }

        if ($this->has_discount !== null && !in_array($this->has_discount, [true, false])) {
            $this->errors['has_discount'][] = __('validation.has_discount_invalid');
        }

        if ($this->rate && !in_array($this->rate, [1, 2, 3, 4, 5])) {
            $this->errors['rate'][] = __('validation.rate_invalid');
        }

        // Skip database validation for better performance - invalid IDs will simply return no results
        // The filter queries will handle non-existent IDs gracefully

        return count($this->errors) === 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function countryExists(string $countryId): bool
    {
        return Country::where('id', $countryId)->orWhere('slug', $countryId)->exists();
    }

    private function cityExists(string $cityId): bool
    {
        return City::where('id', $cityId)->orWhere('slug', $cityId)->exists();
    }

    private function regionExists(string $regionId): bool
    {
        return Region::where('id', $regionId)->orWhere('slug', $regionId)->exists();
    }

    private function subregionExists(string $subregionId): bool
    {
        return SubRegion::where('id', $subregionId)->orWhere('slug', $subregionId)->exists();
    }

    private function departmentExists(string $departmentId): bool
    {
        return Department::where('id', $departmentId)->orWhere('slug', $departmentId)->exists();
    }

    private function categoryExists(string $categoryId): bool
    {
        return Category::where('id', $categoryId)->orWhere('slug', $categoryId)->exists();
    }

    private function subCategoryExists(string $subCategoryId): bool
    {
        return SubCategory::where('id', $subCategoryId)->orWhere('slug', $subCategoryId)->exists();
    }

    private function brandExists(string $brandId): bool
    {
        return Brand::where('id', $brandId)->orWhere('slug', $brandId)->exists();
    }

    private function vendorExists(string $vendorId): bool
    {
        return Vendor::where('id', $vendorId)->orWhere('slug', $vendorId)->exists();
    }
}

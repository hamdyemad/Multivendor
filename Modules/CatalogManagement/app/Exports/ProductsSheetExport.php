<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Illuminate\Support\Facades\Auth;

/**
 * Sheet: products
 * Exports Product and VendorProduct data
 */
class ProductsSheetExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected bool $isAdmin;
    protected array $filters;

    public function __construct(bool $isAdmin = false, array $filters = [])
    {
        $this->isAdmin = $isAdmin;
        $this->filters = $filters;
    }

    public function query()
    {
        $query = VendorProduct::with([
            'product.brand',
            'product.department',
            'product.category',
            'product.subCategory',
            'product.translations',
            'vendor'
        ]);

        // Apply filters based on user role
        if (!$this->isAdmin) {
            $vendorId = Auth::user()->vendor?->id;
            if ($vendorId) {
                $query->where('vendor_id', $vendorId);
            }
        }

        // Apply additional filters from request
        if (!empty($this->filters['vendor_id'])) {
            $query->where('vendor_id', $this->filters['vendor_id']);
        }

        if (!empty($this->filters['department_id'])) {
            $query->whereHas('product', function($q) {
                $q->where('department_id', $this->filters['department_id']);
            });
        }

        if (!empty($this->filters['category_id'])) {
            $query->whereHas('product', function($q) {
                $q->where('category_id', $this->filters['category_id']);
            });
        }

        if (!empty($this->filters['brand_id'])) {
            $query->whereHas('product', function($q) {
                $q->where('brand_id', $this->filters['brand_id']);
            });
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhereHas('product.translations', function($tq) use ($search) {
                      $tq->where('lang_value', 'like', "%{$search}%");
                  });
            });
        }

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('id');
    }

    public function headings(): array
    {
        $headings = [
            'id',
            'sku',
        ];

        if ($this->isAdmin) {
            $headings[] = 'vendor_id';
        }

        return array_merge($headings, [
            'title_en',
            'title_ar',
            'description_en',
            'description_ar',
            'summary_en',
            'summary_ar',
            'features_en',
            'features_ar',
            'instructions_en',
            'instructions_ar',
            'extra_description_en',
            'extra_description_ar',
            'material_en',
            'material_ar',
            'meta_title_en',
            'meta_title_ar',
            'meta_description_en',
            'meta_description_ar',
            'meta_keywords_en',
            'meta_keywords_ar',
            'department',
            'main_category',
            'sub_category',
            'brand',
            'have_varient',
            'status',
            'featured_product',
            'max_per_order',
        ]);
    }

    public function map($vendorProduct): array
    {
        $product = $vendorProduct->product;
        
        $row = [
            $vendorProduct->id,
            $vendorProduct->sku,
        ];

        if ($this->isAdmin) {
            $row[] = $vendorProduct->vendor_id;
        }

        $translations = $product->translations->groupBy('lang_key');
        
        return array_merge($row, [
            $this->getTranslation($translations, 'title', 'en'),
            $this->getTranslation($translations, 'title', 'ar'),
            $this->getTranslation($translations, 'details', 'en'),
            $this->getTranslation($translations, 'details', 'ar'),
            $this->getTranslation($translations, 'summary', 'en'),
            $this->getTranslation($translations, 'summary', 'ar'),
            $this->getTranslation($translations, 'features', 'en'),
            $this->getTranslation($translations, 'features', 'ar'),
            $this->getTranslation($translations, 'instructions', 'en'),
            $this->getTranslation($translations, 'instructions', 'ar'),
            $this->getTranslation($translations, 'extra_description', 'en'),
            $this->getTranslation($translations, 'extra_description', 'ar'),
            $this->getTranslation($translations, 'material', 'en'),
            $this->getTranslation($translations, 'material', 'ar'),
            $this->getTranslation($translations, 'meta_title', 'en'),
            $this->getTranslation($translations, 'meta_title', 'ar'),
            $this->getTranslation($translations, 'meta_description', 'en'),
            $this->getTranslation($translations, 'meta_description', 'ar'),
            $this->getTranslation($translations, 'meta_keywords', 'en'),
            $this->getTranslation($translations, 'meta_keywords', 'ar'),
            $product->department_id ?? '',
            $product->category_id ?? '',
            $product->sub_category_id ?? '',
            $product->brand_id ?? '',
            $product->configuration_type === 'variants' ? 'yes' : 'no',
            $vendorProduct->is_active ? 'yes' : 'no',
            $vendorProduct->is_featured ? 'yes' : 'no',
            $vendorProduct->max_per_order ?? 1,
        ]);
    }

    protected function getTranslation($translations, $key, $lang): string
    {
        if (!isset($translations[$key])) {
            return '';
        }

        $langId = \App\Models\Language::where('code', $lang)->first()?->id;
        if (!$langId) {
            return '';
        }

        $translation = $translations[$key]->firstWhere('lang_id', $langId);
        return $translation ? $translation->lang_value : '';
    }

    public function title(): string
    {
        return 'products';
    }
}

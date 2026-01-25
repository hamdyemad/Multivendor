<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Sheet: products
 * Exports Product and VendorProduct data
 */
class ProductsSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected bool $isAdmin;
    protected $vendorProducts;
    protected array $productIdMapping;

    public function __construct(bool $isAdmin = false, $vendorProducts = null, array $productIdMapping = [])
    {
        $this->isAdmin = $isAdmin;
        $this->vendorProducts = $vendorProducts;
        $this->productIdMapping = $productIdMapping;
    }

    public function collection()
    {
        return $this->vendorProducts;
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
            'tags_en',
            'tags_ar',
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
        
        // Use the mapping to get incremental index
        $incrementalId = $this->productIdMapping[$vendorProduct->id] ?? $vendorProduct->id;
        
        $row = [
            $incrementalId,
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
            $this->getTranslation($translations, 'tags', 'en'),
            $this->getTranslation($translations, 'tags', 'ar'),
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

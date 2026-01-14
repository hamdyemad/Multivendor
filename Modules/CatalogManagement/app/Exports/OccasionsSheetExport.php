<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Modules\CatalogManagement\app\Models\Occasion;

/**
 * Sheet: occasions
 * Exports occasions (admin only)
 */
class OccasionsSheetExport implements FromQuery, WithHeadings, WithMapping, WithTitle
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
        return Occasion::with('translations')->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'id',
            'name_en',
            'name_ar',
            'description_en',
            'description_ar',
            'start_date',
            'end_date',
            'is_active',
        ];
    }

    public function map($occasion): array
    {
        $translations = $occasion->translations->groupBy('lang_key');
        
        return [
            $occasion->id,
            $this->getTranslation($translations, 'name', 'en'),
            $this->getTranslation($translations, 'name', 'ar'),
            $this->getTranslation($translations, 'description', 'en'),
            $this->getTranslation($translations, 'description', 'ar'),
            $occasion->start_date ? $occasion->start_date->format('Y-m-d') : '',
            $occasion->end_date ? $occasion->end_date->format('Y-m-d') : '',
            $occasion->is_active ? 'yes' : 'no',
        ];
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
        return 'occasions';
    }
}

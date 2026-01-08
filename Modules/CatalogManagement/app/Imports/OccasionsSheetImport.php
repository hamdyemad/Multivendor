<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\Occasion;

/**
 * Sheet: occasions
 * Creates or updates Occasion entries (admin only)
 */
class OccasionsSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function __construct(
        protected array &$occasionMap,
        protected array &$importErrors = [],
        protected bool $isAdmin = false
    ) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $excelId = (int)($row['id'] ?? 0);
            $nameEn = trim((string)($row['name_en'] ?? ''));
            $nameAr = trim((string)($row['name_ar'] ?? ''));

            $validator = Validator::make($row->toArray(), [
                'id' => 'required|integer|min:1',
                'name_en' => 'nullable|string|max:255',
                'name_ar' => 'nullable|string|max:255',
                'title_en' => 'nullable|string|max:255',
                'title_ar' => 'nullable|string|max:255',
                'sub_title_en' => 'nullable|string|max:255',
                'sub_title_ar' => 'nullable|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'image' => 'nullable|string',
                'is_active' => 'nullable|in:0,1,true,false,yes,no',
            ], [
                'id.required' => __('validation.required', ['attribute' => 'id']),
                'start_date.required' => __('validation.required', ['attribute' => 'start_date']),
                'start_date.date' => __('validation.date', ['attribute' => 'start_date']),
                'end_date.required' => __('validation.required', ['attribute' => 'end_date']),
                'end_date.date' => __('validation.date', ['attribute' => 'end_date']),
                'end_date.after_or_equal' => __('validation.after_or_equal', ['attribute' => 'end_date', 'date' => 'start_date']),
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'occasions',
                    'row' => $index + 2,
                    'id' => $excelId,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($excelId <= 0 || ($nameEn === '' && $nameAr === '')) {
                $this->importErrors[] = [
                    'sheet' => 'occasions',
                    'row' => $index + 2,
                    'id' => $excelId,
                    'errors' => [__('catalogmanagement::product.invalid_occasion_id_or_names')]
                ];
                continue;
            }

            $isActive = $this->normalizeYesNo($row['is_active'] ?? '1') === 'yes';

            // Generate slug from English name
            $slug = !empty($nameEn) ? Str::slug($nameEn) : Str::uuid();
            
            // Check if occasion with this slug already exists
            $existingOccasion = Occasion::where('slug', $slug)->first();
            
            if ($existingOccasion) {
                // Update existing occasion
                $existingOccasion->update([
                    'start_date' => $row['start_date'],
                    'end_date' => $row['end_date'],
                    'image' => $row['image'] ?? $existingOccasion->image,
                    'is_active' => $isActive,
                ]);
                
                // Update translations
                $existingOccasion->translations()->delete();
                $this->storeTranslations($existingOccasion, $row);
                
                $occasion = $existingOccasion;
            } else {
                // Create new occasion - slug will be auto-generated in model boot if not provided
                $occasion = Occasion::create([
                    'slug' => $slug,
                    'start_date' => $row['start_date'],
                    'end_date' => $row['end_date'],
                    'image' => $row['image'] ?? null,
                    'is_active' => $isActive,
                ]);

                // Store translations
                $this->storeTranslations($occasion, $row);
            }

            $this->occasionMap[$excelId] = (int)$occasion->id;
        }
    }

    private function storeTranslations(Occasion $occasion, $row): void
    {
        $languages = \App\Models\Language::all();
        
        foreach ($languages as $language) {
            $langCode = $language->code;
            
            $fields = [
                'name' => $row["title_{$langCode}"] ?? null,
                'title' => $row["title_{$langCode}"] ?? null,
                'sub_title' => $row["sub_title_{$langCode}"] ?? null,
                'meta_title' => $row["meta_title_{$langCode}"] ?? null,
                'meta_description' => $row["meta_description_{$langCode}"] ?? null,
                'meta_keywords' => $row["meta_keywords_{$langCode}"] ?? null,
            ];

            foreach ($fields as $key => $value) {
                if (!empty($value)) {
                    $occasion->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => $key,
                        'lang_value' => $value,
                    ]);
                }
            }
        }
    }

    private function normalizeYesNo($value): string
    {
        $v = strtolower(trim((string)$value));
        return in_array($v, ['1', 'true', 'yes', 'y'], true) ? 'yes' : 'no';
    }
}

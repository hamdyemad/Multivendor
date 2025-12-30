<?php

namespace Modules\CatalogManagement\app\Repositories;

use Modules\CatalogManagement\app\Interfaces\OccasionRepositoryInterface;
use Modules\CatalogManagement\app\Models\Occasion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OccasionRepository implements OccasionRepositoryInterface
{
    /**
     * Get all occasions with optional filters
     */


    public function getAllOccasions(array $filters = [], $perPage = 10)
    {
        $query = Occasion::with([
            'translations', 
            'occasionProducts.vendorProductVariant.vendorProduct.product'
        ])
            ->filter($filters)
            ->latest();

        return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get occasion by ID
     */
    public function getOccasionById($id)
    {
        $query = Occasion::with([
            'translations',
            'occasionProducts.vendorProductVariant.vendorProduct.product.mainImage',
            'occasionProducts.vendorProductVariant.vendorProduct.product.translations',
            'occasionProducts.vendorProductVariant.vendorProduct.vendor.logo',
            'occasionProducts.vendorProductVariant.variantConfiguration.key'
        ])
        ->where(function($q) use ($id) {
            $q->where('id', $id)->orWhere('slug', $id);
        });
        
        return $query->firstOrFail();
    }

    /**
     * Create new occasion
     */
    public function createOccasion(array $data)
    {
        return DB::transaction(function () use ($data) {
            $occasion = Occasion::create([
                'slug' => uniqid(),
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Handle image upload via attachments
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $image = $data['image'];
                $filePath = $image->store("occasions", 'public');

                $occasion->attachments()->create([
                    'type' => 'image',
                    'path' => $filePath
                ]);
            }

            // Store translations
            $this->storeTranslations($occasion, $data);

            // Store occasion products (variants)
            $this->storeOccasionProducts($occasion, $data);

            return $occasion;
        });
    }

    /**
     * Update occasion
     */
    public function updateOccasion($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $occasion = $this->getOccasionById($id);

            $updateData = [
                'start_date' => $data['start_date'] ?? $occasion->start_date,
                'end_date' => $data['end_date'] ?? $occasion->end_date,
                'is_active' => $data['is_active'] ?? $occasion->is_active,
            ];

            $occasion->update($updateData);

            // Handle image upload via attachments - only if a new file is provided
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                // Delete old image attachment if exists
                $occasion->attachments()->where('type', 'image')->delete();

                // Store new image
                $image = $data['image'];
                $filePath = $image->store("occasions", 'public');

                $occasion->attachments()->create([
                    'path' => $filePath,
                    'type' => 'image'
                ]);
            }

            // Store translations (this will handle updates)
            $this->storeTranslations($occasion, $data);

            // Store occasion products (variants)
            $this->storeOccasionProducts($occasion, $data);

            return $occasion->fresh();
        });
    }

    /**
     * Delete occasion
     */
    public function deleteOccasion($id)
    {
        $occasion = $this->getOccasionById($id);
        return $occasion->delete();
    }

    /**
     * Get active occasions
     */
    public function getActiveOccasions()
    {
        return Occasion::active()->with(['translations'])->get();
    }

    /**
     * Toggle occasion status
     */
    public function toggleOccasionStatus($id)
    {
        $occasion = $this->getOccasionById($id);
        $occasion->update(['is_active' => !$occasion->is_active]);
        return $occasion->fresh();
    }

    /**
     * Count total occasions
     */
    public function count(): int
    {
        return Occasion::count();
    }

    /**
     * Store translations for occasion
     */
    protected function storeTranslations(Occasion $occasion, array $data): void
    {
        if (!isset($data['translations']) || !is_array($data['translations'])) {
            return;
        }

        // Get all languages
        $languages = \App\Models\Language::all();
        $slugUpdated = false;

        foreach ($languages as $language) {
            $translationData = $data['translations'][$language->id] ?? [];

            if (!empty($translationData['name'])) {
                // Generate slug from name only once (from first language with name)
                if (!$slugUpdated) {
                    $baseSlug = Str::slug($translationData['name']);
                    
                    // If slug is empty (e.g., Arabic only name), use uniqid
                    if (empty($baseSlug)) {
                        $baseSlug = uniqid('occasion-');
                    }
                    
                    $slug = $baseSlug;
                    $counter = 1;
                    
                    // Keep checking until we find a unique slug (including soft-deleted records)
                    while (Occasion::withTrashed()
                        ->withoutGlobalScopes()
                        ->where('slug', $slug)
                        ->where('id', '!=', $occasion->id)
                        ->exists()) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }
                    
                    $occasion->update(['slug' => $slug]);
                    $slugUpdated = true;
                }

                // Store translation fields - including empty values
                $translationFields = ['name', 'title', 'sub_title', 'seo_title', 'seo_description', 'seo_keywords'];

                foreach ($translationFields as $field) {
                    // Store the field if it exists in the data (even if empty)
                    if (isset($translationData[$field])) {
                        $value = $translationData[$field];
                        // Only store if value is not null and not just whitespace
                        if ($value !== null && trim((string)$value) !== '') {
                            // Use updateOrCreate to preserve translations from other languages
                            $occasion->translations()->updateOrCreate(
                                [
                                    'lang_id' => $language->id,
                                    'lang_key' => $field,
                                ],
                                [
                                    'lang_value' => $value,
                                ]
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Store occasion products (variants with special prices)
     */
    protected function storeOccasionProducts(Occasion $occasion, array $data): void
    {
        if (!isset($data['variants']) || !is_array($data['variants'])) {
            return;
        }

        // Delete existing occasion products
        $occasion->occasionProducts()->delete();

        // Store new occasion products
        $position = 0;
        foreach ($data['variants'] as $variant) {
            if (!empty($variant['vendor_product_variant_id'])) {
                $occasion->occasionProducts()->create([
                    'vendor_product_variant_id' => $variant['vendor_product_variant_id'],
                    'special_price' => $variant['special_price'] ?? null,
                    'position' => $position++,
                ]);
            }
        }
    }
}

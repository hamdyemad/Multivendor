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
        $query = Occasion::with(['translations', 'vendor', 'occasionProducts.vendorProductVariant.vendorProduct.product'])
            ->filter($filters);

        return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get occasion by ID
     */
    public function getOccasionById($id)
    {
        $query = Occasion::with([
            'translations',
            'vendor',
            'occasionProducts'
        ])
        ->where(function($q) use ($id) {
            $q->where('id', $id)->orWhere('slug', $id);
        });
        
        // If current user is a vendor, only allow access to occasions in their vendor
        if (auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $query->where('vendor_id', $vendor->id);
            }
        }
        
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
                'vendor_id' => $data['vendor_id'],
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Handle image upload via attachments
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $image = $data['image'];
                $filePath = $image->store("{$data['vendor_id']}/occasions", 'public');

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
                'vendor_id' => $data['vendor_id'] ?? $occasion->vendor_id,
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
                $filePath = $image->store("{$occasion->vendor_id}/occasions", 'public');

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
        
        // If current user is a vendor, verify they have access to this occasion
        if (auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor && $occasion->vendor_id !== $vendor->id) {
                abort(404);
            }
        }
        
        return $occasion->delete();
    }

    /**
     * Get active occasions
     */
    public function getActiveOccasions()
    {
        return Occasion::active()->with(['translations', 'vendor'])->get();
    }

    /**
     * Toggle occasion status
     */
    public function toggleOccasionStatus($id)
    {
        $occasion = $this->getOccasionById($id);
        
        // If current user is a vendor, verify they have access to this occasion
        if (auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor && $occasion->vendor_id !== $vendor->id) {
                abort(404);
            }
        }
        
        $occasion->update(['is_active' => !$occasion->is_active]);
        return $occasion->fresh();
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

        foreach ($languages as $language) {
            $translationData = $data['translations'][$language->id] ?? [];

            if (!empty($translationData['name'])) {
                // Generate slug from name
                $model = Occasion::where('slug', Str::slug($translationData['name']))
                ->withoutCountryFilter()
                ->where('id', '!=', $occasion->id)->first();

                if($model) {
                    $newSlug = $model->slug . '-' . rand(1, 1000);
                    $occasion->update([
                        'slug' => $newSlug
                    ]);
                } else {
                    $occasion->update([
                        'slug' => Str::slug($translationData['name'])
                    ]);
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

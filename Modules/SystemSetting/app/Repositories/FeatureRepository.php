<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Models\Feature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FeatureRepository
{
    /**
     * Get all features
     */
    public function all()
    {
        return Feature::with('translations', 'attachments')->get();
    }

    /**
     * Find feature by ID
     */
    public function find($id)
    {
        return Feature::with('translations', 'attachments')->findOrFail($id);
    }

    /**
     * Create a new feature
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $feature = Feature::create([
                'active' => $data['active'] ?? 1,
            ]);
            $feature->translations()->forceDelete();

            // Save translations
            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (!empty($translation['title'])) {
                        $feature->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'title',
                            'lang_value' => $translation['title'],
                        ]);
                    }
                    if (!empty($translation['subtitle'])) {
                        $feature->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'subtitle',
                            'lang_value' => $translation['subtitle'],
                        ]);
                    }
                }
            }

            // Save logo
            if (isset($data['logo']) && $data['logo']) {
                $logoPath = $data['logo']->store('features', 'public');
                $feature->attachments()->create([
                    'path' => $logoPath,
                    'type' => 'logo',
                ]);
            }

            return $feature;
        });
    }

    /**
     * Update feature
     */
    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $feature = Feature::findOrFail($id);

            $feature->update([
                'active' => $data['active'] ?? 0,
            ]);
            $feature->translations()->forceDelete();

            // Update translations
            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (!empty($translation['title'])) {
                        $feature->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'title',
                            'lang_value' => $translation['title'],
                        ]);
                    }
                    if (!empty($translation['subtitle'])) {
                        $feature->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'subtitle',
                            'lang_value' => $translation['subtitle'],
                        ]);
                    }
                }
            }

            // Update logo if provided
            if (isset($data['logo']) && $data['logo']) {
                // Delete old logo
                $oldLogo = $feature->attachments()->where('type', 'logo')->first();
                if ($oldLogo) {
                    Storage::disk('public')->delete($oldLogo->path);
                    $oldLogo->delete();
                }

                // Save new logo
                $logoPath = $data['logo']->store('features', 'public');
                $feature->attachments()->create([
                    'path' => $logoPath,
                    'type' => 'logo',
                ]);
            }

            // Touch the model to trigger GlobalModelObserver
            // This ensures activity log is recorded even when only translations change
            $feature->touch();

            return $feature;
        });
    }

    /**
     * Delete feature
     */
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $feature = Feature::findOrFail($id);

            // Delete attachments
            foreach ($feature->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }

            // Delete translations
            $feature->translations()->forceDelete();

            // Delete feature
            return $feature->delete();
        });
    }

    /**
     * Filter features
     */
    public function filter($filters)
    {
        return Feature::filter($filters)->with('translations', 'attachments')->get();
    }
}

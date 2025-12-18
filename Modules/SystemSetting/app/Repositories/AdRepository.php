<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Interfaces\AdRepositoryInterface;
use Modules\SystemSetting\app\Models\Ad;
use App\Models\Attachment;
use Illuminate\Support\Facades\DB;

class AdRepository implements AdRepositoryInterface
{
    public function all()
    {
        return Ad::with('translations', 'attachments')->get();
    }

    public function find($id)
    {
        return Ad::with('translations', 'attachments')->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $ad = Ad::create([
                'position' => $data['position'],
                'link' => $data['link'] ?? null,
                'active' => $data['active'] ?? 1,
            ]);

            // Handle translations
            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    $ad->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'title',
                        'lang_value' => $translation['title'],
                    ]);

                    if (!empty($translation['subtitle'])) {
                        $ad->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'subtitle',
                            'lang_value' => $translation['subtitle'],
                        ]);
                    }
                }
            }

            // Handle image upload
            if (isset($data['image'])) {
                $path = $data['image']->store('ads', 'public');
                Attachment::create([
                    'attachable_type' => Ad::class,
                    'attachable_id' => $ad->id,
                    'path' => $path,
                    'type' => 'image',
                ]);
            }

            return $ad;
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $ad = Ad::findOrFail($id);

            $ad->update([
                'position' => $data['position'],
                'link' => $data['link'] ?? null,
                'active' => $data['active'] ?? 1,
            ]);

            // Update translations
            if (isset($data['translations'])) {
                $ad->translations()->delete();

                foreach ($data['translations'] as $langId => $translation) {
                    $ad->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'title',
                        'lang_value' => $translation['title'],
                    ]);

                    if (!empty($translation['subtitle'])) {
                        $ad->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'subtitle',
                            'lang_value' => $translation['subtitle'],
                        ]);
                    }
                }
            }

            // Handle image removal
            if (isset($data['remove_image']) && $data['remove_image']) {
                $ad->attachments()->where('type', 'image')->delete();
            }

            // Handle new image upload
            if (isset($data['image'])) {
                // Remove old image
                $ad->attachments()->where('type', 'image')->delete();

                // Upload new image
                $path = $data['image']->store('ads', 'public');
                Attachment::create([
                    'attachable_type' => Ad::class,
                    'attachable_id' => $ad->id,
                    'path' => $path,
                    'type' => 'image',
                ]);
            }

            return $ad->fresh();
        });
    }

    public function delete($id)
    {
        $ad = Ad::findOrFail($id);
        return $ad->delete();
    }

    public function filter(array $filters)
    {
        return Ad::filter($filters)
            ->with('translations', 'attachments')
            ->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }
    public function toggleStatus($id, $status)
    {
        $ad = Ad::findOrFail($id);
        return $ad->update(['active' => $status]);
    }
}

<?php

namespace Modules\Order\app\Repositories;

use Modules\Order\app\Interfaces\OrderStageRepositoryInterface;
use Modules\Order\app\Models\OrderStage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderStageRepository implements OrderStageRepositoryInterface
{
    /**
     * Get all order stages with optional filters
     */
    public function getOrderStagesQuery(array $filters = [], $orderBy = null, $orderDirection = 'desc')
    {
        $query = OrderStage::with(['translations', 'country'])
        ->filter($filters)
        ->where('is_system', 1)
        ->orWhere(function ($q) {
            $q->where('is_system', 0)
              ->where('country_id', 1); // يرجع stages خاصة بالدولة فقط
        })
        ->orderBydesc('created_at');
        return $query;
    }

    /**
     * Get order stage by ID
     */
    public function getOrderStageById($id)
    {
        return OrderStage::with(['translations', 'country'])->findOrFail($id);
    }

    /**
     * Create new order stage
     */
    public function createOrderStage(array $data)
    {
        return DB::transaction(function () use ($data) {
            $orderStage = OrderStage::create([
                'slug' => rand(1, 1000),
                'color' => $data['color'] ?? '#3498db',
                'active' => $data['active'] ?? 1,
                'is_system' => $data['is_system'] ?? 0,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            // Store translations
            $this->storeTranslations($orderStage, $data);

            return $orderStage;
        });
    }

    /**
     * Update order stage
     */
    public function updateOrderStage($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $orderStage = $this->getOrderStageById($id);

            $orderStage->update([
                'color' => $data['color'] ?? $orderStage->color,
                'active' => $data['active'] ?? $orderStage->active,
                'sort_order' => $data['sort_order'] ?? $orderStage->sort_order,
            ]);

            // Store translations (this will handle updates)
            $this->storeTranslations($orderStage, $data);

            return $orderStage->fresh();
        });
    }

    /**
     * Delete order stage
     */
    public function deleteOrderStage($id)
    {
        $orderStage = $this->getOrderStageById($id);

        // Prevent deletion of system stages
        if ($orderStage->is_system) {
            throw new \Exception(__('order::order_stage.cannot_delete_system_stage'));
        }

        return $orderStage->delete();
    }

    /**
     * Get active order stages
     */
    public function getActiveOrderStages()
    {
        return OrderStage::active()->with(['translations'])->orderBy('sort_order')->get();
    }

    /**
     * Toggle order stage status
     */
    public function toggleOrderStageStatus($id)
    {
        $orderStage = $this->getOrderStageById($id);
        $orderStage->update(['active' => !$orderStage->active]);
        return $orderStage->fresh();
    }

    /**
     * Store translations for order stage
     */
    protected function storeTranslations(OrderStage $orderStage, array $data): void
    {
        // Force delete existing translations (including soft deleted ones)
        $orderStage->translations()->forceDelete();

        if (!empty($data['translations'])) {
            Log::info('Storing translations for order stage', [
                'order_stage_id' => $orderStage->id,
                'translations_data' => $data['translations']
            ]);

            foreach ($data['translations'] as $languageId => $fields) {
                $language = \App\Models\Language::find($languageId);
                if (!$language) {
                    continue;
                }

                // Store all translation fields
                $translationFields = ['name'];

                foreach ($translationFields as $field) {
                    if (isset($fields[$field])) {

                        if($field == 'name' && $language->code == 'en') {
                            // Generate slug from English name
                            if(OrderStage::where('slug', Str::slug($fields[$field]))->where('id', '!=', $orderStage->id)->exists()) {
                                $model = OrderStage::where('slug', Str::slug($fields[$field]))->where('id', '!=', $orderStage->id)->first();
                                $orderStage->update([
                                    'slug' => $model->slug . '-' . rand(1, 1000)
                                ]);
                            } else {
                                $orderStage->update([
                                    'slug' => Str::slug($fields[$field])
                                ]);
                            }
                        }

                        Log::info('Creating order stage translation', [
                            'field' => $field,
                            'language' => $language->code,
                            'value' => $fields[$field]
                        ]);

                        $orderStage->translations()->create([
                            'lang_id' => $language->id,
                            'lang_key' => $field,
                            'lang_value' => $fields[$field],
                        ]);
                    }
                }
            }
        }
    }
}

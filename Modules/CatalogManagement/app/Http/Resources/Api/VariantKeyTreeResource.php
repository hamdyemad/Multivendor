<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantKeyTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'key_id' => $this->id,
            'key_name' => $this->{"name_{$locale}"} ?? null,
            'options' => $this->buildOptionsFromVariants($locale),
        ];
    }

    /**
     * Build options from variants
     */
    private function buildOptionsFromVariants($locale)
    {
        $options = [];

        // Get all parent variants (variants without parent_id)
        $parentVariants = $this->variants()->whereNull('parent_id')->with('childrenRecursive.key')->get();

        foreach ($parentVariants as $variant) {
            $option = [
                'id' => $variant->id,
                'name' => $variant->{"name_{$locale}"} ?? null,
                'color' => $variant->color ?? null,
            ];

            // If this variant has children, build their tree
            if ($variant->childrenRecursive->isNotEmpty()) {
                $option['children'] = $this->buildChildrenTree($variant->childrenRecursive, $locale);
            }

            $options[] = $option;
        }

        return $options;
    }

    /**
     * Build children tree recursively
     */
    private function buildChildrenTree($children, $locale)
    {
        // Group children by their key
        $groupedByKey = $children->groupBy('key_id');

        $result = [];

        foreach ($groupedByKey as $keyId => $variants) {
            $firstVariant = $variants->first();
            $key = $firstVariant->key;

            if (!$key) {
                continue;
            }

            $keyData = [
                'key_id' => $key->id,
                'key_name' => $key->{"name_{$locale}"} ?? null,
                'options' => []
            ];

            foreach ($variants as $variant) {
                $option = [
                    'id' => $variant->id,
                    'name' => $variant->{"name_{$locale}"} ?? null,
                    'color' => $variant->color ?? null,
                ];

                // If this variant has children, recursively build their tree
                if ($variant->childrenRecursive->isNotEmpty()) {
                    $option['children'] = $this->buildChildrenTree($variant->childrenRecursive, $locale);
                }

                $keyData['options'][] = $option;
            }

            $result[] = $keyData;
        }

        return $result;
    }
}

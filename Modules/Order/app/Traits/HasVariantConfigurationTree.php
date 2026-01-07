<?php

namespace Modules\Order\app\Traits;

trait HasVariantConfigurationTree
{
    /**
     * Build configuration tree for the selected variant only
     * Returns: key -> variant structure
     */
    protected function buildVariantConfigurationTree($configuration, $variantId, $locale): array
    {
        if (!$configuration || !$configuration->key) {
            return [];
        }

        $key = $configuration->key;
        
        // Get color value - only if type is 'color', use the value field
        $colorValue = null;
        if ($configuration->type === 'color' && $configuration->value) {
            $colorValue = $configuration->value;
        }

        return [
            [
                'id' => $key->id,
                'name' => $key->getTranslation('name', $locale) ?? $key->name,
                'type' => 'key',
                'children' => [
                    [
                        'id' => $configuration->id,
                        'variant_id' => $variantId,
                        'name' => $configuration->getTranslation('name', $locale) ?? $configuration->name ?? $configuration->value,
                        'value' => $configuration->value,
                        'type' => $configuration->type,
                        'color' => $colorValue,
                        'key_id' => $configuration->key_id,
                        'parent_id' => $configuration->parent_id,
                        'is_selected' => true,
                    ]
                ]
            ]
        ];
    }
}

<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $variantName = 'Default Variant';
        $variantTree = null;

        if ($this->variantConfiguration) {
            // Get variant value name (e.g., "Red", "Large")
            $variantName = $this->variantConfiguration->name ?? 'Default';

            // Build recursive tree structure
            $variantTree = $this->buildVariantTree($this->variantConfiguration);
        }

        return [
            'id' => $this->id,
            'name' => $variantName,
            'sku' => null, // SKU is stored in vendor_product_variants, not product_variants
            'key' => [
                'id' => $this->variantConfiguration->key->id ?? null,
                'name' => $this->variantConfiguration->key->name ?? null,
            ],
            'variant_configuration_id' => $this->variant_configuration_id,
            'variant_tree' => $variantTree,
        ];
    }

    /**
     * Build recursive variant configuration tree
     */
    private function buildVariantTree($configuration, $visited = [])
    {
        // Prevent infinite recursion
        if (in_array($configuration->id, $visited)) {
            return null;
        }

        $visited[] = $configuration->id;

        $tree = [
            'id' => $configuration->id,
            'name' => $configuration->name,
            'key' => [
                'id' => $configuration->key->id ?? null,
                'name' => $configuration->key->name ?? null,
            ],
            'children' => [],
            'parent' => null
        ];

        // Add parent configuration if exists
        if ($configuration->parent_data) {
            $tree['parent'] = $this->buildVariantTree($configuration->parent_data, $visited);
        }

        // Add child configurations if they exist
        if ($configuration->children && $configuration->children->count() > 0) {
            foreach ($configuration->children as $child) {
                $childTree = $this->buildVariantTree($child, $visited);
                if ($childTree) {
                    $tree['children'][] = $childTree;
                }
            }
        }

        return $tree;
    }
}

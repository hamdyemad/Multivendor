<?php

namespace Modules\CatalogManagement\app\Models;

use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes, Translation, HasSlug;

    protected $table = 'product_variants';
    protected $guarded = [];

    // Note: Price-related fields have been moved to VendorProductVariant table
    // This model now only stores variant configuration (key, value, etc.)

    /**
     * The field to generate slug from (for HasSlug trait)
     * Since variants don't have direct name field, we'll override the slug generation
     */
    protected $slugFrom = 'name';

    /**
     * Override slug source value generation for variants
     * Generate slug based on product title and variant configuration
     */
    protected function getSourceValueForSlug(): ?string
    {
        // Try to get product title first
        $productTitle = null;
        if ($this->product_id && $this->product) {
            $productTitle = $this->product->getTranslation('title') ?: 'product';
        }

        // Try to get variant configuration name
        $variantName = null;
        if ($this->variantConfiguration) {
            $variantName = $this->variantConfiguration->getTranslation('name') ?: 'variant';
        }

        // Combine product title and variant name
        if ($productTitle && $variantName) {
            return $productTitle . ' ' . $variantName;
        } elseif ($productTitle) {
            return $productTitle . ' variant';
        } elseif ($variantName) {
            return $variantName;
        }

        // Fallback to a generic name
        return 'variant-' . ($this->id ?: uniqid());
    }

    /**
     * Get the product that owns the variant
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    /**
     * Get the variant configuration value
     */
    public function variantConfiguration()
    {
        return $this->belongsTo(VariantsConfiguration::class, 'variant_configuration_id');
    }

}

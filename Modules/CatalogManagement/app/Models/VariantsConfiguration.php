<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariantsConfiguration extends Model
{
    use HasFactory, Translation, HumanDates;

    protected $table = 'variants_configurations';
    protected $guarded = [];


    // scope
    public function scopeParent($query)
    {
        return $query->whereNull("parent_id");
    }

    // relationships
    public function key()
    {
        return $this->belongsTo(VariantConfigurationKey::class, 'key_id');
    }

    public function parent_data()
    {
        return $this->belongsTo(VariantsConfiguration::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive', 'key');
    }


    /**
     * Get all of the ProductVariantConfiguration for the VariantsConfiguration
     * TODO: Uncomment when ProductVariantConfiguration model is created
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function productVariantConfiguration(): HasMany
    // {
    //     return $this->hasMany(ProductVariantConfiguration::class, 'variants_configuration_id');
    // }

    /**
     * Get all of the comments for the VariantsConfiguration
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function comments(): HasMany
    // {
    //     return $this->hasMany(Comment::class, 'foreign_key', 'local_key');
    // }
}

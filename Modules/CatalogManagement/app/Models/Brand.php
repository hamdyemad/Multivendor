<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, Translation, SoftDeletes, HumanDates, HasSlug;

    protected $table = 'brands';
    protected $guarded = [];

    /**
     * Get all attachments for the brand
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the logo attachment
     */
    public function logo()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'logo');
    }

    /**
     * Get the cover attachment
     */
    public function cover()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'cover');
    }

}

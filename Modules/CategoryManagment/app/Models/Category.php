<?php

namespace Modules\CategoryManagment\app\Models;

use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CategoryManagment\app\Models\DepartmentTranslation;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Category extends Model
{
    use HasFactory, SoftDeletes, Translation, HumanDates;

    protected $guarded = [];


    /**
     * Attachments relationship
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get category image
     */
    public function getImageAttribute()
    {
        $imageAttachment = $this->attachments()->where('type', 'image')->first();
        return $imageAttachment ? $imageAttachment->path : null;
    }

    /**
     * Department relationship
     */
    public function department() {
        return $this->belongsTo(Department::class);
    }

    /**
     * Activities relationship
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activities_categories', 'category_id', 'activity_id');
    }

}

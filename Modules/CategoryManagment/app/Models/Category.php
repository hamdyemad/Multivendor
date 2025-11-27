<?php

namespace Modules\CategoryManagment\app\Models;

use App\Models\BaseModel;
use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CategoryManagment\app\Models\DepartmentTranslation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;


class Category extends BaseModel
{
    use HasFactory, SoftDeletes, Translation, HumanDates, HasSlug;

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

    public function subs()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }

    public function activeSubs()
    {
        return $this->subs()->active();
    }

    public function getDescriptionAttribute()
    {
        return $this->getTranslation('description', app()->getLocale()) ?? '-';
    }

    /**
     * Activities relationship
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activities_categories', 'category_id', 'activity_id');
    }

}

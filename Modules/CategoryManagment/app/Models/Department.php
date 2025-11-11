<?php

namespace Modules\CategoryManagment\app\Models;

use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CategoryManagment\app\Models\DepartmentTranslation;

class Department extends Model
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
     * Get department image
     */
    public function getImageAttribute()
    {
        $imageAttachment = $this->attachments()->where('type', 'image')->first();
        return $imageAttachment ? $imageAttachment->path : null;
    }

    /**
     * Activities relationship
     */
    public function activities() {
        return $this->belongsToMany(Activity::class, 'activities_departments', 'department_id', 'activity_id');
    }

}

<?php

namespace Modules\CategoryManagment\app\Models;

use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, Translation, SoftDeletes, HumanDates;

    protected $table = 'activities';
    protected $guarded = [];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\ActivityFactory::new();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_activities', 'activity_id', 'category_id');
    }
}

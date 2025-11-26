<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory, Translation, SoftDeletes, HumanDates;

    protected $table = 'taxes';
    protected $guarded = [];


    public function getNameAttribute() {
        return $this->getTranslation('name', app()->getLocale()) ?? '';
    }
}

<?php

namespace Modules\SystemSetting\app\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use Translation, SoftDeletes;
    
    protected $table = 'currencies';
    protected $guarded = [];
}

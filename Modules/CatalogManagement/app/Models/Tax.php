<?php

namespace Modules\CatalogManagement\app\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory, Translation, SoftDeletes;
    
    protected $table = 'taxes';
    protected $guarded = [];
}

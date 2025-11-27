<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFilterScopes;

/**
 * Base Model
 *
 * All models that need filtering should extend this class
 * Provides common filter scopes through HasFilterScopes trait
 */
class BaseModel extends Model
{
    use HasFilterScopes;
}

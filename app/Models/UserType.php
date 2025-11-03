<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $table = 'users_types';
    protected $guarded = [];
    const SUPER_ADMIN_TYPE = 1;
    const ADMIN_TYPE = 2;
    const VENDOR_TYPE = 3;
    const VENDOR_USER_TYPE = 4;
}

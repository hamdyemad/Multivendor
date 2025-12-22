<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Translation;

class UserType extends Model
{
    use Translation;
    protected $table = 'users_types';
    protected $guarded = [];
    const SUPER_ADMIN_TYPE = 1;
    const ADMIN_TYPE = 2;
    const VENDOR_TYPE = 3;
    const VENDOR_USER_TYPE = 4;

    public static function adminIds(): array
    {
        return [
            self::SUPER_ADMIN_TYPE,
            self::ADMIN_TYPE,
        ];
    }

    public static function vendorIds(): array
    {
        return [
            self::VENDOR_TYPE,
            self::VENDOR_USER_TYPE,
        ];
    }
}

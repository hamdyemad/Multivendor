<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permession extends Model
{
    protected $fillable = [
        'type',
        'module',
        'sub_module',
        'key',
        'module_icon',
        'color',
        'name_en',
        'name_ar',
    ];

    /**
     * Get the roles that have this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permession', 'permession_id', 'role_id')
                    ->withTimestamps();
    }
}

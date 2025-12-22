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

    /**
     * Helper to get translated attributes.
     */
    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        // Handle specific fields that have _en/_ar suffixes
        if (in_array($field, ['name'])) {
            $key = $field . '_' . ($locale == 'ar' ? 'ar' : 'en');
            return $this->$key ?? $this->name_en;
        }

        // Fallback for group_by or module
        if ($field === 'group_by' || $field === 'module') {
            return $this->module;
        }

        return $this->$field ?? null;
    }
}

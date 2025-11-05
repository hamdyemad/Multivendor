<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use Translation, SoftDeletes;
    
    protected $guarded = [];

    
    const SUPER_ADMIN_ROLE_TYPE = 'super_admin';
    const ADMIN_ROLE_TYPE = 'admin';
    const VENDOR_ROLE_TYPE = 'vendor';
    const OTHER_ROLE_TYPE = 'other';

    
    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['name'];

    /**
     * Get the role name from translations based on current locale.
     * This accessor allows accessing $role->name even though there's no name column.
     */
    public function getNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale()) 
               ?? $this->getTranslation('name', config('app.fallback_locale'))
               ?? 'Unnamed Role';
    }

    /**
     * Get the permissions for the role.
     */
    public function permessions()
    {
        return $this->belongsToMany(Permession::class, 'role_permession', 'role_id', 'permession_id')
                    ->withTimestamps();
    }

    public function scopeSuperAdminShowRoles($query) {
        return $query->whereIn('type', [Role::ADMIN_ROLE_TYPE, Role::VENDOR_ROLE_TYPE, Role::OTHER_ROLE_TYPE]);
    }
    public function scopeAdminShowRoles($query) {
        return $query->whereIn('type', [Role::ADMIN_ROLE_TYPE,Role::VENDOR_ROLE_TYPE, Role::OTHER_ROLE_TYPE]);
    }
    public function scopeVendorShowRoles($query) {
        return $query->whereIn('type', [Role::OTHER_ROLE_TYPE]);
    }


    public function scopeFilter($query, $filter = []) {
        if(isset($filter['with'])) {
            $query->with($filter['with']);
        }
        
        if(isset($filter['search']) && !empty($filter['search'])) {
            $query->whereHas('translations', function($query) use ($filter) {
                $query->where('lang_value', 'like', '%' . $filter['search'] . '%');
            });
        }

        // Apply date range filter
        if (isset($filter['created_date_from']) && !empty($filter['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filter['created_date_from']);
        }
        if (isset($filter['created_date_to']) && !empty($filter['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filter['created_date_to']);
        }
        return $query;
    }
}

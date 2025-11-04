<?php

namespace App\Models;

use App\Traits\Translation;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Modules\Vendor\app\Models\Vendor;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Translation, SoftDeletes;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['roles'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user type for the user.
     */
    public function user_type()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }


    /**
     * Get the roles for the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->permessions()->where('key', $permission)->exists()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function vendor() {
        return $this->hasOne(Vendor::class, 'user_id');
    }


    public function scopeActive(Builder $builder) {
        $builder->where('active', 1);
    }
    public function scopeUnActive(Builder $builder) {
        $builder->where('active', 0);
    }
    public function scopeBlocked(Builder $builder) {
        $builder->where('block', 1);
    }
    public function scopeUnBlocked(Builder $builder) {
        $builder->where('block', 0);
    }

    public function scopeSuperAdminShow($query) {
        return $query->whereIn('user_type_id', [UserType::ADMIN_TYPE, UserType::VENDOR_USER_TYPE]);
    }
    public function scopeAdminShow($query) {
        return $query->whereIn('user_type_id', [UserType::ADMIN_TYPE, UserType::VENDOR_USER_TYPE]);
    }
    public function scopeVendorShow($query) {
        return $query->whereIn('user_type_id', [UserType::VENDOR_USER_TYPE]);
    }
    public function scopeOtherShow($query) {
        return $query->whereIn('user_type_id', [UserType::VENDOR_USER_TYPE]);
    }


    public function scopeFilter(Builder $query, array $filters) {
        // Search filter
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('translations', function ($query) use ($searchTerm) {
                    $query->where('lang_key', 'name')
                        ->where('lang_value', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Active status filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Role filter
        if (!empty($filters['role_id'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('roles.id', $filters['role_id']);
            });
        }

        // Date range filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }
        
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }
    }
}

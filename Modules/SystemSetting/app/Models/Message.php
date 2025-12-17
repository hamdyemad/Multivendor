<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $table = 'messages';
    protected $guarded = [];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope: Get pending messages
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get read messages
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope: Get archived messages
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope: Search messages
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%");
        }
        return $query;
    }
}

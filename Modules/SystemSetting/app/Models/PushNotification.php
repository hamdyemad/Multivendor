<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\BaseModel;
use App\Models\User;
use App\Models\Traits\HumanDates;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\AreaSettings\app\Models\Country;
use Modules\Customer\app\Models\Customer;

class PushNotification extends BaseModel
{
    use HasFactory, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    const TYPE_ALL = 'all';
    const TYPE_SPECIFIC = 'specific';
    const TYPE_ALL_VENDORS = 'all_vendors';
    const TYPE_SPECIFIC_VENDORS = 'specific_vendors';

    protected $fillable = [
        'type',
        'title',
        'description',
        'image',
        'created_by',
        'country_id',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'push_notification_customers')
            ->withTimestamps();
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(\Modules\Vendor\app\Models\Vendor::class, 'push_notification_vendors')
            ->withTimestamps();
    }

    /**
     * Get users who viewed this notification
     */
    public function views(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'push_notification_views')
            ->withPivot('created_at', 'updated_at')
            ->withTimestamps();
    }

    /**
     * Check if a user has viewed this notification
     */
    public function isViewedBy(int $userId): bool
    {
        return $this->views()->where('user_id', $userId)->exists();
    }

    /**
     * Mark notification as viewed by a user
     */
    public function markAsViewedBy(int $userId): void
    {
        if (!$this->isViewedBy($userId)) {
            $this->views()->attach($userId);
        }
    }

    /**
     * Get translation for a field by language code
     * Compatible with translation-display component
     */
    public function getTranslation(string $field, string $locale): ?string
    {
        $value = $this->getAttribute($field);
        
        if (is_array($value)) {
            return $value[$locale] ?? null;
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded[$locale] ?? null;
            }
        }
        
        return null;
    }

    /**
     * Get all translations for a field
     */
    public function getTranslations(string $field): array
    {
        $value = $this->getAttribute($field);
        
        if (is_array($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        
        return [];
    }
}

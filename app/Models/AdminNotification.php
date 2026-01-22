<?php

namespace App\Models;

use App\Models\Traits\HumanDates;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminNotification extends Model
{
    use HumanDates, CountryCheckIdTrait, AutoStoreCountryId;

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'user_id',
        'vendor_id',
        'icon',
        'color',
        'title',
        'description',
        'url',
        'data',
        'is_read',
        'read_at',
        'country_id',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'title' => \App\Casts\TranslatableCast::class,
        'description' => \App\Casts\TranslatableCast::class,
    ];

    /**
     * Get the notifiable model
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user (admin)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the views for this notification
     */
    public function views(): HasMany
    {
        return $this->hasMany(AdminNotificationView::class);
    }

    /**
     * Check if notification has been viewed by user
     */
    public function hasBeenViewedBy(int $userId): bool
    {
        return $this->views()->where('user_id', $userId)->exists();
    }

    /**
     * Mark as viewed by user
     */
    public function markAsViewedBy(int $userId): void
    {
        $this->views()->firstOrCreate([
            'user_id' => $userId,
        ], [
            'viewed_at' => now(),
        ]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for admin notifications (no specific user/vendor)
     */
    public function scopeForAdmin($query)
    {
        return $query->whereNull('user_id')->whereNull('vendor_id');
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereNull('user_id');
        });
    }

    /**
     * Scope for specific vendor
     */
    public function scopeForVendor($query, int $vendorId)
    {
        return $query->where(function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId)
              ->orWhereNull('vendor_id');
        });
    }

    /**
     * Scope for notifications not viewed by user
     */
    public function scopeNotViewedBy($query, int $userId)
    {
        return $query->whereDoesntHave('views', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Get translated description with data replacements
     * The TranslatableCast automatically handles translation, but we need to replace placeholders
     */
    public function getTranslatedDescription(): string
    {
        $description = $this->description ?? '';
        
        // If we have data array, replace placeholders
        if ($this->data && is_array($this->data)) {
            foreach ($this->data as $key => $value) {
                // Convert value to string if it's an array or object
                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value);
                }
                
                // Replace :key with value (e.g., :refund_number with actual refund number)
                // Extract the last part of the translation key (e.g., 'refund_number' from 'refund::refund.refund_number')
                $placeholder = str_contains($key, '.') ? substr($key, strrpos($key, '.') + 1) : $key;
                $description = str_replace(':' . $placeholder, (string)$value, $description);
            }
        }
        
        return $description;
    }

    /**
     * Get translated title with data replacements
     * Returns the title with placeholders replaced from data array
     */
    public function getTranslatedTitle(): string
    {
        $title = $this->title ?? '';
        
        // If we have data array, replace placeholders
        if ($this->data && is_array($this->data)) {
            foreach ($this->data as $key => $value) {
                // Convert value to string if it's an array or object
                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value);
                }
                
                // Replace :key with value
                $placeholder = str_contains($key, '.') ? substr($key, strrpos($key, '.') + 1) : $key;
                $title = str_replace(':' . $placeholder, (string)$value, $title);
            }
        }
        
        return $title;
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Create notification helper
     */
    public static function notify(
        string $type,
        string $title,
        ?string $description = null,
        ?string $url = null,
        string $icon = 'uil-bell',
        string $color = 'primary',
        ?Model $notifiable = null,
        ?array $data = null,
        ?int $userId = null,
        ?int $vendorId = null
    ): self {
        return self::create([
            'type' => $type,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable?->id,
            'user_id' => $userId,
            'vendor_id' => $vendorId,
            'icon' => $icon,
            'color' => $color,
            'title' => $title,
            'description' => $description,
            'url' => $url,
            'data' => $data,
        ]);
    }
}

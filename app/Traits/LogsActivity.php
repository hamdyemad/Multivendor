<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Models\User;

trait LogsActivity
{
    /**
     * Log an activity for authenticated user
     */
    public function logActivity(
        string $action,
        string $descriptionKey,
        array $descriptionParams = [],
        $model = null,
        array $properties = []
    ) {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description_key' => $descriptionKey,
            'description_params' => $descriptionParams,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log an activity for a specific user (even if not authenticated)
     */
    public function logActivityForUser(
        User $user,
        string $action,
        string $descriptionKey,
        array $descriptionParams = [],
        $model = null,
        array $properties = []
    ) {
        return ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description_key' => $descriptionKey,
            'description_params' => $descriptionParams,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
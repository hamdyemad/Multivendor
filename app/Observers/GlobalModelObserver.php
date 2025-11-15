<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class GlobalModelObserver
{
    /**
     * Models to exclude from logging
     */
    private array $excludedModels = [
        ActivityLog::class,
        'App\Models\PersonalAccessToken',
        'App\Models\PasswordReset',
        'App\Models\FailedJob',
        'App\Models\Translation',
    ];

    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        if ($this->shouldLog($model)) {
            $this->logActivity($model, 'created');
        }
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        if ($this->shouldLog($model) && $model->wasChanged()) {
            $this->logActivity($model, 'updated', [
                'old' => $this->getOnlyChangedOriginal($model),
                'new' => $model->getChanges(),
            ]);
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        if ($this->shouldLog($model)) {
            $this->logActivity($model, 'deleted', [
                'attributes' => $model->getOriginal()
            ]);
        }
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        if ($this->shouldLog($model)) {
            $this->logActivity($model, 'restored');
        }
    }

    /**
     * Handle the Model "force deleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        if ($this->shouldLog($model)) {
            $this->logActivity($model, 'force_deleted', [
                'attributes' => $model->getOriginal()
            ]);
        }
    }

    /**
     * Check if model should be logged
     */
    private function shouldLog(Model $model): bool
    {
        // Check if model is in excluded list
        foreach ($this->excludedModels as $excludedModel) {
            if ($model instanceof $excludedModel || get_class($model) === $excludedModel) {
                return false;
            }
        }

        // Skip if no authenticated user
        if (!auth()->check()) {
            return false;
        }

        return true;
    }

    /**
     * Get only the changed attributes from original
     */
    private function getOnlyChangedOriginal(Model $model): array
    {
        $changes = $model->getChanges();
        $original = $model->getOriginal();
        
        return array_intersect_key($original, $changes);
    }

    /**
     * Log the activity
     */
    private function logActivity(
        Model $model,
        string $action,
        array $properties = []
    ): void {
        $modelName = class_basename($model);
        $identifier = $model->id;
        
        // Map actions to translation keys
        $descriptionKeys = [
            'created' => 'activity_log.created_model',
            'updated' => 'activity_log.updated_model',
            'deleted' => 'activity_log.deleted_model',
            'restored' => 'activity_log.restored_model',
            'force_deleted' => 'activity_log.force_deleted_model',
        ];

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model' => get_class($model),
            'model_id' => $model->id,
            'description_key' => $descriptionKeys[$action] ?? null,
            'description_params' => [
                'model' => __("activity_log.models.{$modelName}"),
                'identifier' => $identifier,
            ],
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
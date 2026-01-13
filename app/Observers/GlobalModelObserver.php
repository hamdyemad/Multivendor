<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class GlobalModelObserver
{
    /**
     * Models to exclude from logging and country_id storage
     */
    private array $excludedModels = [
        ActivityLog::class,
        'App\Models\PersonalAccessToken',
        'Modules\Vendor\app\Models\Vendor',
        'App\Models\PasswordReset',
        'App\Models\FailedJob',
        'App\Models\Translation',
        'Modules\AreaSettings\app\Models\Country',
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
            // Get changes excluding only updated_at (when touch() is called for related data changes)
            $changes = $model->getChanges();
            $meaningfulChanges = array_diff_key($changes, ['updated_at' => true]);
            
            // If only updated_at changed (from touch()), log without old/new details
            // This handles cases where related data (like translations) changed
            if (empty($meaningfulChanges) && isset($changes['updated_at'])) {
                $this->logActivity($model, 'updated', [
                    'note' => 'Related data updated',
                ]);
            } else {
                $this->logActivity($model, 'updated', [
                    'old' => $this->getOnlyChangedOriginal($model),
                    'new' => $changes,
                ]);
            }
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

        // Skip Country model queries during filtering operations
        if (get_class($model) === 'Modules\AreaSettings\app\Models\Country') {
            // Check if this is a filtering operation by looking at the stack trace
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);
            foreach ($trace as $frame) {
                if (isset($frame['class']) && isset($frame['function'])) {
                    // Skip if called from CountryCheckIdTrait filtering methods
                    if (str_contains($frame['class'], 'CountryCheckIdTrait') || 
                        $frame['function'] === 'resolveCountryId' ||
                        $frame['function'] === 'scopeForCountry') {
                        return false;
                    }
                }
            }
        }

        // Skip if no authenticated user (check both web and admin guards)
        $user = auth()->user() ?? auth('web')->user();
        if (!$user) {
            return false;
        }

        // Skip if request is from API routes
        if ($this->isApiRequest()) {
            return false;
        }

        return true;
    }

    /**
     * Check if current request is from API
     */
    private function isApiRequest(): bool
    {
        return request()->is('api/*');
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
        try {
            $modelName = class_basename($model);
            $identifier = $model->id;
            
            // Get user from any available guard
            $user = auth()->user() ?? auth('web')->user();
            $userId = $user?->id;

            // Map actions to translation keys
            $descriptionKeys = [
                'created' => 'activity_log.created_model',
                'updated' => 'activity_log.updated_model',
                'deleted' => 'activity_log.deleted_model',
                'restored' => 'activity_log.restored_model',
                'force_deleted' => 'activity_log.force_deleted_model',
            ];

            ActivityLog::create([
                'user_id' => $userId,
                'action' => $action,
                'model' => get_class($model),
                'model_id' => $model->id,
                'description_key' => $descriptionKeys[$action] ?? null,
                'description_params' => [
                    'model' => $modelName, // Store just the model name, translate when displaying
                    'identifier' => $identifier,
                ],
                'properties' => $properties,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'country_id' => session('country_id'),
            ]);
        } catch (\Exception $e) {
            Log::error('GlobalModelObserver logActivity error: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id ?? null,
                'action' => $action,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

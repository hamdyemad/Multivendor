<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description_key',
        'description_params',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'description_params' => 'array',
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'translated_description',
        'translated_action',
        'model_name',
        'user_name',
    ];

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject model (polymorphic)
     */
    public function subject()
    {
        if (!$this->model || !$this->model_id) {
            return null;
        }

        if (!class_exists($this->model)) {
            return null;
        }

        return $this->model::find($this->model_id);
    }

    /**
     * Get translated description
     */
    public function getTranslatedDescriptionAttribute(): string
    {
        if (!$this->description_key) {
            return '';
        }

        $params = $this->description_params ?? [];
        
        // If we have model information, try to get the translated identifier
        if ($this->model && isset($params["identifier"])) {
            $modelClass = $this->model;
            $modelId = $this->model_id;
            
            // Get the model instance
            if (class_exists($modelClass)) {
                try {
                    // Build query
                    $query = $modelClass::query();
                    
                    // Check if model uses SoftDeletes trait and handle it
                    if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($modelClass) ?: [])) {
                        $query = $query->withTrashed();
                    }
                    
                    $modelInstance = $query->find($modelId);
                    
                    if ($modelInstance) {
                        // Check if model has getTranslation method from Translation trait
                        if (method_exists($modelInstance, 'getTranslation')) {
                            // Get current locale
                            $locale = app()->getLocale() ?? 'en';
                            
                            // Get translated name
                            $translatedName = $modelInstance->getTranslation('name', $locale);
                            
                            // If translation found, use it; otherwise keep the ID
                            if ($translatedName) {
                                $params['identifier'] = $translatedName;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Silent fail - use the original identifier (ID)
                }
            }
        }

        // Use Laravel's translation with parameters
        return __($this->description_key, $params);
    }

    /**
     * Get translated action
     */
    public function getTranslatedActionAttribute(): string
    {
        return __("activity_log.actions.{$this->action}");
    }

    /**
     * Get formatted model name
     */
    public function getModelNameAttribute(): string
    {
        if (!$this->model) {
            return 'N/A';
        }

        $modelBaseName = class_basename($this->model);
        return __("activity_log.models.{$modelBaseName}");
    }

    /**
     * Get user name or 'System' if no user
     */
    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->email : __('System');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by model
     */
    public function scopeByModel($query, $model)
    {
        return $query->where('model', $model);
    }
}
<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the HasSlug trait for a model.
     */
    public static function bootHasSlug()
    {
        static::creating(function ($model) {
            $model->slug = $model->generateSlug($model->name);
        });

        static::updating(function ($model) {
            // Update slug if name changed
            if ($model->isDirty('name')) {
                $model->slug = $model->generateSlug($model->name);
            }
        });
    }

    /**
     * Generate a unique slug for the model.
     */
    protected function generateSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 2;

        // Ensure slug is unique in this model's table
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}

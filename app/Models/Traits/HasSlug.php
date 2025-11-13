<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the HasSlug trait for a model.
     */
    public static function bootHasSlug()
    {
        // For models with translations, use 'saved' event to ensure translations are committed
        if (method_exists(static::class, 'getTranslation')) {
            static::saved(function ($model) {
                $model->handleTranslationBasedSlug();
            });
        } else {
            // For regular models, use creating/updating events
            static::creating(function ($model) {
                if (empty($model->slug)) {
                    $model->slug = $model->generateSlug();
                }
            });

            static::updating(function ($model) {
                if ($model->shouldRegenerateSlug()) {
                    $model->slug = $model->generateSlug();
                }
            });
        }
    }

    /**
     * Handle slug generation for translation-based models
     */
    protected function handleTranslationBasedSlug()
    {
        // Skip if slug generation is disabled for this instance
        if (property_exists($this, 'skipSlugGeneration') && $this->skipSlugGeneration) {
            return;
        }

        $shouldGenerate = false;

        // For new models, always generate if no slug exists
        if ($this->wasRecentlyCreated && empty($this->slug)) {
            $shouldGenerate = true;
        }
        // For existing models, check if translation changed
        elseif (!$this->wasRecentlyCreated) {
            $shouldGenerate = $this->shouldRegenerateSlugForTranslations();
        }

        if ($shouldGenerate) {
            // Generate new slug
            $newSlug = $this->generateSlug();
            
            if ($newSlug && $newSlug !== $this->slug) {
                // Update without triggering events to avoid infinite loop
                $this->skipSlugGeneration = true;
                
                // Update only the slug column
                static::withoutEvents(function () use ($newSlug) {
                    $this->newQuery()->where($this->getKeyName(), $this->getKey())->update(['slug' => $newSlug]);
                });
                
                $this->skipSlugGeneration = false;
                
                // Update the current model instance
                $this->slug = $newSlug;
            }
        }
    }

    /**
     * Handle slug generation for translation-based models (legacy method)
     */
    protected function handleSlugGeneration()
    {
        // Skip if slug generation is disabled for this instance
        if (property_exists($this, 'skipSlugGeneration') && $this->skipSlugGeneration) {
            return;
        }

        $shouldGenerate = false;

        // For new models, always generate if no slug exists
        if ($this->wasRecentlyCreated && empty($this->slug)) {
            $shouldGenerate = true;
        }
        // For existing models, check if source field changed
        elseif (!$this->wasRecentlyCreated) {
            $shouldGenerate = $this->shouldRegenerateSlugForTranslations();
        }

        if ($shouldGenerate) {
            // Generate new slug
            $newSlug = $this->generateSlug();
            
            // Update without triggering events to avoid infinite loop
            $this->skipSlugGeneration = true;
            $this->updateQuietly(['slug' => $newSlug]);
            $this->skipSlugGeneration = false;
        }
    }

    /**
     * Generate a unique slug for the model.
     */
    protected function generateSlug()
    {
        // Get the source text for slug generation
        $sourceText = $this->getSlugSourceText();

        if (empty($sourceText)) {
            $sourceText = $this->getSlugFallbackText();
        }

        // Generate base slug
        $baseSlug = Str::slug($sourceText);

        // Add suffix if configured
        $suffix = $this->getSlugSuffix();
        $slug = $suffix ? $baseSlug . '-' . $suffix : $baseSlug;

        // Ensure uniqueness
        return $this->ensureUniqueSlug($slug, $baseSlug);
    }

    /**
     * Get the source text for slug generation.
     * Override this method in your model to customize slug source.
     */
    protected function getSlugSourceText()
    {
        // Check if model uses translations
        if (method_exists($this, 'getTranslation')) {
            return $this->getSlugFromTranslations();
        }

        // Fallback to direct field access
        $slugField = $this->getSlugSourceField();
        return $this->$slugField ?? null;
    }

    /**
     * Get slug from translations (for models using Translation trait)
     */
    protected function getSlugFromTranslations()
    {
        $slugField = $this->getSlugSourceField();
        $slugLanguage = $this->getSlugLanguage();

        // Try to get from translations in request data first (during creation/update)
        if (request()->has('translations')) {
            $translations = request()->input('translations');
            foreach ($translations as $langId => $fields) {
                $language = \App\Models\Language::find($langId);
                if ($language && $language->code === $slugLanguage && !empty($fields[$slugField])) {
                    return $fields[$slugField];
                }
            }
        }

        // Fallback to existing translation
        return $this->getTranslation($slugField, $slugLanguage);
    }

    /**
     * Get the field name to use for slug generation.
     * Override this method in your model to customize.
     */
    protected function getSlugSourceField()
    {
        return 'name';
    }

    /**
     * Get the language code to use for slug generation (for translated models).
     * Override this method in your model to customize.
     */
    protected function getSlugLanguage()
    {
        return 'en';
    }

    /**
     * Get fallback text when source text is empty.
     * Override this method in your model to customize.
     */
    protected function getSlugFallbackText()
    {
        return class_basename(static::class);
    }

    /**
     * Get suffix to append to slug.
     * Override this method in your model to customize.
     * Return null for no suffix.
     */
    protected function getSlugSuffix()
    {
        // Check if model wants random suffix
        if (property_exists($this, 'slugWithRandomSuffix') && $this->slugWithRandomSuffix) {
            $suffixLength = property_exists($this, 'slugSuffixLength') ? $this->slugSuffixLength : 6;
            return strtolower(Str::random($suffixLength));
        }

        return null;
    }

    /**
     * Determine if slug should be regenerated on update.
     * Override this method in your model to customize.
     */
    protected function shouldRegenerateSlug()
    {
        // For models with translations
        if (method_exists($this, 'getTranslation')) {
            return $this->shouldRegenerateSlugForTranslations();
        }

        // For regular models, check if source field is dirty
        $slugField = $this->getSlugSourceField();
        return $this->isDirty($slugField);
    }

    /**
     * Check if slug should be regenerated for translation-based models
     */
    protected function shouldRegenerateSlugForTranslations()
    {
        $slugField = $this->getSlugSourceField();
        $slugLanguage = $this->getSlugLanguage();
        
        // Get current translation value
        $currentValue = $this->getTranslation($slugField, $slugLanguage);
        
        // If no translation exists yet, we should generate a slug
        if (empty($currentValue)) {
            return true;
        }
        
        // For existing models, we'll regenerate if we have request data with translations
        // This ensures the slug is always current with the latest translation
        if (request()->has('translations')) {
            $translations = request()->input('translations');
            
            foreach ($translations as $langId => $fields) {
                $language = \App\Models\Language::find($langId);
                if ($language && $language->code === $slugLanguage && isset($fields[$slugField])) {
                    // Always regenerate to ensure slug is current
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Ensure the slug is unique in the database.
     */
    protected function ensureUniqueSlug($slug, $baseSlug)
    {
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            // If we have a random suffix, generate a new one
            if ($this->getSlugSuffix()) {
                $suffix = $this->getSlugSuffix();
                $slug = $baseSlug . '-' . $suffix;
            } else {
                // Use incremental counter for non-random suffixes
                $count++;
                $slug = $baseSlug . '-' . $count;
            }

            // Prevent infinite loop
            if ($count > 50) {
                $slug = $baseSlug . '-' . strtolower(Str::random(8));
                break;
            }
        }

        return $slug;
    }
}

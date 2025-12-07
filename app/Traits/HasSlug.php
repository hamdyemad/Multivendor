<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::created(function ($model) {
            // Generate slug after creation when translations are available
            $model->generateSlugOnCreate();
        });

        static::updated(function ($model) {
            // Update slug after update when translations are available
            $model->generateSlugOnUpdate();
        });

        // Auto-regenerate slug if it's still random (for translated models)
        // This handles cases where slug was generated before translations were saved
        static::retrieved(function ($model) {
            if ($model->usesTranslationTrait() && $model->shouldRegenerateSlug()) {
                $model->regenerateSlug();
            }
        });
    }

    /**
     * Check if slug should be regenerated (it's null or still a random string)
     */
    protected function shouldRegenerateSlug(): bool
    {
        $slug = $this->{$this->slugColumn()};
        // If slug is null or 4 random characters, it needs regeneration
        return empty($slug) || (strlen($slug) === 4 && ctype_alnum($slug));
    }

    /**
     * Public method to regenerate slug manually
     */
    public function regenerateSlug(): void
    {
        $this->generateAndSetSlug();
        $this->save();
    }

    /**
     * Generate the slug when a model is being created.
     * This runs after creation, so translations are already saved.
     * For models with Translation trait, if translations aren't loaded yet,
     * they will be regenerated when accessed via the retrieved event.
     */
    protected function generateSlugOnCreate()
    {
        // Skip if slug already set
        if ($this->{$this->slugColumn()}) {
            return;
        }

        // Generate slug
        $this->generateAndSetSlug();

        // Save slug without triggering events
        $this->saveQuietly();
    }

    /**
     * Generate the slug when a model is being updated,
     * only if the source field has changed.
     * This runs after update, so new translations are already saved.
     */
    protected function generateSlugOnUpdate()
    {
        $sourceField = $this->slugSource();
        $currentSlug = $this->{$this->slugColumn()};

        // Get what the slug should be
        $shouldBeSlug = $this->generateSlugValue();

        // Update slug only if it changed
        if ($currentSlug !== $shouldBeSlug) {
            $this->{$this->slugColumn()} = $shouldBeSlug;
            $this->saveQuietly();
        }
    }

    /**
     * Generate and return the slug value without saving
     */
    protected function generateSlugValue(): string
    {
        $sourceValue = $this->getSourceValueForSlug();
        $slug = Str::slug($sourceValue ?? '');
        return $this->makeSlugUnique($slug);
    }

    protected function generateAndSetSlug()
    {
        // Get the source value for slug generation
        $sourceValue = $this->getSourceValueForSlug();

        // Generate slug from source
        $slug = Str::slug($sourceValue ?? '');

        // Make slug unique and assign to column
        $this->{$this->slugColumn()} = $this->makeSlugUnique($slug);
    }

    /**
     * Get the source value for slug generation.
     * If model uses Translation trait, try to get translated value.
     * Otherwise, get the direct field value.
     *
     * @return string|null
     */
    protected function getSourceValueForSlug(): ?string
    {
        $sourceField = $this->slugSource();

        // Check if model uses Translation trait
        if ($this->usesTranslationTrait()) {
            // Ensure translations are loaded
            if (!$this->relationLoaded('translations')) {
                $this->load('translations');
            }

            // Try to get translated value for current locale
            if (method_exists($this, 'getTranslation')) {
                $locale = app()->getLocale() ?? 'en';
                $translatedValue = $this->getTranslation($sourceField, $locale);

                if ($translatedValue) {
                    return $translatedValue;
                }
            }
        }

        // Fallback to direct field value
        return $this->getAttribute($sourceField);
    }

    /**
     * Check if model uses Translation trait
     *
     * @return bool
     */
    protected function usesTranslationTrait(): bool
    {
        return in_array('App\Traits\Translation', class_uses($this) ?: []);
    }

    /**
     * Ensures the slug is unique in the database.
     *
     * @param string $slug
     * @return string
     */
    protected function makeSlugUnique(string $slug): string
    {
        // Remove numbers from the slug first
        $slug = preg_replace('/\d+/', '', $slug);
        $slug = Str::slug($slug, '-'); // clean up hyphens

        // Handle empty slug
        if (empty($slug)) {
            $slug = Str::random(4);
        }

        $originalSlug = $slug;
        $slugColumn = $this->slugColumn();

        // Build base query - bypass global scopes to check all records
        $query = static::withoutGlobalScopes()->where($slugColumn, $slug);

        // Handle SoftDeletes if used
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this), true)) {
            $query->withTrashed();
        }

        // If model has country_id, scope to current country
        if ($this->hasAttribute('country_id') && $this->country_id) {
            $query->where('country_id', $this->country_id);
        }

        // Exclude current model if updating
        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        // If slug exists, append 4 random characters until unique
        while ($query->exists()) {
            $slug = $originalSlug . '-' . Str::random(4);
            $slug = Str::slug($slug, '-');

            $query = static::withoutGlobalScopes()->where($slugColumn, $slug);

            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this), true)) {
                $query->withTrashed();
            }

            if ($this->hasAttribute('country_id') && $this->country_id) {
                $query->where('country_id', $this->country_id);
            }

            if ($this->exists) {
                $query->where($this->getKeyName(), '!=', $this->getKey());
            }
        }

        return $slug;
    }

    /**
     * Get the source column name for the slug
     *
     * @return string
     */
    protected function slugSource(): string
    {
        return property_exists($this, 'slugFrom') ? $this->slugFrom : 'name';
    }

    /**
     * Get the slug column name
     *
     * @return string
     */
    protected function slugColumn(): string
    {
        return property_exists($this, 'slugTo') ? $this->slugTo : 'slug';
    }
}

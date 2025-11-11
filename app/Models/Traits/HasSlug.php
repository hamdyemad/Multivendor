<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            $model->generateSlugOnCreate();
        });

        static::updating(function ($model) {
            $model->generateSlugOnUpdate();
        });
    }

    /**
     * Public method to regenerate slug manually
     */
    public function regenerateSlug(): void
    {
        $this->generateAndSetSlug();
    }

    /**
     * Generate the slug when a model is being created.
     */
    protected function generateSlugOnCreate()
    {
        // Don't generate a slug if both slugs have been manually set.
        if ($this->{$this->slugColumn('en')} && $this->{$this->slugColumn('ar')}) {
            return;
        }
        $this->generateAndSetSlug();
    }

    /**
     * Generate the slug when a model is being updated,
     * only if the source field has changed.
     */
    protected function generateSlugOnUpdate()
    {
        $sourceEn = $this->slugSource('en');
        $sourceAr = $this->slugSource('ar');

        if ($this->isDirty($sourceEn) || $this->isDirty($sourceAr)) {
            $this->generateAndSetSlug();
        }
    }

    protected function generateAndSetSlug()
    {
        $sourceEn = $this->slugSource('en');
        $sourceAr = $this->slugSource('ar');

        // Generate base slugs from source fields
        $slugEn = Str::slug($this->{$sourceEn} ?? '');
        $slugAr = Str::slug($this->{$sourceAr} ?? '');

        // Make slugs unique and assign to columns
        $this->{$this->slugColumn('en')} = $this->makeSlugUnique($slugEn, 'en');
        $this->{$this->slugColumn('ar')} = $this->makeSlugUnique($slugAr, 'ar');
    }

    /**
     * Ensures the slug is unique in the database.
     *
     * @param string $slug
     * @param string $lang
     * @return string
     */
    protected function makeSlugUnique(string $slug, string $lang = 'en'): string
    {
        // Remove numbers from the slug first
        $slug = preg_replace('/\d+/', '', $slug);
        $slug = Str::slug($slug, '-'); // clean up hyphens

        // Handle empty slug
        if (empty($slug)) {
            $slug = Str::random(4);
        }

        $originalSlug = $slug;
        $slugColumn = $this->slugColumn($lang);

        // Build base query
        $query = static::where($slugColumn, $slug);

        // Handle SoftDeletes if used
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this), true)) {
            $query->withTrashed();
        }

        // Exclude current model if updating
        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        // If slug exists, append 4 random characters until unique
        while ($query->exists()) {
            $slug = $originalSlug . '-' . Str::random(4);
            $slug = Str::slug($slug, '-');

            $query = static::where($slugColumn, $slug);

            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this), true)) {
                $query->withTrashed();
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
     * @param string $lang
     * @return string
     */
    protected function slugSource(string $lang = 'en'): string
    {
        $baseColumn = property_exists($this, 'slugFrom') ? $this->slugFrom : 'name';
        return $baseColumn . '_' . $lang;
    }

    /**
     * Get the slug column name
     *
     * @param string $lang
     * @return string
     */
    protected function slugColumn(string $lang = 'en'): string
    {
        $baseColumn = property_exists($this, 'slugTo') ? $this->slugTo : 'slug';
        return $baseColumn . '_' . $lang;
    }
}
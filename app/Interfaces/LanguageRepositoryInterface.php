<?php

namespace App\Interfaces;

use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;

interface LanguageRepositoryInterface
{
    /**
     * Get all languages
     */
    public function getAll(): Collection;

    /**
     * Get a language by ID
     */
    public function getById(int $id): ?Language;

}

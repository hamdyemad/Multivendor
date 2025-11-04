<?php

namespace App\Repositories;

use App\Interfaces\LanguageRepositoryInterface;
use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;

class LanguageRepository implements LanguageRepositoryInterface
{
    /**
     * Get all languages
     */
    public function getAll(): Collection
    {
        return Language::orderBy('id')->get();
    }

    public function getById(int $id): ?Language
    {
        return Language::find($id);
    }
}

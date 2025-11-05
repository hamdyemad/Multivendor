<?php

namespace App\Services;

use App\Interfaces\LanguageRepositoryInterface;
use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;

class LanguageService
{
    public function __construct(public LanguageRepositoryInterface $languageRepository)
    {

    }

    /**
     * Get all languages
     */
    public function getAll(): Collection
    {
        return $this->languageRepository->getAll();
    }

    public function getById(int $id): ?Language
    {
        return $this->languageRepository->getById($id);
    }
}

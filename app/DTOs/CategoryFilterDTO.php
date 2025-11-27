<?php

namespace App\DTOs;

/**
 * Category Filter DTO
 *
 * Supported Filters:
 * - search: Search by name/translation
 * - active: Filter by active status (true/false)
 * - created_date_from: Filter from date (YYYY-MM-DD)
 * - created_date_to: Filter to date (YYYY-MM-DD)
 * - department_id: Department ID or slug
 * - per_page: Items per page
 * - paginated: Enable pagination
 */
class CategoryFilterDTO extends FilterDTO
{
    private array $errors = [];

    public function __construct(
        public ?string $search = null,
        public ?bool $active = null,
        public ?string $created_date_from = null,
        public ?string $created_date_to = null,
        public ?string $department_id = null,
        public ?int $per_page = null,
        public bool $paginated = false,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'active' => $this->active,
            'created_date_from' => $this->created_date_from,
            'created_date_to' => $this->created_date_to,
            'department_id' => $this->department_id,
            'per_page' => $this->per_page,
            'paginated' => $this->paginated,
        ], fn($value) => $value !== null);
    }

    public function validate(): bool
    {
        $this->errors = [];

        if ($this->created_date_from && !$this->isValidDate($this->created_date_from)) {
            $this->errors[] = 'created_date_from must be a valid date (YYYY-MM-DD)';
        }

        if ($this->created_date_to && !$this->isValidDate($this->created_date_to)) {
            $this->errors[] = 'created_date_to must be a valid date (YYYY-MM-DD)';
        }

        return count($this->errors) === 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function isValidDate(string $date): bool
    {
        return strtotime($date) !== false;
    }
}

<?php

namespace Modules\CategoryManagment\app\DTOs;

use App\DTOs\FilterDTO;


class DepartmentFilterDTO extends FilterDTO
{
    private array $errors = [];

    public function __construct(
        public ?string $search = null,
        public ?string $created_date_from = null,
        public ?string $created_date_to = null,
        public ?string $vendor_id = null,
        public ?string $brand_id = null,
        public ?int $per_page = null,
        public ?string $paginated = null,
    ) {}

    /**
     * Create DTO from HTTP request
     */
    public static function fromRequest($request): self
    {
        return new self(
            search: $request->input('search'),
            created_date_from: $request->input('created_date_from'),
            created_date_to: $request->input('created_date_to'),
            vendor_id: $request->input('vendor_id'),
            brand_id: $request->input('brand_id'),
            per_page: $request->integer('per_page', 10),
            paginated: $request->input('paginated', null)
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'created_date_from' => $this->created_date_from,
            'created_date_to' => $this->created_date_to,
            'vendor_id' => $this->vendor_id,
            'brand_id' => $this->brand_id,
            'per_page' => $this->per_page,
            'paginated' => $this->paginated,
        ], fn($value) => $value !== null);
    }

    public function validate(): bool
    {
        $this->errors = [];

        if ($this->created_date_from && !$this->isValidDate($this->created_date_from)) {
            $this->errors['created_date_from'][] = __('validation.created_date_from_invalid');
        }

        if ($this->created_date_to && !$this->isValidDate($this->created_date_to)) {
            $this->errors['created_date_to'][] = __('validation.created_date_to_invalid');
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

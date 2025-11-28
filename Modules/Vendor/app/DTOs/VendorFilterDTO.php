<?php

namespace Modules\Vendor\app\DTOs;

use App\DTOs\FilterDTO;

class VendorFilterDTO extends FilterDTO
{
    private array $errors = [];

    public function __construct(
        public ?string $search = null,
        public ?string $country_id = null,
        public ?string $id = null,
        public ?int $per_page = null,
        public bool $paginated = false,
    ) {}

    /**
     * Create DTO from HTTP request
     */
    public static function fromRequest($request): self
    {
        return new self(
            search: $request->input('search'),
            country_id: $request->input('country_id'),
            id: $request->input('id'),
            per_page: $request->integer('per_page', 15),
            paginated: $request->boolean('paginated', false)
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'country_id' => $this->country_id,
            'id' => $this->id,
            'per_page' => $this->per_page,
            'paginated' => $this->paginated,
        ], fn($value) => $value !== null);
    }

    public function validate(): bool
    {
        $this->errors = [];

        if ($this->country_id && !$this->countryExists($this->country_id)) {
            $this->errors['country_id'][] = __('validation.country_id_not_exist');
        }

        return count($this->errors) === 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function countryExists(string $countryId): bool
    {
        return \Modules\AreaSettings\app\Models\Country::where('id', $countryId)->exists();
    }
}

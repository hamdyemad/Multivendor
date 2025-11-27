<?php

namespace Modules\Customer\app\DTOs;

class GetAddressesDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $country_id = null,
        public ?int $city_id = null,
        public ?int $region_id = null,
        public ?int $subregion_id = null,
        public ?int $is_primary = null,
        public bool $paginated = false,
        public ?int $per_page = null,
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            country_id: $data['country_id'] ?? null,
            city_id: $data['city_id'] ?? null,
            region_id: $data['region_id'] ?? null,
            subregion_id: $data['subregion_id'] ?? null,
            is_primary: $data['is_primary'] ?? null,
            paginated: $data['paginated'] ?? false,
            per_page: $data['per_page'] ?? null,
        );
    }

    /**
     * Get filters array for model scope
     */
    public function getFilters(): array
    {
        return array_filter([
            'search' => $this->search,
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'region_id' => $this->region_id,
            'subregion_id' => $this->subregion_id,
            'is_primary' => $this->is_primary,
        ], fn($value) => $value !== null);
    }
}

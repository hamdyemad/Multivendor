<?php

namespace Modules\Customer\app\DTOs;

class GetAddressesDTO
{
    public function __construct(
        public ?string $search = null,
        public ?string $country_id = null,
        public ?string $city_id = null,
        public ?string $region_id = null,
        public ?string $subregion_id = null,
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
            search: !empty($data['search']) ? (string) $data['search'] : null,
            country_id: !empty($data['country_id']) ? (string) $data['country_id'] : null,
            city_id: !empty($data['city_id']) ? (string) $data['city_id'] : null,
            region_id: !empty($data['region_id']) ? (string) $data['region_id'] : null,
            subregion_id: !empty($data['subregion_id']) ? (string) $data['subregion_id'] : null,
            is_primary: self::castIsPrimary($data['is_primary'] ?? null),
            paginated: (bool) ($data['paginated'] ?? false),
            per_page: !empty($data['per_page']) ? (int) $data['per_page'] : null,
        );
    }

    /**
     * Cast is_primary to valid integer (0 or 1) or null if invalid
     */
    private static function castIsPrimary(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Valid values: 1, true, "1", "true"
        if ($value === 1 || $value === true || $value === '1' || $value === 'true') {
            return 1;
        }

        // Valid values: 0, false, "0", "false"
        if ($value === 0 || $value === false || $value === '0' || $value === 'false') {
            return 0;
        }

        // Invalid value - return null to exclude from filters
        return null;
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'region_id' => $this->region_id,
            'subregion_id' => $this->subregion_id,
            'is_primary' => $this->is_primary,
            'paginated' => $this->paginated,
            'per_page' => $this->per_page,
        ], fn($value) => $value !== null);
    }
}

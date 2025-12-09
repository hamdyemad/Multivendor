<?php

namespace Modules\Order\app\DTOs;

class OrderFilterDTO
{
    public function __construct(
        public ?int $stage_id = null,
        public ?string $search = null,
        public ?string $created_date_from = null,
        public ?string $created_date_to = null,
        public int $per_page = 15,
        public ?string $paginated = null,
    ) {}

    /**
     * Create DTO from request
     */
    public static function fromRequest($request): self
    {
        return new self(
            stage_id: $request->query('stage_id'),
            search: $request->query('search'),
            created_date_from: $request->query('created_date_from'),
            created_date_to: $request->query('created_date_to'),
            per_page: $request->query('per_page', 15),
            paginated: $request->input('paginated', null),
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'stage_id' => $this->stage_id,
            'search' => $this->search,
            'created_date_from' => $this->created_date_from,
            'created_date_to' => $this->created_date_to,
            'per_page' => $this->per_page,
            'paginated' => $this->paginated,
        ];
    }
}

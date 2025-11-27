<?php

namespace App\DTOs;

/**
 * Base Filter DTO
 *
 * Data Transfer Object for filter parameters
 * Ensures type safety and consistency across the application
 */
abstract class FilterDTO
{
    /**
     * Convert DTO to array for use in queries
     */
    abstract public function toArray(): array;

    /**
     * Validate filter data
     */
    abstract public function validate(): bool;

    /**
     * Get validation errors
     */
    abstract public function getErrors(): array;

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }
}

<?php

namespace Modules\CatalogManagement\app\DTOs;

use App\DTOs\FilterDTO;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\Customer\app\Models\Customer;
use Modules\Vendor\app\Models\Vendor;
use PhpParser\Node\Scalar\String_;

class ReviewFilterDTO extends FilterDTO
{
    private array $errors = [];

    public function __construct(
        public ?int $reviewable_id = null,
        public ?string $reviewable_type = null,
        public ?string $customer_id = null,
        public ?string $status = null,
        public ?int $min_star = null,
        public ?int $max_star = null,
        public ?string $sort_by = null,
        public ?string $sort_type = null,
        public ?int $per_page = null,
        public ?string $paginated = null,
    ) {}

    /**
     * Create DTO from HTTP request
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            reviewable_id: $request->filled('reviewable_id') ? $request->integer('reviewable_id') : null,
            reviewable_type: $request->filled('reviewable_type') ? $request->input('reviewable_type') : null,
            customer_id: $request->filled('customer_id') ? $request->integer('customer_id') : null,
            status: $request->filled('status') ? $request->input('status') : null,
            min_star: $request->filled('min_star') ? $request->integer('min_star') : null,
            max_star: $request->filled('max_star') ? $request->integer('max_star') : null,
            sort_by: $request->filled('sort_by') ? $request->input('sort_by') : null,
            sort_type: $request->filled('sort_type') ? $request->input('sort_type') : null,
            per_page: $request->filled('per_page') ? $request->integer('per_page') : 15,
            paginated: $request->filled('paginated') ? $request->input('paginated') : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'reviewable_id' => $this->reviewable_id,
            'reviewable_type' => $this->reviewable_type,
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'min_star' => $this->min_star,
            'max_star' => $this->max_star,
            'sort_by' => $this->sort_by,
            'sort_type' => $this->sort_type,
            'per_page' => $this->per_page,
            'paginated' => $this->paginated,
        ], fn($value) => $value !== null);
    }

    public function validate(): bool
    {
        $this->errors = [];

        // Only validate if values are provided
        if ($this->min_star !== null && ($this->min_star < 1 || $this->min_star > 5)) {
            $this->errors['min_star'][] = __('validation.min_star_range');
        }

        if ($this->max_star !== null && ($this->max_star < 1 || $this->max_star > 5)) {
            $this->errors['max_star'][] = __('validation.max_star_range');
        }

        if ($this->min_star !== null && $this->max_star !== null && $this->min_star > $this->max_star) {
            $this->errors['min_star'][] = __('validation.min_star_max_star');
        }

        if ($this->status !== null && !in_array($this->status, ['pending', 'approved', 'rejected'])) {
            $this->errors['status'][] = __('validation.status_invalid');
        }

        if ($this->sort_by !== null && !in_array($this->sort_by, ['created_at', 'star', 'updated_at'])) {
            $this->errors['sort_by'][] = __('validation.sort_by_invalid');
        }

        if ($this->sort_type !== null && !in_array($this->sort_type, ['asc', 'desc'])) {
            $this->errors['sort_type'][] = __('validation.sort_type_invalid');
        }

        if ($this->reviewable_id !== null) {
            if ($this->reviewable_type == 'products') {
                if (!$this->vendorProductExists($this->reviewable_id)) {
                    $this->errors['reviewable_id'][] = __('validation.product_not_found');
                }
            } elseif ($this->reviewable_type == 'vendors') {
                if (!$this->vendorExists($this->reviewable_id)) {
                    $this->errors['reviewable_id'][] = __('validation.vendor_not_found');
                }
            }
        }

        if ($this->customer_id !== null && !$this->customerExists($this->customer_id)) {
            $this->errors['customer_id'][] = __('validation.customer_id_not_exist');
        }

        return count($this->errors) === 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function vendorProductExists(int $vendorProductId): bool
    {
        return VendorProduct::where('id', $vendorProductId)->exists();
    }

    private function vendorExists(int $vendorId): bool
    {
        return Vendor::where('id', $vendorId)->exists();
    }

    private function customerExists(String $customerId): bool
    {
        return Customer::where('id', $customerId)->exists();
    }
}

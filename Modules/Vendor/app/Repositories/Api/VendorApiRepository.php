<?php

namespace Modules\Vendor\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Illuminate\Support\Facades\DB;
use Modules\Vendor\app\Actions\Api\VendorQueryAction;
use Modules\Vendor\app\DTOs\VendorFilterDTO;
use Modules\Vendor\app\Interfaces\Api\VendorApiRepositoryInterface;
use Modules\Vendor\app\Models\Vendor;
use Modules\Vendor\app\Models\VendorRequest;

class VendorApiRepository implements VendorApiRepositoryInterface
{
    public function __construct(protected VendorQueryAction $query, protected IsPaginatedAction $paginated) {}

    /**
     * Get all Vendors with filters and pagination
     */
    public function getAllVendors(VendorFilterDTO $dto)
    {
        $filters = $dto->toArray();
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $dto->per_page, $dto->paginated);
        return $result;
    }

    /**
     * Get Vendor by ID
     */
    public function find(VendorFilterDTO $dto, $id)
    {
        $filters = $dto->toArray();
        return $this->query->handle($filters)->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->firstOrFail();
    }

    /**
     * Create a new vendor request
     */
    public function createVendorRequest(array $data)
    {
        return DB::transaction(function () use ($data) {
            $createData = [
                'email' => $data['email'],
                'phone' => $data['phone'],
                'company_name' => $data['company_name'],
                'manager_name' => $data['manager_name'] ?? null,
                'status' => 'pending',
            ];
            // Handle company logo upload
            if (!empty($data['company_logo'])) {
                $createData['company_logo'] = $data['company_logo']->store('vendor-requests', 'public');
            }

            $vendorRequest = VendorRequest::create($createData);

            return $vendorRequest;
        });
    }
}

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
        $result = $this->paginated->handle($query, $dto->paginated, $dto->per_page);
        return $result;
    }

    /**
     * Get Vendor by ID
     */
    public function find(VendorFilterDTO $dto, $id)
    {
        $filters = $dto->toArray();
        return $this->query->handle($filters)->with('activeActivities')->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->firstOrFail();
    }

    /**
     * Create a new vendor request
     */
    public function createVendorRequest(array $data)
    {
        return DB::transaction(function () use ($data) {
            $vendorRequest = VendorRequest::create([
                'email' => $data['email'],
                'phone' => $data['phone'],
                'company_name' => $data['company_name'],
                'status' => 'pending',
            ]);

            // Attach activities to vendor request
            if (!empty($data['activities'])) {
                $vendorRequest->activities()->attach($data['activities']);
            }

            return $vendorRequest->load('activities');
        });
    }
}

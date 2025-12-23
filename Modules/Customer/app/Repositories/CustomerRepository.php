<?php

namespace Modules\Customer\app\Repositories;

use App\Actions\IsPaginatedAction;
use Carbon\Carbon;
use Modules\Customer\app\Actions\CustomerQueryAction;
use Modules\Customer\app\Interfaces\CustomerRepositoryInterface;
use Modules\Customer\app\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function __construct(protected CustomerQueryAction $query, protected IsPaginatedAction $paginated) {}

    public function getAllCustomers(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $filters["per_page"] ?? null, $paginated);
        return $result;
    }

    public function getCustomersQuery(array $filters = [])
    {
        return $this->query->handle($filters);
    }

    public function getCustomerById(int $id): ?Customer
    {
        $query = Customer::query();
        
        // If current user is a vendor, only allow access to customers in their vendor OR customers with no vendor
        if (auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $query->where(function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id)
                      ->orWhereNull('vendor_id');
                });
            }
        }
        
        return $query->findOrFail($id);
    }

    public function findById($id, array $filters = [])
    {
        return $this->query->handle($filters)->where('id', $id)->firstOrFail();
    }

    public function createCustomer(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Add vendor_id if user is vendor and vendor_id not provided
            if (!isAdmin() && !isset($data['vendor_id'])) {
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if ($vendor) {
                    $data['vendor_id'] = $vendor->id;
                }
            }

            $customer = Customer::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'] ?? null,
                'image' => $data['image'] ?? null,
                'status' => $data['status'] ?? true,
                'gender' => $data['gender'],
                'city_id' => $data['city_id'],
                'region_id' => $data['region_id'],
                'lang' => $data['lang'] ?? 'en',
                'vendor_id' => $data['vendor_id'] ?? null,
                'email_verified_at' => Carbon::now(),
            ]);

            if (!empty($data['addresses']) && is_array($data['addresses'])) {
                foreach ($data['addresses'] as $address) {
                    $customer->addresses()->create($address);
                }
            }

            return $customer;
        });
    }

    public function updateCustomer(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $query = Customer::query();
            
            // If current user is a vendor, only allow access to customers in their vendor OR customers with no vendor
            if (auth()->check() && auth()->user()->isVendor()) {
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if ($vendor) {
                    $query->where(function ($q) use ($vendor) {
                        $q->where('vendor_id', $vendor->id)
                          ->orWhereNull('vendor_id');
                    });
                }
            }
            
            $customer = $query->findOrFail($id);
            \Log::info($data);

            $updateData = [
                'first_name' => $data['first_name'] ?? $customer->first_name,
                'last_name' => $data['last_name'] ?? $customer->last_name,
                'email' => $data['email'] ?? $customer->email,
                'phone' => $data['phone'] ?? $customer->phone,
                'image' => $data['image'] ?? $customer->image,
                'status' => $data['status'] ?? $customer->status,
                'lang' => $data['lang'] ?? $customer->lang,
                'gender' => $data['gender'] ?? $customer->gender,
                'city_id' => $data['city_id'] ?? $customer->city_id,
                'region_id' => $data['region_id'] ?? $customer->region_id,
                'email_verified_at' => Carbon::now(),
            ];

            // Preserve vendor_id (don't allow changing it)
            if (isset($data['vendor_id'])) {
                $updateData['vendor_id'] = $data['vendor_id'];
            }

            // Only update password if provided
            if (!empty($data['password'])) {
                $updateData['password'] = $data['password'];
            }

            $customer->update($updateData);

            if (!empty($data['addresses']) && is_array($data['addresses'])) {
                $customer->addresses()->delete();
                foreach ($data['addresses'] as $address) {
                    $customer->addresses()->create($address);
                }
            }

            return $customer->load(['addresses', 'fcmTokens']);
        });
    }

    public function deleteCustomer(int $id)
    {
        $query = Customer::query();
        
        // If current user is a vendor, only allow access to customers in their vendor OR customers with no vendor
        if (auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $query->where(function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id)
                      ->orWhereNull('vendor_id');
                });
            }
        }
        
        $customer = $query->findOrFail($id);
        return $customer->delete();
    }

    public function createOtp(string $email, string $otp, string $type): \Modules\Customer\app\Models\CustomerOtp
    {
        return \Modules\Customer\app\Models\CustomerOtp::create([
            'email' => $email,
            'otp' => $otp,
            'type' => $type,
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    public function getByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    public function create(array $data): Customer
    {
        // Add vendor_id if user is vendor and vendor_id not provided
        if (!isAdmin() && !isset($data['vendor_id'])) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $data['vendor_id'] = $vendor->id;
            }
        }
        
        return Customer::create($data);
    }

    public function createPasswordResetToken(string $email, string $token): \Modules\Customer\app\Models\CustomerPasswordResetToken
    {
        return \Modules\Customer\app\Models\CustomerPasswordResetToken::updateOrCreate(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => now(),
                'expires_at' => now()->addHours(1),
            ]
        );
    }

    public function getPasswordResetToken(string $email, string $token): ?\Modules\Customer\app\Models\CustomerPasswordResetToken
    {
        return \Modules\Customer\app\Models\CustomerPasswordResetToken::where('email', $email)
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function deletePasswordResetToken(string $email): void
    {
        \Modules\Customer\app\Models\CustomerPasswordResetToken::where('email', $email)->delete();
    }

    public function getCustomerCount(): int
    {
        $query = Customer::query();
        
        // Filter by vendor if user is a vendor - include vendor's customers AND customers with no vendor
        if (auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $query->where(function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id)
                      ->orWhereNull('vendor_id');
                });
            }
        }
        
        return $query->count();
    }

    /**
     * Get customer with full details by ID
     */
    public function getCustomerWithDetails($customerId)
    {
        $query = Customer::with(['addresses', 'fcmTokens']);
        
        // If current user is a vendor, only allow access to customers in their vendor OR customers with no vendor
        if (auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $query->where(function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id)
                      ->orWhereNull('vendor_id');
                });
            }
        }
        
        return $query->findOrFail($customerId);
    }

    /**
     * Get customer address by ID
     */
    public function getCustomerAddress($addressId)
    {
        return \Modules\Customer\app\Models\CustomerAddress::with(['country', 'city', 'region', 'subregion'])
            ->findOrFail($addressId);
    }

    /**
     * Get all customer addresses
     */
    public function getCustomerAddresses($customerId)
    {
        return \Modules\Customer\app\Models\CustomerAddress::where('customer_id', $customerId)
            ->with(['country', 'city', 'region', 'subregion'])
            ->get();
    }
}

<?php

namespace Modules\Customer\app\Repositories;

use App\Actions\IsPaginatedAction;
use Carbon\Carbon;
use Modules\Customer\app\Actions\CustomerQueryAction;
use Modules\Customer\app\Interfaces\CustomerRepositoryInterface;
use Modules\Customer\app\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function __construct(protected CustomerQueryAction $query, protected IsPaginatedAction $paginated) {}

    public function getAllCustomers(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }

    public function getCustomersQuery(array $filters = [])
    {
        return $this->query->handle($filters);
    }

    public function getCustomerById(int $id): ?Customer
    {
        return Customer::findOrFail($id);
    }

    public function findById(array $filters = [], $id)
    {
        return $this->query->handle($filters)->where('id', $id)->firstOrFail();
    }

    public function createCustomer(array $data)
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'phone' => $data['phone'] ?? null,
                'image' => $data['image'] ?? null,
                'status' => $data['status'] ?? true,
                'lang' => $data['lang'] ?? 'en',
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
            $customer = Customer::findOrFail($id);

            $updateData = [
                'first_name' => $data['first_name'] ?? $customer->first_name,
                'last_name' => $data['last_name'] ?? $customer->last_name,
                'email' => $data['email'] ?? $customer->email,
                'phone' => $data['phone'] ?? $customer->phone,
                'image' => $data['image'] ?? $customer->image,
                'status' => $data['status'] ?? $customer->status,
                'lang' => $data['lang'] ?? $customer->lang,
                'email_verified_at' => Carbon::now(),
            ];

            // Only update password if provided
            if (!empty($data['password'])) {
                $updateData['password'] = bcrypt($data['password']);
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
        $customer = Customer::findOrFail($id);
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
        return Customer::count();
    }
}

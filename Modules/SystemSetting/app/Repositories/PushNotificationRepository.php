<?php

namespace Modules\SystemSetting\app\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerFcmToken;
use Modules\SystemSetting\app\Interfaces\PushNotificationRepositoryInterface;
use Modules\SystemSetting\app\Models\PushNotification;
use Modules\SystemSetting\app\Services\FirebaseService;

class PushNotificationRepository implements PushNotificationRepositoryInterface
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function all(array $filters = [])
    {
        $query = PushNotification::with(['createdBy', 'customers', 'vendors'])
            ->latest();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query;
    }

    public function find($id)
    {
        return PushNotification::with(['createdBy', 'customers', 'vendors'])->findOrFail($id);
    }

    public function createAndSend(array $data): PushNotification
    {
        DB::beginTransaction();

        try {
            // Create notification record
            $notification = PushNotification::create([
                'type' => $data['type'],
                'title' => [
                    'en' => $data['title_en'],
                    'ar' => $data['title_ar'],
                ],
                'description' => [
                    'en' => $data['description_en'],
                    'ar' => $data['description_ar'],
                ],
                'image' => $data['image'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Handle customer notifications
            if ($data['type'] === PushNotification::TYPE_ALL) {
                $customerIds = Customer::active()->pluck('id')->toArray();
                if (!empty($customerIds)) {
                    $notification->customers()->attach($customerIds);
                }
            } elseif ($data['type'] === PushNotification::TYPE_SPECIFIC) {
                $customerIds = $data['customer_ids'] ?? [];
                if (!empty($customerIds)) {
                    $notification->customers()->attach($customerIds);
                }
            }
            // Handle vendor notifications
            elseif ($data['type'] === PushNotification::TYPE_ALL_VENDORS) {
                $vendorIds = \Modules\Vendor\app\Models\Vendor::where('active', 1)->pluck('id')->toArray();
                if (!empty($vendorIds)) {
                    $notification->vendors()->attach($vendorIds);
                }
            } elseif ($data['type'] === PushNotification::TYPE_SPECIFIC_VENDORS) {
                $vendorIds = $data['vendor_ids'] ?? [];
                if (!empty($vendorIds)) {
                    $notification->vendors()->attach($vendorIds);
                }
            }

            DB::commit();

            // Send notifications
            $this->sendNotification($notification);

            return $notification->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create push notification: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id)
    {
        $notification = PushNotification::findOrFail($id);

        // Delete image if exists
        if ($notification->image && Storage::disk('public')->exists($notification->image)) {
            Storage::disk('public')->delete($notification->image);
        }

        return $notification->delete();
    }

    /**
     * Send the push notification to all attached customers or vendors
     */
    protected function sendNotification(PushNotification $notification): void
    {
        // Get titles and descriptions using model method
        $titles = $notification->getTranslations('title');
        $descriptions = $notification->getTranslations('description');
        $imageUrl = $notification->image ? asset('storage/' . $notification->image) : null;

        // Send to customers
        $customerIds = $notification->customers()->pluck('customers.id')->toArray();
        if (!empty($customerIds)) {
            $fcmTokens = CustomerFcmToken::whereIn('customer_id', $customerIds)
                ->pluck('fcm_token', 'customer_id')
                ->toArray();

            foreach ($fcmTokens as $customerId => $token) {
                $customer = Customer::find($customerId);
                $lang = $customer->language ?? 'en';

                $title = $titles[$lang] ?? $titles['en'] ?? '';
                $body = $descriptions[$lang] ?? $descriptions['en'] ?? '';

                $this->firebaseService->sendToToken(
                    $token,
                    $title,
                    $body,
                    $imageUrl,
                    ['notification_id' => $notification->id]
                );
            }
        }

        // Send to vendors
        $vendorIds = $notification->vendors()->pluck('vendors.id')->toArray();
        if (!empty($vendorIds)) {
            $vendorFcmTokens = \Modules\Vendor\app\Models\VendorFcmToken::whereIn('vendor_id', $vendorIds)
                ->pluck('fcm_token', 'vendor_id')
                ->toArray();

            foreach ($vendorFcmTokens as $vendorId => $token) {
                $vendor = \Modules\Vendor\app\Models\Vendor::find($vendorId);
                $lang = $vendor->language ?? 'en';

                $title = $titles[$lang] ?? $titles['en'] ?? '';
                $body = $descriptions[$lang] ?? $descriptions['en'] ?? '';

                $this->firebaseService->sendToToken(
                    $token,
                    $title,
                    $body,
                    $imageUrl,
                    ['notification_id' => $notification->id]
                );
            }
        }
    }
}

<?php

namespace Modules\Order\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\Order\app\Actions\WishlistQueryAction;
use Modules\Order\app\Interfaces\Api\WishlistRepositoryInterface;
use Modules\Order\app\Models\Wishlist;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WishlistRepository implements WishlistRepositoryInterface
{
    public function __construct(protected WishlistQueryAction $query, protected IsPaginatedAction $paginated)
    {}

    /**
     * Get all wishlist items for a customer with pagination
     */
    public function getCustomerWishlist(array $data, $customerId)
    {
        $query = $this->query->handle($customerId);
        $result = $this->paginated->handle($query, $data['per_page'], $data['paginated']);
        return $result;
    }

    /**
     * Get a single wishlist item by ID
     */
    public function getWishlistItemById($customerId, $id)
    {
        return $this->query->handle($customerId)->findOrFail($id);
    }

    /**
     * Add a product to wishlist
     */
    public function addToWishlist($customerId, $vendorProductId)
    {
        return DB::transaction(function () use ($customerId, $vendorProductId) {
            // Check if already in wishlist
            $existing = $this->query->handle($customerId)
                ->where('vendor_product_id', $vendorProductId)
                ->first();

            if ($existing) {
                return $existing;
            }

            // Create new wishlist item
            return Wishlist::create([
                'customer_id' => $customerId,
                'vendor_product_id' => $vendorProductId,
            ]);
        });
    }

    /**
     * Remove a product from wishlist
     */
    public function removeFromWishlist($customerId, $vendorProductId)
    {
        return DB::transaction(function () use ($customerId, $vendorProductId) {
            $wishlistItem = $this->query->handle($customerId)
                ->where('vendor_product_id', $vendorProductId)
                ->firstOrFail();

            return $wishlistItem->delete();
        });
    }

    /**
     * Remove all items from wishlist
     */
    public function clearWishlist($customerId)
    {
        return DB::transaction(function () use ($customerId) {
            return $this->query->handle($customerId)->delete();
        });
    }

    /**
     * Check if product is in wishlist
     */
    public function isInWishlist($customerId, $vendorProductId): bool
    {
        return $this->query->handle($customerId)
            ->where('vendor_product_id', $vendorProductId)
            ->exists();
    }

    /**
     * Get wishlist count for customer
     */
    public function getWishlistCount($customerId): int
    {
        return $this->query->handle($customerId)->count();
    }
}

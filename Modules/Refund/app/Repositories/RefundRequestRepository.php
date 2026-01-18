<?php

namespace Modules\Refund\app\Repositories;

use Modules\Refund\app\Interfaces\RefundRequestRepositoryInterface;
use Modules\Refund\app\Models\RefundRequest;

class RefundRequestRepository implements RefundRequestRepositoryInterface
{
    protected $model;

    public function __construct(RefundRequest $model)
    {
        $this->model = $model;
    }

    public function getAllPaginated(array $filters = [], int $perPage = 15)
    {
        $query = $this->model
            ->with(['order', 'customer', 'vendor', 'items.orderProduct']);
        
        // By default, show vendor refunds in dashboard (not parent refunds)
        if (!isset($filters['show_parent'])) {
            $query->vendorOnly();
        }
        
        return $query->filter($filters)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id)
    {
        return $this->model
            ->with(['order', 'customer', 'vendor', 'items.orderProduct', 'vendorRefunds.items.orderProduct', 'parentRefund'])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $refund = $this->findById($id);
        $refund->update($data);
        return $refund->fresh();
    }

    public function delete(int $id)
    {
        $refund = $this->findById($id);
        return $refund->delete();
    }

    public function getStatistics(array $filters = [])
    {
        $query = $this->model->filter($filters);

        return [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'picked_up' => (clone $query)->where('status', 'picked_up')->count(),
            'refunded' => (clone $query)->where('status', 'refunded')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'total_amount' => (clone $query)->sum('total_refund_amount'),
        ];
    }

    public function canUserAccessRefund(int $refundId, $user): bool
    {
        $refund = $this->findById($refundId);

        // Admin can access all
        if ($user->isAdmin()) {
            return true;
        }

        // Vendor can access their refunds
        if ($user->vendor_id && $refund->vendor_id === $user->vendor_id) {
            return true;
        }

        // Customer can access their refunds
        if ($refund->customer_id === $user->id) {
            return true;
        }

        return false;
    }

    public function canUserCancelRefund(int $refundId, $user): bool
    {
        $refund = $this->findById($refundId);

        // Only customer can cancel
        if ($refund->customer_id !== $user->id) {
            return false;
        }

        // Can only cancel pending requests
        if ($refund->status !== 'pending') {
            return false;
        }

        return true;
    }

    public function createRefundWithVendorSplit(array $data, $user)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Get order
            $order = \Modules\Order\app\Models\Order::findOrFail($data['order_id']);

            // Verify customer owns this order
            if ($order->customer_id !== $user->id) {
                throw new \Exception('Unauthorized access to this order');
            }

            // Group items by vendor
            $itemsByVendor = $this->groupItemsByVendor($data['items']);

            // Create parent (customer) refund request
            $parentRefund = $this->create([
                'parent_refund_id' => null,
                'is_parent' => true,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'vendor_id' => null,
                'status' => 'pending',
                'reason' => $data['reason'],
                'customer_notes' => $data['customer_notes'] ?? null,
            ]);

            // Create vendor-specific refund requests
            foreach ($itemsByVendor as $vendorId => $vendorItems) {
                $this->createVendorRefund($parentRefund, $order, $vendorId, $vendorItems, $data);
            }

            // Calculate totals for parent refund
            $parentRefund->calculateTotals();

            \Illuminate\Support\Facades\DB::commit();

            // Reload with relationships
            return $this->getRefundWithRelations(
                $parentRefund->id,
                ['vendorRefunds.items.orderProduct', 'items']
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            throw $e;
        }
    }

    public function updateRefundStatus(int $id, array $data, $user)
    {
        // Check authorization
        if (!$this->canUserAccessRefund($id, $user)) {
            throw new \Exception('Unauthorized');
        }

        return $this->update($id, [
            'status' => $data['status'],
            'vendor_notes' => $data['notes'] ?? null,
        ]);
    }

    public function cancelRefund(int $id, $user)
    {
        // Check if user can cancel
        if (!$this->canUserCancelRefund($id, $user)) {
            throw new \Exception('Cannot cancel refund request in current status');
        }

        return $this->update($id, ['status' => 'cancelled']);
    }

    public function getRefundWithRelations(int $refundId, array $relations = [])
    {
        return $this->model->with($relations)->findOrFail($refundId);
    }

    /**
     * Group refund items by vendor
     */
    protected function groupItemsByVendor(array $items): array
    {
        $grouped = [];
        
        foreach ($items as $item) {
            $orderProduct = \Modules\Order\app\Models\OrderProduct::findOrFail($item['order_product_id']);
            $vendorId = $orderProduct->vendor_id;
            
            if (!isset($grouped[$vendorId])) {
                $grouped[$vendorId] = [];
            }
            
            $grouped[$vendorId][] = [
                'order_product_id' => $item['order_product_id'],
                'quantity' => $item['quantity'],
                'reason' => $item['reason'] ?? null,
                'order_product' => $orderProduct,
            ];
        }
        
        return $grouped;
    }

    /**
     * Create vendor-specific refund request
     */
    protected function createVendorRefund(
        $parentRefund,
        $order,
        int $vendorId,
        array $items,
        array $originalData
    ) {
        // Create vendor refund request
        $vendorRefund = $this->create([
            'parent_refund_id' => $parentRefund->id,
            'is_parent' => false,
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'vendor_id' => $vendorId,
            'status' => 'pending',
            'reason' => $originalData['reason'],
            'customer_notes' => $originalData['customer_notes'] ?? null,
        ]);

        // Create refund items for this vendor
        foreach ($items as $item) {
            $orderProduct = $item['order_product'];

            // Create vendor refund item
            \Modules\Refund\app\Models\RefundRequestItem::create([
                'refund_request_id' => $vendorRefund->id,
                'order_product_id' => $orderProduct->id,
                'vendor_id' => $vendorId,
                'quantity' => $item['quantity'],
                'unit_price' => $orderProduct->price,
                'total_price' => $orderProduct->price * $item['quantity'],
                'reason' => $item['reason'],
            ]);
            
            // Also create item reference in parent refund
            \Modules\Refund\app\Models\RefundRequestItem::create([
                'refund_request_id' => $parentRefund->id,
                'order_product_id' => $orderProduct->id,
                'vendor_id' => $vendorId,
                'quantity' => $item['quantity'],
                'unit_price' => $orderProduct->price,
                'total_price' => $orderProduct->price * $item['quantity'],
                'reason' => $item['reason'],
            ]);
        }

        // Calculate totals for vendor refund
        $vendorRefund->calculateTotals();

        return $vendorRefund;
    }
}

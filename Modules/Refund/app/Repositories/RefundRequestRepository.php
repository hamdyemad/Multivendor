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
            ->with([
                'order', 
                'customer', 
                'vendor', 
                'items.orderProduct.vendorProduct.product',
                'items.orderProduct.vendorProduct.vendor',
                'items.orderProduct.vendorProduct.variants.variantConfiguration.key',
                'items.orderProduct.vendorProduct.taxes',
                'items.orderProduct.vendorProductVariant',
                'history.user',
                'history.customer'
            ]);
        
        
        return $query->filter($filters)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id)
    {
        return $this->model
            ->with([
                'order', 
                'customer', 
                'vendor', 
                'items.orderProduct.vendorProduct.product',
                'items.orderProduct.vendorProduct.vendor',
                'items.orderProduct.vendorProduct.variants.variantConfiguration.key',
                'items.orderProduct.vendorProduct.taxes',
                'items.orderProduct.vendorProductVariant',
                'history.user',
                'history.customer'
            ])
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
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'total_amount' => (clone $query)->sum('total_refund_amount'),
        ];
    }

    public function canUserAccessRefund(int $refundId, $user): bool
    {
        $refund = $this->findById($refundId);

        // Admin can access all
        if (isAdmin()) {
            return true;
        }

        // Vendor can access their refunds
        if ($user->vendor && $refund->vendor_id === $user->vendor->id) {
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
            
            // Validate that we're not refunding more than purchased for each order product
            foreach ($data['items'] as $item) {
                $orderProduct = \Modules\Order\app\Models\OrderProduct::findOrFail($item['order_product_id']);
                
                // Calculate total already refunded for this order product (excluding cancelled)
                $totalRefunded = \Modules\Refund\app\Models\RefundRequestItem::where('order_product_id', $orderProduct->id)
                    ->whereHas('refundRequest', function($q) {
                        $q->whereNotIn('status', ['cancelled']);
                    })
                    ->sum('quantity');
                
                // Check if requested quantity exceeds available
                $availableForRefund = $orderProduct->quantity - $totalRefunded;
                if ($item['quantity'] > $availableForRefund) {
                    $message = trans('refund::refund.validation.quantity_exceeds_available', [
                        'requested' => $item['quantity'],
                        'available' => $availableForRefund,
                        'product' => $orderProduct->vendorProduct->product->name ?? 'Product #' . $orderProduct->id
                    ]);
                    \Log::error('Refund quantity validation failed', [
                        'order_product_id' => $orderProduct->id,
                        'requested_quantity' => $item['quantity'],
                        'available_quantity' => $availableForRefund,
                        'total_quantity' => $orderProduct->quantity,
                        'already_refunded' => $totalRefunded,
                    ]);
                    throw new \Exception($message);
                }
            }
            
            $createdRefunds = [];

            // Create independent refund requests for each vendor
            foreach ($itemsByVendor as $vendorId => $vendorItems) {
                // Calculate total quantity being refunded for this vendor
                $totalRefundedQty = collect($vendorItems)->sum('quantity');
                $totalOrderQtyForVendor = collect($vendorItems)->sum(fn($item) => $item['order_product']->quantity);
                
                // Calculate proportional fees/discounts/points for this vendor
                $vendorFees = \Illuminate\Support\Facades\DB::table('order_extra_fees_discounts')
                    ->where('order_id', $order->id)
                    ->where('vendor_id', $vendorId)
                    ->where('type', 'fee')
                    ->sum('cost') ?? 0;
                
                $vendorDiscounts = \Illuminate\Support\Facades\DB::table('order_extra_fees_discounts')
                    ->where('order_id', $order->id)
                    ->where('vendor_id', $vendorId)
                    ->where('type', 'discount')
                    ->sum('cost') ?? 0;
                
                // Get vendor's share of promo code and points from vendor_order_stages
                $vendorShares = \Illuminate\Support\Facades\DB::table('vendor_order_stages')
                    ->where('order_id', $order->id)
                    ->where('vendor_id', $vendorId)
                    ->select('promo_code_share', 'points_share')
                    ->first();
                
                $promoCodeShare = $vendorShares ? ($vendorShares->promo_code_share ?? 0) : 0;
                $pointsShare = $vendorShares ? ($vendorShares->points_share ?? 0) : 0;
                
                // Calculate proportional amounts based on refunded quantity
                $proportionRefunded = $totalOrderQtyForVendor > 0 ? $totalRefundedQty / $totalOrderQtyForVendor : 0;
                
                $proportionalFees = $vendorFees * $proportionRefunded;
                $proportionalDiscounts = $vendorDiscounts * $proportionRefunded;
                $proportionalPromoCode = $promoCodeShare * $proportionRefunded;
                $proportionalPoints = $pointsShare * $proportionRefunded;
                
                // Get vendor refund settings to determine who pays return shipping
                $vendorSettings = \Modules\Refund\app\Models\VendorRefundSetting::getForVendor($vendorId);
                $customerPaysReturnShipping = $data['customer_pays_return_shipping'] ?? $vendorSettings->customer_pays_return_shipping;
                
                // If vendor pays return shipping, set return_shipping_cost to 0
                // If customer pays, use the provided return_shipping_cost
                $returnShippingCost = $customerPaysReturnShipping 
                    ? ($data['return_shipping_cost'] ?? 0) 
                    : 0;
                
                // Create basic refund request
                $refundRequest = $this->create([
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'vendor_id' => $vendorId,
                    'country_id' => $order->country_id,
                    'status' => 'pending',
                    'reason' => $data['reason'],
                    'customer_notes' => $data['customer_notes'] ?? null,
                    'vendor_fees_amount' => $proportionalFees,
                    'vendor_discounts_amount' => $proportionalDiscounts,
                    'promo_code_amount' => $proportionalPromoCode,
                    'points_used' => $proportionalPoints,
                    'customer_pays_return_shipping' => $customerPaysReturnShipping,
                    'return_shipping_cost' => $returnShippingCost,
                ]);

                // Add items
                foreach ($vendorItems as $item) {
                    $orderProduct = $item['order_product'];
                    
                    // Calculate total tax for this order product
                    $totalTax = $orderProduct->taxes()->sum('amount') ?? 0;
                    
                    // Calculate unit price WITHOUT tax (order product price includes tax)
                    $priceWithTax = $orderProduct->price; // Total price for all quantities (with tax)
                    $priceWithoutTax = $priceWithTax - $totalTax; // Total price without tax
                    
                    $actualUnitPriceWithoutTax = $orderProduct->quantity > 0 
                        ? $priceWithoutTax / $orderProduct->quantity 
                        : $priceWithoutTax;
                    
                    // Calculate actual unit price WITH tax (for total_price calculation)
                    $actualUnitPriceWithTax = $orderProduct->quantity > 0 
                        ? $priceWithTax / $orderProduct->quantity 
                        : $priceWithTax;
                    
                    // Calculate proportional shipping cost for refunded quantity
                    // If vendor pays return shipping, don't refund original shipping cost
                    $shippingPerUnit = $customerPaysReturnShipping && $orderProduct->quantity > 0 
                        ? ($orderProduct->shipping_cost ?? 0) / $orderProduct->quantity 
                        : 0;
                    $refundShippingAmount = $shippingPerUnit * $item['quantity'];
                    
                    // Calculate proportional tax for refunded quantity
                    $taxPerUnit = $orderProduct->quantity > 0 
                        ? $totalTax / $orderProduct->quantity 
                        : 0;
                    $refundTaxAmount = $taxPerUnit * $item['quantity'];

                    \Modules\Refund\app\Models\RefundRequestItem::create([
                        'refund_request_id' => $refundRequest->id,
                        'order_product_id' => $orderProduct->id,
                        'vendor_id' => $vendorId,
                        'quantity' => $item['quantity'],
                        'unit_price' => $actualUnitPriceWithoutTax, // Store unit price WITHOUT tax
                        'total_price' => $actualUnitPriceWithTax * $item['quantity'], // Total WITH tax
                        'shipping_amount' => $refundShippingAmount,
                        'tax_amount' => $refundTaxAmount,
                        'discount_amount' => 0, // Will be calculated in calculateTotals if needed
                        'refund_amount' => ($actualUnitPriceWithTax * $item['quantity']) + $refundShippingAmount, // Don't add tax again
                        'reason' => $item['reason'],
                    ]);
                }

                // Calculate totals
                $refundRequest->calculateTotals();
                $createdRefunds[] = $refundRequest->fresh(['items.orderProduct', 'order', 'vendor']);
            }

            \Illuminate\Support\Facades\DB::commit();

            return $createdRefunds;

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

        $refund = $this->findById($id);

        // Prepare update data
        $updateData = [
            'status' => $data['status'],
            'vendor_notes' => $data['notes'] ?? null,
        ];

        // If status is refunded, set refunded_at timestamp
        if ($data['status'] === 'refunded') {
            $updateData['refunded_at'] = now();
        }

        // Update status
        $refund = $this->update($id, $updateData);

        // History record will be created by the observer

        return $refund;
    }

    public function getRefundWithRelations(int $refundId, array $relations = [])
    {
        return $this->model->with($relations)->findOrFail($refundId);
    }

    public function approveRefund(int $id)
    {
        $refund = $this->findById($id);
        
        if ($refund->status != 'pending') {
            throw new \Exception('Cannot approve this refund request');
        }
        
        $refund = $this->update($id, [
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // History record will be created by the observer

        return $refund;
    }

    public function cancelRefund(int $id, string $cancellationReason)
    {
        $refund = $this->findById($id);
        
        // Cannot cancel if already refunded
        if ($refund->status === 'refunded') {
            throw new \Exception(trans('refund::refund.messages.cannot_cancel_refunded'));
        }
        
        // Cannot cancel if already cancelled
        if ($refund->status === 'cancelled') {
            throw new \Exception(trans('refund::refund.messages.already_cancelled'));
        }
        
        // Can only cancel pending refunds
        if ($refund->status !== 'pending') {
            throw new \Exception(trans('refund::refund.messages.can_only_cancel_pending'));
        }

        $refund = $this->update($id, [
            'status' => 'cancelled',
            'vendor_notes' => $cancellationReason,
        ]);

        // History record will be created by the observer

        return $refund;
    }

    public function updateNotes(int $id, string $notes, bool $isAdmin = false)
    {
        $notesField = $isAdmin ? 'admin_notes' : 'vendor_notes';
        
        return $this->update($id, [
            $notesField => $notes,
        ]);
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
}

<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\app\Models\VendorOrderStage;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get vendors with their stages
        $vendorsWithStages = $this->getVendorsWithStages();
        
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_address' => $this->customer_address,
            'order_from' => $this->order_from,
            'payment_type' => $this->payment_type ?? '',
            'payment_reference' => $this->payment_reference ?? '',
            'payment_visa_status' => $this->payment_visa_status ?? '',
            'paymob_order_id' => $this->whenLoaded('payments', function() {
                // Get the paid payment first, otherwise get the latest one
                $paidPayment = $this->payments->firstWhere('status', 'paid');
                return $paidPayment?->paymob_order_id ?? $this->payments->first()?->paymob_order_id;
            }),
            'items_count' => $this->items_count,
            'total_product_price' => $this->calculateTotalProductPrice(),
            'total_tax' => (float) $this->total_tax,
            'total_fees' => (float) $this->total_fees,
            'total_discounts' => (float) $this->total_discounts,
            'shipping' => (float) $this->shipping,
            'points_used' => (float) ($this->points_used ?? 0),
            'points_cost' => (float) ($this->points_cost ?? 0),
            'points_discount_amount' => (float) ($this->points_cost ?? 0),
            'total_price' => $this->calculateTotalPrice(),
            'promo_code' => $this->customer_promo_code_title,
            'promo_discount' => $this->customer_promo_code_amount ? (float) $this->customer_promo_code_amount : 0,
            'refunded' => (float) ($this->refunded_amount ?? 0),
            'vendors_stages' => $vendorsWithStages,
            'products' => OrderProductResource::collection($this->whenLoaded('products')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
    
    /**
     * Get vendors with their stages for this order
     */
    private function getVendorsWithStages(): array
    {
        // Get unique vendors from products
        $vendors = $this->products->map(function ($orderProduct) {
            return $orderProduct->vendorProduct->vendor ?? null;
        })->filter()->unique('id');
        
        return $vendors->map(function ($vendor) {
            $vendorOrderStage = VendorOrderStage::where('order_id', $this->id)
                ->where('vendor_id', $vendor->id)
                ->with(['stage' => function($q) {
                    $q->withoutGlobalScopes();
                }])
                ->first();
            
            return [
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'vendor_logo' => $vendor->logo ? asset('storage/' . $vendor->logo->path) : null,
                'stage_id' => $vendorOrderStage?->stage_id,
                'stage_name' => $vendorOrderStage?->stage?->name ?? null,
                'stage_color' => $vendorOrderStage?->stage?->color ?? null,
                'stage_type' => $vendorOrderStage?->stage?->type ?? null,
                'promo_code_share' => (float) ($vendorOrderStage?->promo_code_share ?? 0),
                'points_share' => (float) ($vendorOrderStage?->points_share ?? 0),
            ];
        })->values()->toArray();
    }
    
    /**
     * Calculate total product price (price before tax - already includes quantity)
     */
    private function calculateTotalProductPrice(): float
    {
        $total = 0;
        
        foreach ($this->products as $product) {
            $priceAfterTax = (float) $product->price;
            $taxPercentage = $product->taxes->sum('percentage');
            $priceBeforeTax = $taxPercentage > 0 
                ? $priceAfterTax / (1 + ($taxPercentage / 100))
                : $priceAfterTax;
            
            $total += $priceBeforeTax;
        }
        
        return round($total, 2);
    }
    
    /**
     * Calculate total price (products with tax + shipping - discounts)
     */
    private function calculateTotalPrice(): float
    {
        $totalProductsWithTax = 0;
        
        foreach ($this->products as $product) {
            $totalProductsWithTax += (float) $product->price;
        }
        
        $shipping = (float) $this->shipping;
        $promoDiscount = (float) ($this->customer_promo_code_amount ?? 0);
        $pointsDiscount = (float) ($this->points_cost ?? 0);
        
        return round($totalProductsWithTax + $shipping - $promoDiscount - $pointsDiscount, 2);
    }
}

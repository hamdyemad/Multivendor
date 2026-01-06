<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\app\Models\VendorOrderStage;

class OrderDetailResource extends JsonResource
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
            'customer' => [
                'name' => $this->customer_name,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
                'address' => $this->customer_address,
            ],
            'location' => [
                'country_name' => $this->country?->name,
                'city_name' => $this->city?->name,
                'region_name' => $this->region?->name,
            ],
            'order_from' => $this->order_from,
            'payment_type' => $this->payment_type,
            'payment_visa_status' => $this->payment_visa_status ?? '',
            'payment_reference' => $this->payment_reference ?? '',
            'vendors_stages' => $vendorsWithStages,
            'products' => OrderProductResource::collection($this->whenLoaded('products')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'promo' => [
                'code' => $this->customer_promo_code_title,
                'discount_value' => $this->customer_promo_code_value ? (float) $this->customer_promo_code_value : null,
                'discount_type' => $this->customer_promo_code_type,
                'discount_amount' => $this->customer_promo_code_amount ? (float) $this->customer_promo_code_amount : null,
            ],
            'points' => [
                'used' => (float) ($this->points_used ?? 0),
                'cost' => (float) ($this->points_cost ?? 0),
            ],
            'pricing' => [
                'items_count' => $this->items_count,
                'total_product_price' => $this->calculateTotalProductPrice(),
                'total_tax' => (float) $this->total_tax,
                'shipping' => (float) $this->shipping,
                'extra_fees_discounts' => ExtraFeeDiscountResource::collection($this->whenLoaded('extraFeesDiscounts')),
                'total_price' => $this->calculateTotalPrice(),
            ],
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
     * Calculate total product price (price before tax × quantity)
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
            
            $total += $priceBeforeTax * $product->quantity;
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
            $totalProductsWithTax += (float) $product->price * $product->quantity;
        }
        
        $shipping = (float) $this->shipping;
        $promoDiscount = (float) ($this->customer_promo_code_amount ?? 0);
        $pointsDiscount = (float) ($this->points_cost ?? 0);
        
        return round($totalProductsWithTax + $shipping - $promoDiscount - $pointsDiscount, 2);
    }
}

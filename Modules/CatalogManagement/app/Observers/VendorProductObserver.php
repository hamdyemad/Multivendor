<?php

namespace Modules\CatalogManagement\app\Observers;

use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\Tax;
use Illuminate\Support\Facades\Log;

class VendorProductObserver
{
    /**
     * Track if taxes have been synced to prevent double sync
     */
    private static array $syncedProducts = [];

    /**
     * Handle the VendorProduct "created" event.
     */
    public function created(VendorProduct $vendorProduct): void
    {
        $this->syncActiveTaxes($vendorProduct, 'created');
    }

    /**
     * Handle the VendorProduct "updated" event.
     */
    public function updated(VendorProduct $vendorProduct): void
    {
        $this->syncActiveTaxes($vendorProduct, 'updated');
    }

    /**
     * Sync all active taxes to the vendor product.
     */
    protected function syncActiveTaxes(VendorProduct $vendorProduct, string $event): void
    {
        // Prevent double sync in the same request
        $key = $vendorProduct->id . '_' . $event;
        if (isset(self::$syncedProducts[$key])) {
            Log::info('VendorProductObserver: Skipping duplicate sync', [
                'vendor_product_id' => $vendorProduct->id,
                'event' => $event
            ]);
            return;
        }
        self::$syncedProducts[$key] = true;

        // Get all active taxes for the current country
        $activeTaxIds = Tax::where('is_active', true)->pluck('id')->toArray();
        
        Log::info('VendorProductObserver: Syncing taxes', [
            'vendor_product_id' => $vendorProduct->id,
            'event' => $event,
            'active_tax_ids' => $activeTaxIds
        ]);
        
        // Sync taxes (this will add missing taxes and keep existing ones)
        $vendorProduct->taxes()->sync($activeTaxIds);
        
        Log::info('VendorProductObserver: Taxes synced successfully', [
            'vendor_product_id' => $vendorProduct->id,
            'synced_tax_ids' => $activeTaxIds
        ]);
    }
}

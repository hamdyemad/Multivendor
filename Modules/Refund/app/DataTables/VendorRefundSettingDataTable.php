<?php

namespace Modules\Refund\app\DataTables;

use Modules\Refund\app\Models\VendorRefundSetting;
use Modules\Vendor\app\Models\Vendor;

class VendorRefundSettingDataTable
{
    /**
     * Get datatable data for vendor refund settings
     */
    public function getDataTable(array $data): array
    {
        $draw = $data['draw'];
        $start = $data['start'];
        $length = $data['length'];
        $searchValue = $data['search'];
        
        if (is_array($searchValue)) {
            $searchValue = $searchValue['value'] ?? '';
        }
        $filters = [
            'search' => $searchValue,
        ];
        // Build query
        $query = Vendor::with('refundSettings')->filter($filters);


        // Get total and filtered counts
        $totalRecords = Vendor::count();
        $filteredRecords = (clone $query)->count();

        // Calculate page number from start and length
        // DataTables sends start=0 for page 1, start=10 for page 2, etc.
        $page = ($start / $length) + 1;
        
        // Apply pagination
        $vendors = $query->paginate($length, ['*'], 'page', $page);

        // Format data for DataTables
        $tableData = [];
        $index = $start + 1;

        foreach ($vendors as $vendor) {
            $settings = $vendor->refundSettings ?? VendorRefundSetting::getForVendor($vendor->id);

            // Vendor name with logo (centered)
            $vendorName = '<div class="d-flex align-items-center justify-content-center">';
            if ($vendor->logo) {
                $vendorName .= '<img src="' . asset('storage/' . $vendor->logo->path) . '" alt="' . $vendor->name . '" class="rounded me-2" style="width: 32px; height: 32px; object-fit: cover;">';
            }
            $vendorName .= '<span class="fw-500">' . $vendor->name . '</span>';
            $vendorName .= '</div>';

            // Refund days input component
            $refundDays = view('refund::components.refund-days-input', [
                'vendorId' => $vendor->id,
                'days' => $settings->refund_processing_days
            ])->render();

            // Customer pays shipping switcher component
            $returnShipping = view('refund::components.customer-pays-shipping-switcher', [
                'vendorId' => $vendor->id,
                'vendorName' => $vendor->name,
                'checked' => $settings->customer_pays_return_shipping
            ])->render();

            $tableData[] = [
                'index' => $index++,
                'vendor_name' => $vendorName,
                'refund_days' => $refundDays,
                'return_shipping' => $returnShipping,
            ];
        }

        return [
            'dataPaginated' => $vendors,
            'data' => $tableData,
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords,
        ];
    }
}

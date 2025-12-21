<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Vendor\app\Models\Vendor;
use Modules\AreaSettings\app\Models\Region;

class StockSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:products.stock-setup')->only(['index', 'save']);
    }
    /**
     * Display stock setup page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = in_array($user->user_type_id, \App\Models\UserType::adminIds());

        // Get all vendors for admin dropdown
        $vendors = [];
        if ($isAdmin) {
            $vendors = Vendor::with('translations')
                ->where('active', 1)
                ->get()
                ->map(function ($vendor) {
                    return [
                        'id' => $vendor->id,
                        'name' => $vendor->name
                    ];
                });
        }

        // Get vendor based on user type or selection
        $selectedVendorId = null;
        $vendor = null;

        if ($isAdmin) {
            // Admin: Get vendor from request or use first vendor
            $selectedVendorId = $request->get('vendor_id');
            if ($selectedVendorId) {
                $vendor = Vendor::find($selectedVendorId);
            }
        } else {
            // Vendor: Get current user's vendor
            $vendor = $this->getCurrentVendor();
            if ($vendor) {
                $selectedVendorId = $vendor->id;
            }
        }

        // Initialize empty arrays
        $regions = collect([]);
        $selectedRegions = [];

        // Only load selected regions if vendor is selected (regions will be loaded via AJAX)
        if ($vendor) {
            // Get vendor's selected regions from pivot table
            $selectedRegions = DB::table('vendor_regions')
                ->where('vendor_id', $vendor->id)
                ->pluck('region_id')
                ->toArray();
        }

        // Check if this is an AJAX request (for getting selected regions only)
        if ($request->ajax() || $request->has('ajax') || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'selectedRegions' => $selectedRegions,
                'vendor' => $vendor ? [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'country_id' => $vendor->country_id
                ] : null
            ]);
        }

        return view('catalogmanagement::product.stock-setup', compact(
            'vendor',
            'regions',
            'selectedRegions',
            'isAdmin',
            'vendors',
            'selectedVendorId'
        ));
    }

    /**
     * Save vendor's selected regions
     */
    public function save(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'regions' => 'nullable|array',
            'regions.*' => 'exists:regions,id'
        ]);

        $user = Auth::user();
        $isAdmin = in_array($user->user_type_id, \App\Models\UserType::adminIds());

        // Get vendor - either from request (admin) or current user (vendor)
        if ($isAdmin) {
            $vendor = Vendor::find($request->vendor_id);
        } else {
            $vendor = $this->getCurrentVendor();

            // Verify vendor user is updating their own regions
            if (!$vendor || $vendor->id != $request->vendor_id) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::product.unauthorized_action')
                ], 403);
            }
        }

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.vendor_not_found')
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Delete existing region associations
            DB::table('vendor_regions')
                ->where('vendor_id', $vendor->id)
                ->delete();

            // Insert new associations
            if ($request->has('regions') && !empty($request->regions)) {
                $regionData = collect($request->regions)->map(function ($regionId) use ($vendor) {
                    return [
                        'vendor_id' => $vendor->id,
                        'region_id' => $regionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                DB::table('vendor_regions')->insert($regionData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.regions_saved_successfully')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.error_saving_regions'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current vendor based on user type
     */
    private function getCurrentVendor()
    {
        $user = Auth::user();

        // Check if user is vendor
        if (in_array($user->user_type_id, \App\Models\UserType::vendorIds())) {
            return $user->vendor;
        }

        // For admin, we would need to select a vendor (could be added later)
        return null;
    }
}

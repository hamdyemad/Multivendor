<?php

namespace Modules\Report\app\Repositories;

use Modules\Report\app\DTOs\ReportFilterDTO;
use Modules\Report\app\Interfaces\ReportRepositoryInterface;
use Modules\Customer\app\Models\Customer;
use Modules\Order\app\Models\Order;
use Modules\CatalogManagement\app\Models\Product;

class ReportRepository implements ReportRepositoryInterface
{
    /**
     * Get registered users report
     */
    public function getRegisteredUsersReport(ReportFilterDTO $filter): array
    {
        \Log::info('ReportRepository::getRegisteredUsersReport', ['filter' => (array)$filter]);

        $query = Customer::query();

        // Date range filter
        if ($filter->from) {
            $query->whereDate('created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('created_at', '<=', $filter->to);
        }

        // Status filter
        if ($filter->status) {
            $query->where('status', $filter->status === 'active' ? 1 : 0);
        }

        // Gender filter
        if ($filter->gender) {
            $query->where('gender', $filter->gender);
        }

        // Search filter
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('first_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('last_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('email', 'like', '%' . $filter->search . '%')
                  ->orWhere('phone', 'like', '%' . $filter->search . '%');
            });
        }

        $total = $query->count();
        \Log::info('ReportRepository::Total count', ['total' => $total, 'sql' => $query->toSql()]);

        // Get statistics and chart data for ALL matching records (not just current page)
        $allData = $query->get(['id', 'first_name', 'last_name', 'email', 'phone', 'gender', 'status', 'created_at']);

        // Calculate statistics
        $activeCount = $allData->where('status', 1)->count();
        $inactiveCount = $allData->where('status', 0)->count();
        $genderDistribution = [
            'male' => $allData->where('gender', 'male')->count(),
            'female' => $allData->where('gender', 'female')->count(),
            'other' => $allData->where('gender', 'other')->count(),
        ];

        // Calculate registration trend by date
        $registrationTrend = [];
        foreach ($allData as $customer) {
            if ($customer->created_at) {
                $date = $customer->created_at;
                $registrationTrend[$date] = ($registrationTrend[$date] ?? 0) + 1;
            }
        }
        ksort($registrationTrend);

        // Now paginate for table display
        $data = $query->paginate(
            perPage: $filter->per_page,
            page: $filter->page,
            columns: ['id', 'first_name', 'last_name', 'email', 'phone', 'gender', 'status', 'created_at']
        );

        // Add index to each item
        $items = $data->items();
        $startIndex = ($filter->page - 1) * $filter->per_page + 1;
        foreach ($items as $index => $item) {
            $item->index = $startIndex + $index;
        }

        \Log::info('ReportRepository::Data retrieved', ['count' => count($items), 'items' => $items]);

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $items,
            'statistics' => [
                'active' => $activeCount,
                'inactive' => $inactiveCount,
                'total_filtered' => $total,
            ],
            'gender_distribution' => $genderDistribution,
            'registration_trend' => $registrationTrend,
        ];
    }

    /**
     * Get area users report (Customers by current country_code and selected city)
     */
    public function getAreaUsersReport(ReportFilterDTO $filter): array
    {
        // Get current country from session
        $countryCode = session('country_code');
        $countryId = $countryCode
            ? \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id')
            : null;

        // Start with customers query
        $query = Customer::query();

        // If city is selected, filter by city/area
        if ($filter->city_id) {
            $query->where('city_id', $filter->city_id);
        } elseif ($countryId) {
            // Otherwise filter by country
            $query->where('country_id', $countryId);
        }

        // Date range filter
        if ($filter->from) {
            $query->whereDate('created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('created_at', '<=', $filter->to);
        }

        // Status filter
        if ($filter->status) {
            $query->where('status', $filter->status === 'active' ? 1 : 0);
        }

        // Search filter
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('first_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('last_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('email', 'like', '%' . $filter->search . '%')
                  ->orWhere('phone', 'like', '%' . $filter->search . '%');
            });
        }

        $total = $query->count();

        // Get all data for statistics and charts
        $allData = $query->with(['city'])->get();

        // Calculate statistics
        $activeCount = $allData->where('status', 1)->count();
        $inactiveCount = $allData->where('status', 0)->count();

        // Calculate registration trend by date
        $registrationTrend = [];
        foreach ($allData as $customer) {
            if ($customer->created_at) {
                $date = $customer->created_at;
                $registrationTrend[$date] = ($registrationTrend[$date] ?? 0) + 1;
            }
        }
        ksort($registrationTrend);

        // Calculate city distribution
        $cityDistribution = [];
        foreach ($allData as $customer) {
            $cityName = $customer->city?->name ?? 'Unknown';
            $cityDistribution[$cityName] = ($cityDistribution[$cityName] ?? 0) + 1;
        }
        arsort($cityDistribution); // Sort by count descending

        // Now paginate for table display
        $data = $query->with(['city'])
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page,
                columns: ['id', 'first_name', 'last_name', 'email', 'phone', 'status', 'created_at', 'city_id']
            );

        // Add index and city name to each item
        $items = $data->items();
        $startIndex = ($filter->page - 1) * $filter->per_page + 1;
        foreach ($items as $index => $item) {
            $item->index = $startIndex + $index;
            $item->name = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: 'N/A';
            $item->city_name = $item->city?->name ?? 'N/A';
        }

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $items,
            'statistics' => [
                'active' => $activeCount,
                'inactive' => $inactiveCount,
                'total_filtered' => $total,
            ],
            'registration_trend' => $registrationTrend,
            'city_distribution' => $cityDistribution,
        ];
    }

    /**
     * Get orders report
     */
    public function getOrdersReport(ReportFilterDTO $filter): array
    {
        $isVendor = isVendor();
        $vendorId = null;
        if ($isVendor) {
            $vendor = auth()->user()->vendor;
            $vendorId = $vendor ? $vendor->id : null;
        }

        $query = Order::withoutCountryFilter();

        // Apply country filter manually with qualified table name
        $countryCode = session('country_code') ?? request('country_code') ?? config('app.default_country_code');
        if ($countryCode) {
            $countryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
            if ($countryId) {
                $query->where('orders.country_id', $countryId);
            }
        }

        // If user is vendor, filter to show only their orders
        if ($isVendor && $vendorId) {
            $query->whereHas('products', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        } elseif ($isVendor) {
            // If vendor user but no vendor record, return empty results
            $query->whereRaw('1 = 0');
        }

        // Date range filter
        if ($filter->from) {
            $query->whereDate('orders.created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('orders.created_at', '<=', $filter->to);
        }

        // Stage filter - for vendors, filter by vendor_order_stages
        if ($filter->type) {
            if ($isVendor && $vendorId) {
                $query->whereHas('vendorStages', function ($q) use ($filter, $vendorId) {
                    $q->where('vendor_id', $vendorId)
                      ->where('stage_id', $filter->type);
                });
            } else {
                $query->where('stage_id', $filter->type);
            }
        }

        // Search filter (by order number or customer name)
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('order_number', 'like', '%' . $filter->search . '%')
                  ->orWhereHas('customer', function ($customerQ) use ($filter) {
                      $customerQ->where('first_name', 'like', '%' . $filter->search . '%')
                                ->orWhere('last_name', 'like', '%' . $filter->search . '%')
                                ->orWhere('email', 'like', '%' . $filter->search . '%')
                                ->orWhere('phone', 'like', '%' . $filter->search . '%');
                  });
            });
        }

        $total = $query->count();

        // Get all data for charts before pagination (without ordering to avoid GROUP BY issues)
        $allData = clone $query;

        // Calculate stage distribution based on user type
        if ($isVendor && $vendorId) {
            // For vendors, use vendor_order_stages
            $stageDistribution = \DB::table('vendor_order_stages')
                ->join('order_stages', 'vendor_order_stages.stage_id', '=', 'order_stages.id')
                ->whereIn('vendor_order_stages.order_id', $allData->clone()->pluck('orders.id'))
                ->where('vendor_order_stages.vendor_id', $vendorId)
                ->select('order_stages.type', \DB::raw('COUNT(*) as count'))
                ->groupBy('order_stages.type')
                ->get()
                ->pluck('count', 'type')
                ->toArray();
        } else {
            $stageDistribution = $allData->clone()
                ->join('order_stages', 'orders.stage_id', '=', 'order_stages.id')
                ->select('order_stages.type', \DB::raw('COUNT(orders.id) as count'))
                ->groupBy('order_stages.type')
                ->get()
                ->pluck('count', 'type')
                ->toArray();
        }

        // Calculate orders trend (by date) - use raw query to avoid GROUP BY issues
        $orderIds = $allData->clone()->pluck('orders.id');
        $ordersTrend = \DB::table('orders')
            ->whereIn('id', $orderIds)
            ->selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Calculate completed and pending counts based on user type
        if ($isVendor && $vendorId) {
            $completedCount = \DB::table('vendor_order_stages')
                ->join('order_stages', 'vendor_order_stages.stage_id', '=', 'order_stages.id')
                ->whereIn('vendor_order_stages.order_id', $orderIds)
                ->where('vendor_order_stages.vendor_id', $vendorId)
                ->where('order_stages.type', 'deliver')
                ->count();

            $pendingCount = \DB::table('vendor_order_stages')
                ->join('order_stages', 'vendor_order_stages.stage_id', '=', 'order_stages.id')
                ->whereIn('vendor_order_stages.order_id', $orderIds)
                ->where('vendor_order_stages.vendor_id', $vendorId)
                ->whereIn('order_stages.type', ['new', 'in_progress'])
                ->count();
        } else {
            $completedCount = \DB::table('orders')
                ->join('order_stages', 'orders.stage_id', '=', 'order_stages.id')
                ->whereIn('orders.id', $orderIds)
                ->where('order_stages.type', 'deliver')
                ->count();

            $pendingCount = \DB::table('orders')
                ->join('order_stages', 'orders.stage_id', '=', 'order_stages.id')
                ->whereIn('orders.id', $orderIds)
                ->whereIn('order_stages.type', ['new', 'in_progress'])
                ->count();
        }

        // Now add ordering for pagination
        $query->orderBy('orders.created_at', 'desc');

        // Load relationships based on user type
        $relationships = ['customer', 'stage', 'products'];
        if ($isVendor && $vendorId) {
            $relationships[] = 'vendorStages.stage';
        }

        $data = $query->with($relationships)
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page
            );

        // Transform data for frontend
        $items = $data->items();
        $transformedItems = [];
        foreach ($items as $index => $order) {
            $customerName = 'N/A';
            if ($order->customer) {
                $customerName = trim(($order->customer->first_name ?? '') . ' ' . ($order->customer->last_name ?? '')) ?: 'N/A';
            }
            
            // Calculate total and get stage based on user type
            $orderTotal = $order->total_price;
            $stageName = $order->stage ? $order->stage->name : 'N/A';
            $stageType = $order->stage ? $order->stage->type : null;
            
            if ($isVendor && $vendorId) {
                // For vendors, show only their products total + shipping
                $vendorProducts = $order->products->where('vendor_id', $vendorId);
                // Note: price field already contains total (unit_price * quantity)
                $productsTotal = $vendorProducts->sum('price');
                $shippingTotal = $vendorProducts->sum('shipping_cost');
                $orderTotal = $productsTotal + $shippingTotal;
                
                // Get vendor's stage from vendor_order_stages
                $vendorStage = $order->vendorStages->where('vendor_id', $vendorId)->first();
                if ($vendorStage && $vendorStage->stage) {
                    $stageName = $vendorStage->stage->name;
                    $stageType = $vendorStage->stage->type;
                }
            }
            
            $transformedItems[] = [
                'index' => ($filter->page - 1) * $filter->per_page + $index + 1,
                'order_number' => $order->order_number,
                'customer_name' => $customerName,
                'stage' => $stageName,
                'stage_type' => $stageType,
                'total' => $orderTotal,
                'created_at' => $order->created_at,
            ];
        }

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $transformedItems,
            'statistics' => [
                'stage_distribution' => $stageDistribution,
                'orders_trend' => $ordersTrend,
                'completed' => $completedCount,
                'pending' => $pendingCount,
                'total_filtered' => $total,
            ],
        ];
    }

    /**
     * Get products report
     */
    public function getProductsReport(ReportFilterDTO $filter): array
    {
        $query = \Modules\CatalogManagement\app\Models\VendorProduct::withoutCountryFilter();

        // Apply country filter manually
        $countryCode = session('country_code') ?? request('country_code') ?? config('app.default_country_code');
        if ($countryCode) {
            $countryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
            if ($countryId) {
                $query->where('vendor_products.country_id', $countryId);
            }
        }

        // If user is vendor, filter to show only their products
        if (isVendor()) {
            $vendor = auth()->user()->vendor;
            if ($vendor) {
                $query->where('vendor_id', $vendor->id);
            } else {
                // If vendor user but no vendor record, return empty results
                $query->whereRaw('1 = 0');
            }
        }

        // Date range filter
        if ($filter->from) {
            $query->whereDate('created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('created_at', '<=', $filter->to);
        }

        // Status filter
        if ($filter->status) {
            $query->where('status', $filter->status);
        }

        // Category filter
        if ($filter->category) {
            $query->whereHas('product', function ($q) use ($filter) {
                $q->where('category_id', $filter->category);
            });
        }

        // Vendor filter
        if ($filter->vendor) {
            $query->where('vendor_id', $filter->vendor);
        }

        // Search filter
        if ($filter->search) {
            $searchTerm = strtolower($filter->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(sku) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(status) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereHas('product', function ($productQ) use ($searchTerm) {
                      $productQ->whereHas('translations', function ($transQ) use ($searchTerm) {
                          $transQ->whereRaw('LOWER(lang_value) LIKE ?', ['%' . $searchTerm . '%'])
                                 ->where('lang_key', 'title');
                      });
                  });
            });
        }

        $total = $query->count();

        // Get all data for statistics
        $allData = clone $query;

        // Calculate status distribution
        $statusDistribution = $allData->clone()
            ->select('status', \DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Calculate products trend (by date)
        $productsTrend = $allData->clone()
            ->selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Calculate active/inactive counts
        $activeCount = $allData->clone()->where('is_active', 1)->count();
        $inactiveCount = $allData->clone()->where('is_active', 0)->count();

        $data = $query->with(['product.category', 'product.mainImage', 'vendor.logo'])
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page
            );

        // Transform data for frontend
        $items = $data->items();
        $transformedItems = [];
        foreach ($items as $index => $vendorProduct) {
            $productImage = null;
            if ($vendorProduct->product && $vendorProduct->product->mainImage) {
                $productImage = asset('storage/' . $vendorProduct->product->mainImage->path);
            }
            
            $vendorImage = null;
            if ($vendorProduct->vendor && $vendorProduct->vendor->logo) {
                $vendorImage = asset('storage/' . $vendorProduct->vendor->logo->path);
            }
            
            $transformedItems[] = [
                'index' => ($filter->page - 1) * $filter->per_page + $index + 1,
                'name' => $vendorProduct->product ? $vendorProduct->product->name : 'N/A',
                'product_image' => $productImage,
                'sku' => $vendorProduct->sku,
                'category' => $vendorProduct->product?->category ? $vendorProduct->product->category->name : 'N/A',
                'vendor' => $vendorProduct->vendor ? $vendorProduct->vendor->name : 'N/A',
                'vendor_image' => $vendorImage,
                'status' => $vendorProduct->status,
                'is_active' => $vendorProduct->is_active,
                'sales' => $vendorProduct->sales,
                'views' => $vendorProduct->views,
                'created_at' => $vendorProduct->created_at,
            ];
        }

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $transformedItems,
            'statistics' => [
                'status_distribution' => $statusDistribution,
                'products_trend' => $productsTrend,
                'active' => $activeCount,
                'inactive' => $inactiveCount,
                'total_filtered' => $total,
            ],
        ];
    }

    /**
     * Get points report
     */
    public function getPointsReport(ReportFilterDTO $filter): array
    {
        $query = \Modules\SystemSetting\app\Models\UserPoints::query()
            ->whereHas('user'); // Only include user_points with valid users

        // Apply country filter through user relationship (only if users have country_id)
        $countryCode = session('country_code') ?? request('country_code') ?? config('app.default_country_code');
        if ($countryCode) {
            $countryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
            if ($countryId) {
                $query->whereHas('user', function($q) use ($countryId) {
                    $q->where(function($subQ) use ($countryId) {
                        $subQ->where('country_id', $countryId)
                             ->orWhereNull('country_id'); // Include users without country
                    });
                });
            }
        }

        // Date range filter
        if ($filter->from) {
            $query->whereDate('created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('created_at', '<=', $filter->to);
        }

        // Search filter
        if ($filter->search) {
            $query->whereHas('user', function ($q) use ($filter) {
                $q->where('email', 'like', '%' . $filter->search . '%');
            });
        }

        $total = $query->count();

        // Get all data for statistics
        $allData = clone $query;

        // Calculate points distribution
        $pointsDistribution = [
            'high' => $allData->clone()->where('total_points', '>=', 1000)->count(),
            'medium' => $allData->clone()->whereBetween('total_points', [100, 999])->count(),
            'low' => $allData->clone()->where('total_points', '<', 100)->count(),
        ];

        // Calculate points trend (by date)
        $pointsTrend = $allData->clone()
            ->selectRaw('DATE(created_at) as date, SUM(total_points) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Calculate totals
        $totalPoints = $allData->clone()->sum('total_points');
        $totalEarned = $allData->clone()->sum('earned_points');
        $totalRedeemed = $allData->clone()->sum('redeemed_points');

        $data = $query->with('user')
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page
            );

        // Transform data for frontend
        $items = $data->items();
        $transformedItems = [];
        foreach ($items as $index => $userPoint) {
            $transformedItems[] = [
                'index' => ($filter->page - 1) * $filter->per_page + $index + 1,
                'user_name' => $userPoint->user?->name ?: ($userPoint->user?->email ?? 'N/A'),
                'email' => $userPoint->user?->email ?? 'N/A',
                'total_points' => $userPoint->total_points,
                'earned_points' => $userPoint->earned_points,
                'redeemed_points' => $userPoint->redeemed_points,
                'points_spent' => $userPoint->redeemed_points, // Same as redeemed
                'remaining_points' => $userPoint->total_points, // Current total points
                'created_at' => $userPoint->created_at,
            ];
        }

        // Calculate totals for all users (not just filtered)
        $allUsersQuery = \Modules\SystemSetting\app\Models\UserPoints::query()->whereHas('user');

        $totalUsers = $allUsersQuery->count();
        $totalEarned = $allUsersQuery->sum('earned_points');
        $totalRedeemed = $allUsersQuery->sum('redeemed_points');
        $avgPoints = $totalUsers > 0 ? $totalEarned / $totalUsers : 0;

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $transformedItems,
            'statistics' => [
                'total_users' => $totalUsers,
                'total_earned' => $totalEarned,
                'total_redeemed' => $totalRedeemed,
                'avg_points' => round($avgPoints, 2),
                'points_distribution' => $pointsDistribution,
                'points_trend' => $pointsTrend,
            ]
        ];
    }
}

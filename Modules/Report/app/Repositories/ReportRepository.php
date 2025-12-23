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
            $item->name = $item->first_name . ' ' . $item->last_name;
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
        $query = Order::withoutCountryFilter();

        // Apply country filter manually with qualified table name
        $countryCode = session('country_code') ?? request('country_code') ?? config('app.default_country_code');
        if ($countryCode) {
            $countryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
            if ($countryId) {
                $query->where('orders.country_id', $countryId);
            }
        }

        // Date range filter
        if ($filter->from) {
            $query->whereDate('created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('created_at', '<=', $filter->to);
        }

        // Stage filter - use stage_id instead of status
        if ($filter->type) {
            $query->where('stage_id', $filter->type);
        }

        // Search filter (by order number or customer name)
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('order_number', 'like', '%' . $filter->search . '%')
                  ->orWhereHas('customer', function ($customerQ) use ($filter) {
                      $customerQ->where('first_name', 'like', '%' . $filter->search . '%')
                               ->orWhere('last_name', 'like', '%' . $filter->search . '%');
                  });
            });
        }

        $total = $query->count();

        // Get all data for charts before pagination
        $allData = clone $query;
        
        // Calculate stage distribution
        $stageDistribution = $allData->clone()
            ->join('order_stages', 'orders.stage_id', '=', 'order_stages.id')
            ->leftJoin('translations as stage_trans', function($join) {
                $join->on('order_stages.id', '=', 'stage_trans.translatable_id')
                     ->where('stage_trans.translatable_type', '=', 'Modules\\Order\\app\\Models\\OrderStage')
                     ->where('stage_trans.lang_key', '=', 'name');
            })
            ->leftJoin('languages', function($join) {
                $join->on('stage_trans.lang_id', '=', 'languages.id')
                     ->where('languages.code', '=', app()->getLocale());
            })
            ->select(
                'order_stages.type',
                \DB::raw('COALESCE(stage_trans.lang_value, order_stages.slug) as stage_name'), 
                \DB::raw('COUNT(orders.id) as count')
            )
            ->groupBy('order_stages.type', 'order_stages.id', 'stage_trans.lang_value', 'order_stages.slug')
            ->get()
            ->groupBy('type')
            ->map(function($stages) {
                return $stages->sum('count');
            })
            ->toArray();

        // Calculate orders trend (by date)
        $ordersTrend = $allData->clone()
            ->selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Calculate completed and pending counts
        $completedCount = $allData->clone()
            ->join('order_stages', 'orders.stage_id', '=', 'order_stages.id')
            ->where('order_stages.type', 'deliver')
            ->count();

        $pendingCount = $allData->clone()
            ->join('order_stages', 'orders.stage_id', '=', 'order_stages.id')
            ->whereIn('order_stages.type', ['new', 'in_progress'])
            ->count();

        $data = $query->with(['customer', 'stage'])
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page
            );

        // Transform data for frontend
        $items = $data->items();
        $transformedItems = [];
        foreach ($items as $index => $order) {
            $transformedItems[] = [
                'index' => ($filter->page - 1) * $filter->per_page + $index + 1,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer ? ($order->customer->first_name . ' ' . $order->customer->last_name) : 'N/A',
                'stage' => $order->stage ? $order->stage->name : 'N/A',
                'stage_type' => $order->stage ? $order->stage->type : null,
                'total' => $order->total_price,
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

        $data = $query->with(['product.category', 'vendor'])
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page
            );

        // Transform data for frontend
        $items = $data->items();
        $transformedItems = [];
        foreach ($items as $index => $vendorProduct) {
            $transformedItems[] = [
                'index' => ($filter->page - 1) * $filter->per_page + $index + 1,
                'name' => $vendorProduct->product ? $vendorProduct->product->name : 'N/A',
                'sku' => $vendorProduct->sku,
                'category' => $vendorProduct->product?->category ? $vendorProduct->product->category->name : 'N/A',
                'vendor' => $vendorProduct->vendor ? $vendorProduct->vendor->name : 'N/A',
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
        $query = \Modules\SystemSetting\app\Models\UserPoints::withoutCountryFilter();

        // Apply country filter manually
        $countryCode = session('country_code') ?? request('country_code') ?? config('app.default_country_code');
        if ($countryCode) {
            $countryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
            if ($countryId) {
                $query->whereHas('user', function($q) use ($countryId) {
                    $q->where('country_id', $countryId);
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
                $q->where('first_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('last_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('email', 'like', '%' . $filter->search . '%');
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
                'user_name' => $userPoint->user ? ($userPoint->user->first_name . ' ' . $userPoint->user->last_name) : 'N/A',
                'email' => $userPoint->user?->email ?? 'N/A',
                'total_points' => $userPoint->total_points,
                'earned_points' => $userPoint->earned_points,
                'redeemed_points' => $userPoint->redeemed_points,
                'created_at' => $userPoint->created_at,
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
                'points_distribution' => $pointsDistribution,
                'points_trend' => $pointsTrend,
                'total_points' => $totalPoints,
                'total_earned' => $totalEarned,
                'total_redeemed' => $totalRedeemed,
                'total_filtered' => $total,
            ],
        ];
    }
}

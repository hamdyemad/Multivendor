<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Modules\AreaSettings\app\Models\Country;
use Modules\CatalogManagement\app\Models\Product;
use Modules\Customer\app\Models\Customer;
use Modules\Order\app\Models\Order;
use Modules\Vendor\app\Models\Vendor;

class DashboardService
{
    public function getDashboardData($countryCode)
    {
        $country = Country::where('code', $countryCode)->firstOrFail();
        $countryId = $country->id;

        $stats = $this->getStats($countryId);
        $salesChart = $this->getSalesChartData($countryId);
        $latestOrders = $this->getLatestOrders($countryId);
        $topSellingProducts = $this->getTopSellingProducts($countryId);
        $topVendors = $this->getTopVendors($countryId);
        $bestCustomers = $this->getBestCustomers($countryId);

        return [
            'stats' => $stats,
            'salesChart' => $salesChart,
            'latestOrders' => $latestOrders,
            'topSellingProducts' => $topSellingProducts,
            'topVendors' => $topVendors,
            'bestCustomers' => $bestCustomers,
        ];
    }

    private function getStats($countryId)
    {
        $today = Carbon::today();

        return [
            'total_sales' => Order::where('country_id', $countryId)->sum('total_price'),
            'total_orders' => Order::where('country_id', $countryId)->count(),
            'total_products' => Product::where('country_id', $countryId)->count(),
            'total_customers' => Customer::where('country_id', $countryId)->count(),
            'total_vendors' => Vendor::where('country_id', $countryId)->count(),
            'today_sales' => Order::where('country_id', $countryId)->whereDate('created_at', $today)->sum('total_price'),
            'today_orders' => Order::where('country_id', $countryId)->whereDate('created_at', $today)->count(),
        ];
    }

    private function getSalesChartData($countryId)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subYear();
        $sales = Order::where('country_id', $countryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as total_sales')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $sales->pluck('month');
        $data = $sales->pluck('total_sales');

        return ['labels' => $labels, 'data' => $data];
    }

    private function getLatestOrders($countryId, $limit = 10)
    {
        return Order::where('country_id', $countryId)->latest()->take($limit)->get();
    }

    private function getTopSellingProducts($countryId, $limit = 5)
    {
        return Product::where('country_id', $countryId)
            ->withCount('variants') // Assuming variants represent sales
            ->orderBy('variants_count', 'desc')
            ->take($limit)
            ->get();
    }

    private function getTopVendors($countryId, $limit = 5)
    {
        return Vendor::where('country_id', $countryId)
            ->withCount('total_orders')
            ->orderBy('total_orders_count', 'desc')
            ->take($limit)
            ->get();
    }

    private function getBestCustomers($countryId, $limit = 5)
    {
        return Customer::where('country_id', $countryId)
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take($limit)
            ->get();
    }
}

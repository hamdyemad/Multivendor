<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\app\Models\AccountingEntry;
use Modules\Accounting\app\Models\Expense;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\Tax;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\Promocode;
use Modules\CatalogManagement\app\Models\Review;
use Modules\Customer\app\Models\Customer;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;
use Modules\Vendor\app\Models\Vendor;
use Modules\Vendor\app\Models\VendorRequest;
use Modules\SystemSetting\app\Models\Ad;
use Modules\SystemSetting\app\Models\Message;

class DashboardService
{
    protected $vendorId = null;
    protected $isVendor = false;

    protected function initVendorCheck()
    {
        // Check if authenticated user is a vendor
        if (Auth::check() && Auth::user()->isVendor()) {
            $this->isVendor = true;
            $this->vendorId = Auth::user()->vendor?->id;
        }
    }

    public function getDashboardData($countryCode)
    {
        // Initialize vendor check here instead of constructor
        $this->initVendorCheck();

        $stats = $this->getStats();
        $salesChart = $this->getSalesChartData();
        $earningsChart = $this->getEarningsChartData();
        $latestOrders = $this->getLatestOrders();
        $topSellingProducts = $this->getTopSellingProducts();
        $topVendors = $this->isVendor ? [] : $this->getTopVendors(); // Hide for vendors
        $bestCustomers = $this->getBestCustomers();
        $ordersOverview = $this->getOrdersOverview();
        $salesOverview = $this->getSalesOverview();
        $incomeExpense = $this->getIncomeExpenseData();
        $recentActivities = $this->isVendor ? [] : $this->getRecentActivities(); // Hide for vendors

        return [
            'stats' => $stats,
            'salesChart' => $salesChart,
            'earningsChart' => $earningsChart,
            'latestOrders' => $latestOrders,
            'topSellingProducts' => $topSellingProducts,
            'topVendors' => $topVendors,
            'bestCustomers' => $bestCustomers,
            'ordersOverview' => $ordersOverview,
            'salesOverview' => $salesOverview,
            'incomeExpense' => $incomeExpense,
            'recentActivities' => $recentActivities,
            'isVendor' => $this->isVendor,
        ];
    }

    private function getStats()
    {
        $today = Carbon::today();

        // If vendor, return vendor-specific stats
        if ($this->isVendor && $this->vendorId) {
            return $this->getVendorStats($today);
        }

        // Get admins count based on user type (same as admin management page)
        $adminsQuery = User::whereIn('user_type_id', [UserType::ADMIN_TYPE, UserType::VENDOR_USER_TYPE]);

        return [
            // Users & Admins
            'total_admins' => $adminsQuery->count(),
            'vendor_users' => User::where('user_type_id', UserType::VENDOR_USER_TYPE)->count(),

            // Vendors
            'total_vendors' => Vendor::count(),
            'become_vendor_requests' => VendorRequest::where('status', 'pending')->count(),
            'accepted_vendors' => Vendor::where('active', 1)->count(),
            'rejected_vendors' => Vendor::where('active', 0)->count(),
            'new_vendors' => Vendor::whereDate('created_at', '>=', Carbon::now()->subDays(30))->count(),

            // Customers
            'total_customers' => Customer::count(),
            'total_male_users' => Customer::where('gender', 'male')->count(),
            'total_female_users' => Customer::where('gender', 'female')->count(),

            // Roles
            'admins_total_roles' => Role::where('type', 'admin')->count(),
            'vendor_users_total_roles' => Role::where('type', 'vendor')->count(),

            // Products & Stock
            'total_products' => Product::count(),
            'instock' => VendorProduct::whereHas('variants', function($q) {
                $q->whereHas('stocks', function($sq) {
                    $sq->where('quantity', '>', 0);
                });
            })->count(),
            'out_of_stock' => VendorProduct::where(function($q) {
                $q->whereDoesntHave('variants')
                  ->orWhereDoesntHave('variants.stocks', function($sq) {
                      $sq->where('quantity', '>', 0);
                  });
            })->count(),

            // Orders
            'total_orders' => Order::count(),
            'total_sales' => Order::whereHas('stage', function($q) {
                $q->where('type', 'deliver');
            })->sum('total_price'),
            'today_sales' => Order::whereHas('stage', function($q) {
                $q->where('type', 'deliver');
            })->whereDate('created_at', $today)->sum('total_price'),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'total_order_stages' => OrderStage::withoutCountryFilter()->count(),

            // Taxes
            'total_taxes' => Tax::count(),

            // Messages
            'total_messages' => Message::count(),

            // Promo Codes
            'promocodes' => Promocode::count(),

            // Area Settings
            'country' => Country::count(),
            'city' => City::count(),
            'region' => Region::count(),
            'subregion' => 0,

            // Offers
            'total_offers' => 0,

            // Reviews
            'all_products_reviews' => Review::count(),
            'accept_products_reviews' => Review::where('status', 'approved')->count(),
            'reject_products_reviews' => Review::where('status', 'rejected')->count(),

            // Advertisements
            'total_advertisments' => Ad::count(),
        ];
    }

    private function getVendorStats($today)
    {
        $vendorId = $this->vendorId;

        // Get delivered stage without country filter
        $deliveredStage = OrderStage::withoutCountryFilter()->where('type', 'deliver')->first();

        // Get order products for this vendor
        $orderProductsQuery = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId);

        // Get delivered order products using stage_id
        $deliveredOrderProducts = $deliveredStage
            ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($deliveredStage) {
                    $q->where('stage_id', $deliveredStage->id);
                })
            : \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)->whereRaw('1=0'); // Empty query if no stage

        return [
            // Products & Stock (vendor specific)
            'total_products' => VendorProduct::where('vendor_id', $vendorId)->count(),
            'instock' => VendorProduct::where('vendor_id', $vendorId)->whereHas('variants', function($q) {
                $q->whereHas('stocks', function($sq) {
                    $sq->where('quantity', '>', 0);
                });
            })->count(),
            'out_of_stock' => VendorProduct::where('vendor_id', $vendorId)->where(function($q) {
                $q->whereDoesntHave('variants')
                  ->orWhereDoesntHave('variants.stocks', function($sq) {
                      $sq->where('quantity', '>', 0);
                  });
            })->count(),

            // Orders (vendor specific - based on order products)
            'total_orders' => $orderProductsQuery->distinct('order_id')->count('order_id'),
            'total_sales' => (clone $deliveredOrderProducts)->sum(\DB::raw('price * quantity')),
            'today_sales' => (clone $deliveredOrderProducts)->whereHas('order', function($q) use ($today) {
                $q->whereDate('created_at', $today);
            })->sum(\DB::raw('price * quantity')),
            'today_orders' => \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($today) {
                    $q->whereDate('created_at', $today);
                })->distinct('order_id')->count('order_id'),

            // Reviews (vendor specific)
            'all_products_reviews' => Review::whereHas('vendorProduct', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->count(),
            'accept_products_reviews' => Review::where('status', 'approved')->whereHas('vendorProduct', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->count(),
            'reject_products_reviews' => Review::where('status', 'rejected')->whereHas('vendorProduct', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->count(),

            // Hide admin-only stats for vendors
            'total_admins' => 0,
            'vendor_users' => 0,
            'total_vendors' => 0,
            'become_vendor_requests' => 0,
            'accepted_vendors' => 0,
            'rejected_vendors' => 0,
            'new_vendors' => 0,
            'total_customers' => 0,
            'total_male_users' => 0,
            'total_female_users' => 0,
            'admins_total_roles' => 0,
            'vendor_users_total_roles' => 0,
            'total_order_stages' => 0,
            'total_taxes' => 0,
            'total_messages' => 0,
            'promocodes' => 0,
            'country' => 0,
            'city' => 0,
            'region' => 0,
            'subregion' => 0,
            'total_offers' => 0,
            'total_advertisments' => 0,
        ];
    }

    private function getSalesOverview()
    {
        $startOfYear = Carbon::now()->startOfYear();
        $newStage = OrderStage::withoutCountryFilter()->where('type', 'new')->first();
        $inProgressStage = OrderStage::withoutCountryFilter()->where('type', 'in_progress')->first();

        $totalExpenses = Expense::sum('amount');

        $totalIncome = AccountingEntry::where('type', 'income')->sum('amount');
        $ytdIncome = AccountingEntry::where('type', 'income')->whereDate('created_at', '>=', $startOfYear)->sum('amount');
        if ($this->isVendor && $this->vendorId) {
            $revenueYtd = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                ->whereHas('order', function($q) use ($startOfYear) {
                    $q->whereDate('created_at', '>=', $startOfYear);
                })->sum((DB::raw('price * quantity')));

            $newOrdersCount = $newStage
                ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                    ->whereHas('order', function($q) use ($newStage) {
                        $q->where('stage_id', $newStage->id);
                    })->distinct('order_id')->count('order_id')
                : 0;

            $inProgressOrdersCount = $inProgressStage
                ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                    ->whereHas('order', function($q) use ($inProgressStage) {
                        $q->where('stage_id', $inProgressStage->id);
                    })->distinct('order_id')->count('order_id')
                : 0;
        } else {
            $revenueYtd = Order::whereDate('created_at', '>=', $startOfYear)->sum('total_price');

            $newOrdersCount = $newStage ? Order::where('stage_id', $newStage->id)->count() : 0;

            $inProgressOrdersCount = $inProgressStage ? Order::where('stage_id', $inProgressStage->id)->count() : 0;
        }

        return [
            'total_expenses' => $totalExpenses,
            'total_income' => $totalIncome,
            'net_profit_ytd' => $ytdIncome - $totalExpenses,
            'revenue_ytd' => $revenueYtd,
            'new_orders_count' => $newOrdersCount,
            'in_progress_orders_count' => $inProgressOrdersCount,
        ];
    }

    private function getOrdersOverview()
    {
        // Get all order stages dynamically
        $stages = OrderStage::withoutCountryFilter()->orderBy('id', 'asc')->get();

        $overview = [];

        foreach ($stages as $stage) {
            if ($this->isVendor && $this->vendorId) {
                // Vendor-specific: count orders that have products from this vendor
                $count = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                    ->whereHas('order', function($q) use ($stage) {
                        $q->where('stage_id', $stage->id);
                    })->distinct('order_id')->count('order_id');
            } else {
                // Admin: count all orders
                $count = Order::where('stage_id', $stage->id)->count();
            }

            $overview[] = [
                'id' => $stage->id,
                'name' => $stage->name,
                'slug' => $stage->slug,
                'type' => $stage->type,
                'color' => $stage->color ?? '#6c757d',
                'count' => $count,
            ];
        }

        return $overview;
    }

    private function getIncomeExpenseData()
    {
        $now = Carbon::now();
        // This Month data
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $AccountingQuery = AccountingEntry::query()->where('type', 'income');

        $monthlyIncome = (clone $AccountingQuery)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');

        $monthlyExpenses = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])->sum('amount');
        $monthlyCommission = (clone $AccountingQuery)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('commission_amount');
        $monthlyProfit = $monthlyIncome - $monthlyExpenses;

        // This Year data
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        $yearlyIncome = (clone $AccountingQuery)->whereBetween('created_at', [$startOfYear, $endOfYear])->sum('amount');

        $yearlyExpenses = Expense::whereBetween('expense_date', [$startOfYear, $endOfYear])->sum('amount');
        $yearlyCommission = (clone $AccountingQuery)->whereBetween('created_at', [$startOfYear, $endOfYear])->sum('commission_amount');
        $yearlyProfit = $yearlyIncome - $yearlyExpenses;

        // Monthly breakdown for chart (current year)
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();

            $income = (clone $AccountingQuery)->whereBetween('created_at', [$monthStart, $monthEnd])->sum('amount');

            $expenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');

            $monthlyData[] = [
                'month' => $month,
                'income' => $income,
                'expenses' => $expenses,
            ];
        }

        // Daily breakdown for current month chart
        $dailyData = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);

            $income = (clone $AccountingQuery)->whereDate('created_at', $dayDate)->sum('amount');

            $expenses = Expense::whereDate('expense_date', $dayDate)->sum('amount');

            $dailyData[] = [
                'day' => $day,
                'income' => $income,
                'expenses' => $expenses,
            ];
        }

        return [
            'month' => [
                'income' => $monthlyIncome,
                'expenses' => $monthlyExpenses,
                'commission' => $monthlyCommission,
                'profit' => $monthlyProfit,
                'period' => $now->format('m-Y'),
                'daily_data' => $dailyData,
            ],
            'year' => [
                'income' => $yearlyIncome,
                'expenses' => $yearlyExpenses,
                'commission' => $yearlyCommission,
                'profit' => $yearlyProfit,
                'period' => $now->year,
                'monthly_data' => $monthlyData,
            ],
        ];
    }

    private function getSalesChartData()
    {
        $now = Carbon::now();

        if ($this->isVendor && $this->vendorId) {
            return $this->getVendorSalesChartData($now);
        }

        // Monthly data (last 12 months) - all orders
        $endDate = $now->copy();
        $startDate = $now->copy()->subYear();
        $monthlySales = Order::withoutCountryFilter()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as total_sales')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $monthlySales->pluck('month');
        $data = $monthlySales->pluck('total_sales');

        // Hourly data (today 12am-12pm) - all orders
        $hourly = [];
        for ($i = 0; $i < 12; $i++) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 1);
            $hourly[] = Order::withoutCountryFilter()
                ->whereBetween('created_at', [$hourStart, $hourEnd])->sum('total_price') ?? 0;
        }

        // Weekly data (this week) - all orders
        $weekly = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            $weekly[] = Order::withoutCountryFilter()
                ->whereBetween('created_at', [$dayStart, $dayEnd])->sum('total_price') ?? 0;
        }

        // Daily data (current month) - all orders
        $daily = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            $daily[] = Order::withoutCountryFilter()
                ->whereDate('created_at', $dayDate)->sum('total_price') ?? 0;
        }

        // Monthly breakdown for current year - all orders
        $monthly = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            $monthly[] = Order::withoutCountryFilter()
                ->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_price') ?? 0;
        }

        // Yearly data (last 5 years) - all orders
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            $yearlyData[] = Order::withoutCountryFilter()
                ->whereBetween('created_at', [$yearStart, $yearEnd])->sum('total_price') ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'hourly' => $hourly,
            'weekly' => $weekly,
            'daily' => $daily,
            'monthly' => $monthly,
            'yearly_labels' => $yearlyLabels,
            'yearly_data' => $yearlyData,
        ];
    }

    private function getEarningsChartData()
    {
        $now = Carbon::now();
        $deliveredStage = OrderStage::withoutCountryFilter()->where('type', 'deliver')->first();

        if ($this->isVendor && $this->vendorId) {
            return $this->getVendorEarningsChartData($now, $deliveredStage);
        }

        // If no delivered stage found, return empty data
        if (!$deliveredStage) {
            return [
                'labels' => [],
                'data' => [],
                'hourly' => [0,0,0,0,0,0,0,0],
                'weekly' => [0,0,0,0,0,0,0],
                'daily' => [],
                'monthly' => [0,0,0,0,0,0,0,0,0,0,0,0],
                'yearly_labels' => [],
                'yearly_data' => [],
            ];
        }

        $deliveredStageId = $deliveredStage->id;

        // Monthly data (last 12 months) - only delivered orders
        $endDate = $now->copy();
        $startDate = $now->copy()->subYear();
        $monthlySales = Order::withoutCountryFilter()
            ->where('stage_id', $deliveredStageId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as total_sales')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $monthlySales->pluck('month');
        $data = $monthlySales->pluck('total_sales');

        // Hourly data (today 12am-12pm) - only delivered orders
        $hourly = [];
        for ($i = 0; $i < 12; $i++) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 1);
            $hourly[] = Order::withoutCountryFilter()
                ->where('stage_id', $deliveredStageId)
                ->whereBetween('created_at', [$hourStart, $hourEnd])->sum('total_price') ?? 0;
        }

        // Weekly data (this week) - only delivered orders
        $weekly = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            $weekly[] = Order::withoutCountryFilter()
                ->where('stage_id', $deliveredStageId)
                ->whereBetween('created_at', [$dayStart, $dayEnd])->sum('total_price') ?? 0;
        }

        // Daily data (current month) - only delivered orders
        $daily = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            $daily[] = Order::withoutCountryFilter()
                ->where('stage_id', $deliveredStageId)
                ->whereDate('created_at', $dayDate)->sum('total_price') ?? 0;
        }

        // Monthly breakdown for current year - only delivered orders
        $monthly = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            $monthly[] = Order::withoutCountryFilter()
                ->where('stage_id', $deliveredStageId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_price') ?? 0;
        }

        // Yearly data (last 5 years) - only delivered orders
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            $yearlyData[] = Order::withoutCountryFilter()
                ->where('stage_id', $deliveredStageId)
                ->whereBetween('created_at', [$yearStart, $yearEnd])->sum('total_price') ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'hourly' => $hourly,
            'weekly' => $weekly,
            'daily' => $daily,
            'monthly' => $monthly,
            'yearly_labels' => $yearlyLabels,
            'yearly_data' => $yearlyData,
        ];
    }

    private function getVendorEarningsChartData($now, $deliveredStage)
    {
        $vendorId = $this->vendorId;

        // Monthly data (last 12 months) - only delivered orders for this vendor
        $endDate = $now->copy();
        $startDate = $now->copy()->subYear();

        $monthlySales = $deliveredStage
            ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($deliveredStage, $startDate, $endDate) {
                    $q->where('stage_id', $deliveredStage->id)
                      ->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->join('orders', 'order_products.order_id', '=', 'orders.id')
                ->selectRaw('DATE_FORMAT(orders.created_at, "%Y-%m") as month, SUM(order_products.price * order_products.quantity) as total_sales')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get()
            : collect([]);

        $labels = $monthlySales->pluck('month');
        $data = $monthlySales->pluck('total_sales');

        // Hourly data (today 12am-12pm) - only delivered orders for this vendor
        $hourly = [];
        for ($i = 0; $i < 12; $i++) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 1);
            $hourly[] = $deliveredStage
                ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                    ->whereHas('order', function($q) use ($deliveredStage, $hourStart, $hourEnd) {
                        $q->where('stage_id', $deliveredStage->id)
                          ->whereBetween('created_at', [$hourStart, $hourEnd]);
                    })->sum(\DB::raw('price * quantity')) ?? 0
                : 0;
        }

        // Weekly data (this week) - only delivered orders for this vendor
        $weekly = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            $weekly[] = $deliveredStage
                ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                    ->whereHas('order', function($q) use ($deliveredStage, $dayStart, $dayEnd) {
                        $q->where('stage_id', $deliveredStage->id)
                          ->whereBetween('created_at', [$dayStart, $dayEnd]);
                    })->sum(\DB::raw('price * quantity')) ?? 0
                : 0;
        }

        // Daily data (current month) - only delivered orders for this vendor
        $daily = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            $daily[] = $deliveredStage
                ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                    ->whereHas('order', function($q) use ($deliveredStage, $dayDate) {
                        $q->where('stage_id', $deliveredStage->id)
                          ->whereDate('created_at', $dayDate);
                    })->sum(\DB::raw('price * quantity')) ?? 0
                : 0;
        }

        // Monthly breakdown for current year - only delivered orders for this vendor
        $monthly = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            $monthly[] = $deliveredStage
                ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                    ->whereHas('order', function($q) use ($deliveredStage, $monthStart, $monthEnd) {
                        $q->where('stage_id', $deliveredStage->id)
                          ->whereBetween('created_at', [$monthStart, $monthEnd]);
                    })->sum(\DB::raw('price * quantity')) ?? 0
                : 0;
        }

        // Yearly data (last 5 years) - only delivered orders for this vendor
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            $yearlyData[] = $deliveredStage
                ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                    ->whereHas('order', function($q) use ($deliveredStage, $yearStart, $yearEnd) {
                        $q->where('stage_id', $deliveredStage->id)
                          ->whereBetween('created_at', [$yearStart, $yearEnd]);
                    })->sum(\DB::raw('price * quantity')) ?? 0
                : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'hourly' => $hourly,
            'weekly' => $weekly,
            'daily' => $daily,
            'monthly' => $monthly,
            'yearly_labels' => $yearlyLabels,
            'yearly_data' => $yearlyData,
        ];
    }

    private function getVendorSalesChartData($now)
    {
        $vendorId = $this->vendorId;

        // Monthly data (last 12 months) - all orders for this vendor
        $endDate = $now->copy();
        $startDate = $now->copy()->subYear();

        $monthlySales = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->join('orders', 'order_products.order_id', '=', 'orders.id')
            ->selectRaw('DATE_FORMAT(orders.created_at, "%Y-%m") as month, SUM(order_products.price * order_products.quantity) as total_sales')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $monthlySales->pluck('month');
        $data = $monthlySales->pluck('total_sales');

        // Hourly data (today 12am-12pm) - all orders for this vendor
        $hourly = [];
        for ($i = 0; $i < 12; $i++) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 1);
            $hourly[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($hourStart, $hourEnd) {
                    $q->whereBetween('created_at', [$hourStart, $hourEnd]);
                })->sum(\DB::raw('price * quantity')) ?? 0;
        }

        // Weekly data (this week) - all orders for this vendor
        $weekly = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            $weekly[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($dayStart, $dayEnd) {
                    $q->whereBetween('created_at', [$dayStart, $dayEnd]);
                })->sum(\DB::raw('price * quantity')) ?? 0;
        }

        // Daily data (current month) - all orders for this vendor
        $daily = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            $daily[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($dayDate) {
                    $q->whereDate('created_at', $dayDate);
                })->sum(\DB::raw('price * quantity')) ?? 0;
        }

        // Monthly breakdown for current year - all orders for this vendor
        $monthly = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            $monthly[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($monthStart, $monthEnd) {
                    $q->whereBetween('created_at', [$monthStart, $monthEnd]);
                })->sum(\DB::raw('price * quantity')) ?? 0;
        }

        // Yearly data (last 5 years) - all orders for this vendor
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            $yearlyData[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($yearStart, $yearEnd) {
                    $q->whereBetween('created_at', [$yearStart, $yearEnd]);
                })->sum(\DB::raw('price * quantity')) ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'hourly' => $hourly,
            'weekly' => $weekly,
            'daily' => $daily,
            'monthly' => $monthly,
            'yearly_labels' => $yearlyLabels,
            'yearly_data' => $yearlyData,
        ];
    }

    private function getLatestOrders($limit = 5)
    {
        if ($this->isVendor && $this->vendorId) {
            // Get orders that contain products from this vendor
            $orderIds = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                ->distinct()
                ->pluck('order_id');

            $orders = Order::with(['customer', 'stage', 'products.vendorProduct.product'])
                ->whereIn('id', $orderIds)
                ->latest()
                ->take($limit)
                ->get();

            // Calculate vendor's product total for each order
            $vendorId = $this->vendorId;
            foreach ($orders as $order) {
                $vendorProductTotal = $order->products
                    ->where('vendor_id', $vendorId)
                    ->sum(function($product) {
                        return $product->price * $product->quantity;
                    });
                $order->vendor_product_total = $vendorProductTotal;
            }

            return $orders;
        }

        return Order::with(['customer', 'stage', 'products.vendorProduct.product'])
            ->latest()
            ->take($limit)
            ->get();
    }

    private function getTopSellingProducts($limit = 5)
    {
        // Get products with most sold quantity
        $query = \Modules\Order\app\Models\OrderProduct::select('vendor_product_id', 'vendor_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->selectRaw('SUM(price * quantity) as total_revenue')
            ->whereNotNull('vendor_product_id');

        // Filter by vendor if logged in as vendor
        if ($this->isVendor && $this->vendorId) {
            $query->where('vendor_id', $this->vendorId);
        }

        $topProducts = $query->groupBy('vendor_product_id', 'vendor_id')
            ->orderByDesc('total_sold')
            ->take($limit)
            ->get();

        // Load relationships after grouping
        return $topProducts->map(function($item) {
            $item->vendorProduct = \Modules\CatalogManagement\app\Models\VendorProduct::with(['product.translations', 'product.mainImage'])->find($item->vendor_product_id);
            $item->vendorData = \Modules\Vendor\app\Models\Vendor::with(['translations', 'logo'])->find($item->vendor_id);
            return $item;
        });
    }

    private function getTopVendors($limit = 5)
    {
        return Vendor::with(['translations', 'logo'])
            ->withSum('total_orders', 'price')
            ->withCount('total_orders')
            ->orderByDesc('total_orders_sum_price')
            ->take($limit)
            ->get();
    }

    private function getBestCustomers($limit = 5)
    {
        if ($this->isVendor && $this->vendorId) {
            // For vendors: Get all customers (registered + external) who bought from this vendor
            // Registered customers
            $registeredCustomers = \DB::table('orders')
                ->select(
                    'customers.id',
                    'customers.first_name',
                    'customers.last_name',
                    'customers.email',
                    'customers.image',
                    'customers.created_at',
                    \DB::raw("'registered' as customer_type")
                )
                ->selectRaw('COUNT(DISTINCT orders.id) as orders_count')
                ->selectRaw('SUM(order_products.price * order_products.quantity) as orders_sum_total_price')
                ->join('customers', 'orders.customer_id', '=', 'customers.id')
                ->join('order_products', 'orders.id', '=', 'order_products.order_id')
                ->where('order_products.vendor_id', $this->vendorId)
                ->whereNotNull('orders.customer_id')
                ->groupBy('customers.id', 'customers.first_name', 'customers.last_name', 'customers.email', 'customers.image', 'customers.created_at');

            // External customers (grouped by name + email)
            $externalCustomers = \DB::table('orders')
                ->select(
                    \DB::raw('NULL as id'),
                    \DB::raw("SUBSTRING_INDEX(orders.customer_name, ' ', 1) as first_name"),
                    \DB::raw("SUBSTRING_INDEX(orders.customer_name, ' ', -1) as last_name"),
                    'orders.customer_email as email',
                    \DB::raw('NULL as image'),
                    \DB::raw('MIN(orders.created_at) as created_at'),
                    \DB::raw("'external' as customer_type")
                )
                ->selectRaw('COUNT(DISTINCT orders.id) as orders_count')
                ->selectRaw('SUM(order_products.price * order_products.quantity) as orders_sum_total_price')
                ->join('order_products', 'orders.id', '=', 'order_products.order_id')
                ->where('order_products.vendor_id', $this->vendorId)
                ->whereNull('orders.customer_id')
                ->whereNotNull('orders.customer_name')
                ->groupBy('orders.customer_name', 'orders.customer_email');

            // Union and order by total
            $customers = $registeredCustomers
                ->union($externalCustomers)
                ->orderByDesc('orders_sum_total_price')
                ->limit($limit)
                ->get();

            // Transform to collection with full_name attribute
            return $customers->map(function ($customer) {
                $customer->full_name = trim($customer->first_name . ' ' . $customer->last_name);
                return $customer;
            });
        }
        // For admin: Get all customers (registered + external) with highest total order amounts
        // Registered customers
        $registeredCustomers = \DB::table('orders')
            ->select(
                'customers.id',
                'customers.first_name',
                'customers.last_name',
                'customers.email',
                'customers.image',
                'customers.created_at',
                \DB::raw("'registered' as customer_type")
            )
            ->selectRaw('COUNT(orders.id) as orders_count')
            ->selectRaw('SUM(orders.total_price) as orders_sum_total_price')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->whereNotNull('orders.customer_id')
            ->groupBy('customers.id', 'customers.first_name', 'customers.last_name', 'customers.email', 'customers.image', 'customers.created_at');

        // External customers (grouped by name + email)
        $externalCustomers = \DB::table('orders')
            ->select(
                \DB::raw('NULL as id'),
                \DB::raw("SUBSTRING_INDEX(customer_name, ' ', 1) as first_name"),
                \DB::raw("SUBSTRING_INDEX(customer_name, ' ', -1) as last_name"),
                'customer_email as email',
                \DB::raw('NULL as image'),
                \DB::raw('MIN(created_at) as created_at'),
                \DB::raw("'external' as customer_type")
            )
            ->selectRaw('COUNT(id) as orders_count')
            ->selectRaw('SUM(total_price) as orders_sum_total_price')
            ->whereNull('customer_id')
            ->whereNotNull('customer_name')
            ->groupBy('customer_name', 'customer_email');

        // Union and order by total
        $customers = $registeredCustomers
            ->union($externalCustomers)
            ->orderByDesc('orders_sum_total_price')
            ->limit($limit)
            ->get();

        // Transform to collection with full_name attribute
        return $customers->map(function ($customer) {
            $customer->full_name = trim($customer->first_name . ' ' . $customer->last_name);
            return $customer;
        });
    }

    private function getRecentActivities($limit = 5)
    {
        return \App\Models\ActivityLog::with(['user', 'user.translations'])
            ->latest()
            ->take($limit)
            ->get();
    }
}
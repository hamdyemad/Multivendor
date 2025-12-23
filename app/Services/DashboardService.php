<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'total_order_stages' => OrderStage::count(),
            
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

        // Get order products for this vendor
        $orderProductsQuery = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId);
        
        // Get delivered order products
        $deliveredOrderProducts = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
            ->whereHas('order.stage', function($q) {
                $q->where('type', 'deliver');
            });

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
        $deliveredStage = OrderStage::where('type', 'deliver')->first();
        
        // Total expenses (you may need to adjust based on your expense tracking)
        $totalExpenses = 0; // Placeholder - implement based on your expense model
        
        if ($this->isVendor && $this->vendorId) {
            // Vendor-specific sales overview
            $totalIncome = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                ->whereHas('order.stage', function($q) {
                    $q->where('type', 'deliver');
                })->sum(\DB::raw('price * quantity'));
            
            $ytdIncome = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                ->whereHas('order', function($q) use ($startOfYear) {
                    $q->whereDate('created_at', '>=', $startOfYear);
                })
                ->whereHas('order.stage', function($q) {
                    $q->where('type', 'deliver');
                })->sum(\DB::raw('price * quantity'));
            
            $revenueYtd = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                ->whereHas('order', function($q) use ($startOfYear) {
                    $q->whereDate('created_at', '>=', $startOfYear);
                })->sum(\DB::raw('price * quantity'));
            
            $newOrdersCount = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                ->whereHas('order.stage', function($q) {
                    $q->where('type', 'new');
                })->distinct('order_id')->count('order_id');
            
            $inProgressOrdersCount = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                ->whereHas('order.stage', function($q) {
                    $q->where('type', 'in_progress');
                })->distinct('order_id')->count('order_id');
        } else {
            // Admin sales overview
            $totalIncome = $deliveredStage 
                ? Order::where('stage_id', $deliveredStage->id)->sum('total_price')
                : 0;
            
            $ytdIncome = $deliveredStage 
                ? Order::where('stage_id', $deliveredStage->id)
                    ->whereDate('created_at', '>=', $startOfYear)
                    ->sum('total_price')
                : 0;
            
            $revenueYtd = Order::whereDate('created_at', '>=', $startOfYear)->sum('total_price');
            
            $newStage = OrderStage::where('type', 'new')->first();
            $newOrdersCount = $newStage ? Order::where('stage_id', $newStage->id)->count() : 0;
            
            $inProgressStage = OrderStage::where('type', 'in_progress')->first();
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
        $stages = OrderStage::all()->keyBy('type');
        
        if ($this->isVendor && $this->vendorId) {
            // Vendor-specific orders overview
            return [
                'new' => isset($stages['new']) ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                    ->whereHas('order', function($q) use ($stages) {
                        $q->where('stage_id', $stages['new']->id);
                    })->distinct('order_id')->count('order_id') : 0,
                'in_progress' => isset($stages['in_progress']) ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                    ->whereHas('order', function($q) use ($stages) {
                        $q->where('stage_id', $stages['in_progress']->id);
                    })->distinct('order_id')->count('order_id') : 0,
                'delivered' => isset($stages['deliver']) ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                    ->whereHas('order', function($q) use ($stages) {
                        $q->where('stage_id', $stages['deliver']->id);
                    })->distinct('order_id')->count('order_id') : 0,
                'cancelled' => isset($stages['cancel']) ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                    ->whereHas('order', function($q) use ($stages) {
                        $q->where('stage_id', $stages['cancel']->id);
                    })->distinct('order_id')->count('order_id') : 0,
                'want_to_return' => isset($stages['want_to_return']) ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                    ->whereHas('order', function($q) use ($stages) {
                        $q->where('stage_id', $stages['want_to_return']->id);
                    })->distinct('order_id')->count('order_id') : 0,
                'return_in_progress' => isset($stages['in_progress_return']) ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                    ->whereHas('order', function($q) use ($stages) {
                        $q->where('stage_id', $stages['in_progress_return']->id);
                    })->distinct('order_id')->count('order_id') : 0,
                'refunded' => isset($stages['refund']) ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                    ->whereHas('order', function($q) use ($stages) {
                        $q->where('stage_id', $stages['refund']->id);
                    })->distinct('order_id')->count('order_id') : 0,
            ];
        }
        
        return [
            'new' => isset($stages['new']) ? Order::where('stage_id', $stages['new']->id)->count() : 0,
            'in_progress' => isset($stages['in_progress']) ? Order::where('stage_id', $stages['in_progress']->id)->count() : 0,
            'delivered' => isset($stages['deliver']) ? Order::where('stage_id', $stages['deliver']->id)->count() : 0,
            'cancelled' => isset($stages['cancel']) ? Order::where('stage_id', $stages['cancel']->id)->count() : 0,
            'want_to_return' => isset($stages['want_to_return']) ? Order::where('stage_id', $stages['want_to_return']->id)->count() : 0,
            'return_in_progress' => isset($stages['in_progress_return']) ? Order::where('stage_id', $stages['in_progress_return']->id)->count() : 0,
            'refunded' => isset($stages['refund']) ? Order::where('stage_id', $stages['refund']->id)->count() : 0,
        ];
    }

    private function getIncomeExpenseData()
    {
        $deliveredStage = OrderStage::where('type', 'deliver')->first();
        $now = Carbon::now();
        
        if ($this->isVendor && $this->vendorId) {
            return $this->getVendorIncomeExpenseData($now);
        }
        
        // This Month data
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        $monthlyIncome = $deliveredStage 
            ? Order::where('stage_id', $deliveredStage->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total_price')
            : 0;
        
        $monthlyExpenses = 0; // Placeholder - implement based on your expense model
        $monthlyProfit = $monthlyIncome - $monthlyExpenses;
        
        // This Year data
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();
        
        $yearlyIncome = $deliveredStage 
            ? Order::where('stage_id', $deliveredStage->id)
                ->whereBetween('created_at', [$startOfYear, $endOfYear])
                ->sum('total_price')
            : 0;
        
        $yearlyExpenses = 0; // Placeholder - implement based on your expense model
        $yearlyProfit = $yearlyIncome - $yearlyExpenses;
        
        // Monthly breakdown for chart (current year)
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            
            $income = $deliveredStage 
                ? Order::where('stage_id', $deliveredStage->id)
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('total_price')
                : 0;
            
            $monthlyData[] = [
                'month' => $month,
                'income' => $income,
                'expenses' => 0, // Placeholder
            ];
        }
        
        // Daily breakdown for current month chart
        $dailyData = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            
            $income = $deliveredStage 
                ? Order::where('stage_id', $deliveredStage->id)
                    ->whereDate('created_at', $dayDate)
                    ->sum('total_price')
                : 0;
            
            $dailyData[] = [
                'day' => $day,
                'income' => $income,
                'expenses' => 0, // Placeholder
            ];
        }
        
        return [
            'month' => [
                'income' => $monthlyIncome,
                'expenses' => $monthlyExpenses,
                'profit' => $monthlyProfit,
                'period' => $now->format('m-Y'),
                'daily_data' => $dailyData,
            ],
            'year' => [
                'income' => $yearlyIncome,
                'expenses' => $yearlyExpenses,
                'profit' => $yearlyProfit,
                'period' => $now->year,
                'monthly_data' => $monthlyData,
            ],
        ];
    }

    private function getVendorIncomeExpenseData($now)
    {
        $vendorId = $this->vendorId;
        
        // This Month data
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        $monthlyIncome = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
            ->whereHas('order.stage', function($q) {
                $q->where('type', 'deliver');
            })
            ->whereHas('order', function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            })->sum(\DB::raw('price * quantity'));
        
        $monthlyExpenses = 0;
        $monthlyProfit = $monthlyIncome - $monthlyExpenses;
        
        // This Year data
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();
        
        $yearlyIncome = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
            ->whereHas('order.stage', function($q) {
                $q->where('type', 'deliver');
            })
            ->whereHas('order', function($q) use ($startOfYear, $endOfYear) {
                $q->whereBetween('created_at', [$startOfYear, $endOfYear]);
            })->sum(\DB::raw('price * quantity'));
        
        $yearlyExpenses = 0;
        $yearlyProfit = $yearlyIncome - $yearlyExpenses;
        
        // Monthly breakdown for chart (current year)
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            
            $income = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order.stage', function($q) {
                    $q->where('type', 'deliver');
                })
                ->whereHas('order', function($q) use ($monthStart, $monthEnd) {
                    $q->whereBetween('created_at', [$monthStart, $monthEnd]);
                })->sum(\DB::raw('price * quantity'));
            
            $monthlyData[] = [
                'month' => $month,
                'income' => $income,
                'expenses' => 0,
            ];
        }
        
        // Daily breakdown for current month chart
        $dailyData = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            
            $income = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order.stage', function($q) {
                    $q->where('type', 'deliver');
                })
                ->whereHas('order', function($q) use ($dayDate) {
                    $q->whereDate('created_at', $dayDate);
                })->sum(\DB::raw('price * quantity'));
            
            $dailyData[] = [
                'day' => $day,
                'income' => $income,
                'expenses' => 0,
            ];
        }
        
        return [
            'month' => [
                'income' => $monthlyIncome,
                'expenses' => $monthlyExpenses,
                'profit' => $monthlyProfit,
                'period' => $now->format('m-Y'),
                'daily_data' => $dailyData,
            ],
            'year' => [
                'income' => $yearlyIncome,
                'expenses' => $yearlyExpenses,
                'profit' => $yearlyProfit,
                'period' => $now->year,
                'monthly_data' => $monthlyData,
            ],
        ];
    }

    private function getSalesChartData()
    {
        $now = Carbon::now();
        $deliveredStage = OrderStage::where('type', 'deliver')->first();
        $deliveredStageId = $deliveredStage ? $deliveredStage->id : 0;
        
        if ($this->isVendor && $this->vendorId) {
            return $this->getVendorSalesChartData($now);
        }
        
        // Monthly data (last 12 months) - only delivered orders
        $endDate = $now->copy();
        $startDate = $now->copy()->subYear();
        $monthlySales = Order::where('stage_id', $deliveredStageId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as total_sales')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $monthlySales->pluck('month');
        $data = $monthlySales->pluck('total_sales');
        
        // Hourly data (today) - only delivered orders
        $hourly = [];
        for ($i = 0; $i < 24; $i += 3) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 3);
            $hourly[] = Order::where('stage_id', $deliveredStageId)
                ->whereBetween('created_at', [$hourStart, $hourEnd])->sum('total_price') ?? 0;
        }
        
        // Weekly data (this week) - only delivered orders
        $weekly = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            $weekly[] = Order::where('stage_id', $deliveredStageId)
                ->whereBetween('created_at', [$dayStart, $dayEnd])->sum('total_price') ?? 0;
        }
        
        // Yearly data (last 5 years) - only delivered orders
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            $yearlyData[] = Order::where('stage_id', $deliveredStageId)
                ->whereBetween('created_at', [$yearStart, $yearEnd])->sum('total_price') ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'hourly' => $hourly,
            'weekly' => $weekly,
            'yearly_labels' => $yearlyLabels,
            'yearly_data' => $yearlyData,
        ];
    }

    private function getVendorSalesChartData($now)
    {
        $vendorId = $this->vendorId;
        
        // Monthly data (last 12 months) - only delivered orders for this vendor
        $endDate = $now->copy();
        $startDate = $now->copy()->subYear();
        
        $monthlySales = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
            ->whereHas('order.stage', function($q) {
                $q->where('type', 'deliver');
            })
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
        
        // Hourly data (today) - only delivered orders for this vendor
        $hourly = [];
        for ($i = 0; $i < 24; $i += 3) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 3);
            $hourly[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order.stage', function($q) {
                    $q->where('type', 'deliver');
                })
                ->whereHas('order', function($q) use ($hourStart, $hourEnd) {
                    $q->whereBetween('created_at', [$hourStart, $hourEnd]);
                })->sum(\DB::raw('price * quantity')) ?? 0;
        }
        
        // Weekly data (this week) - only delivered orders for this vendor
        $weekly = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            $weekly[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order.stage', function($q) {
                    $q->where('type', 'deliver');
                })
                ->whereHas('order', function($q) use ($dayStart, $dayEnd) {
                    $q->whereBetween('created_at', [$dayStart, $dayEnd]);
                })->sum(\DB::raw('price * quantity')) ?? 0;
        }
        
        // Yearly data (last 5 years) - only delivered orders for this vendor
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            $yearlyData[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order.stage', function($q) {
                    $q->where('type', 'deliver');
                })
                ->whereHas('order', function($q) use ($yearStart, $yearEnd) {
                    $q->whereBetween('created_at', [$yearStart, $yearEnd]);
                })->sum(\DB::raw('price * quantity')) ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'hourly' => $hourly,
            'weekly' => $weekly,
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
            
            return Order::with(['customer', 'stage', 'products.vendorProduct.product'])
                ->whereIn('id', $orderIds)
                ->latest()
                ->take($limit)
                ->get();
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
            // Get customers who bought from this vendor
            return Customer::withoutCountryFilter()
                ->select('customers.*')
                ->selectRaw('COUNT(DISTINCT orders.id) as orders_count')
                ->selectRaw('SUM(order_products.price * order_products.quantity) as orders_sum_total_price')
                ->join('orders', 'customers.id', '=', 'orders.customer_id')
                ->join('order_products', 'orders.id', '=', 'order_products.order_id')
                ->where('order_products.vendor_id', $this->vendorId)
                ->groupBy('customers.id')
                ->orderByDesc('orders_sum_total_price')
                ->take($limit)
                ->get();
        }
        
        return Customer::withCount('orders')
            ->withSum('orders', 'total_price')
            ->orderByDesc('orders_sum_total_price')
            ->take($limit)
            ->get();
    }

    private function getRecentActivities($limit = 5)
    {
        return \App\Models\ActivityLog::with(['user', 'user.translations'])
            ->latest()
            ->take($limit)
            ->get();
    }
}

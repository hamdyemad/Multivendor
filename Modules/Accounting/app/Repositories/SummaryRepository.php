<?php

namespace Modules\Accounting\app\Repositories;

use Modules\Accounting\app\Contracts\SummaryRepositoryInterface;
use Modules\Accounting\app\Models\AccountingEntry;
use Modules\Accounting\app\Models\Expense;
use Carbon\Carbon;

class SummaryRepository implements SummaryRepositoryInterface
{
    public function getSummaryData(array $filters = []): array
    {
        $query = AccountingEntry::query();
        $expenseQuery = Expense::query();
        
        // Map date_from/date_to to created_date_from/created_date_to for filter scope
        $mappedFilters = $filters;
        if (!empty($filters['date_from'])) {
            $mappedFilters['created_date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $mappedFilters['created_date_to'] = $filters['date_to'];
        }
        
        // Use scope filters for AccountingEntry
        $query->filter($mappedFilters);
        
        // Use scope filters for Expense
        $expenseQuery->filter($mappedFilters);
        
        // Get monthly data
        $monthlyData = $this->getMonthlyData($filters);
        $expenseCategories = $this->getExpenseCategories($filters);
        
        // Get total withdraws (accepted)
        $totalWithdraws = $this->getTotalWithdraws($filters);
        
        // Calculate totals from monthly data to ensure consistency
        $totalOrderAmount = 0;
        $totalCommissions = 0;
        $totalVendorShare = 0;
        $totalExpenses = 0;
        $totalWithdrawsFromMonthly = 0;
        
        foreach ($monthlyData as $month => $data) {
            $totalOrderAmount += $data['income'] ?? 0;
            $totalCommissions += $data['commissions'] ?? 0;
            $totalVendorShare += $data['vendor_share'] ?? 0;
            $totalExpenses += $data['expenses'] ?? 0;
            $totalWithdrawsFromMonthly += $data['withdraws'] ?? 0;
        }
        
        // Get refunds with date filter
        $refundQuery = AccountingEntry::refund();
        if (!empty($filters['date_from'])) {
            $refundQuery->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $refundQuery->whereDate('created_at', '<=', $filters['date_to']);
        }
        $totalRefunds = abs($refundQuery->sum('amount'));
        
        return [
            'total_income' => $totalOrderAmount, // Show full order amount as Total Income
            'total_order_amount' => $totalOrderAmount, // Full order amounts
            'total_expenses' => $totalExpenses,
            'total_commissions' => $totalCommissions, // Platform's commission
            'total_vendor_share' => $totalVendorShare, // Amount that goes to vendors
            'total_refunds' => $totalRefunds,
            'total_withdraws' => $totalWithdrawsFromMonthly,
            // Net profit = Total Income - Commissions - Expenses
            'net_profit' => $totalOrderAmount - $totalCommissions - $totalExpenses,
            'monthly_data' => $monthlyData,
            'expense_categories' => $expenseCategories
        ];
    }
    
    private function getTotalWithdraws(array $filters = []): float
    {
        $query = \Modules\Withdraw\app\Models\Withdraw::where('status', 'accepted');
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        return $query->sum('sent_amount');
    }
    
    private function getMonthlyData(array $filters = []): array
    {
        // Determine the year and month range based on filters
        $year = date('Y');
        $startMonth = 1;
        $endMonth = 12;
        
        if (!empty($filters['date_from'])) {
            $filterStart = Carbon::parse($filters['date_from']);
            $year = $filterStart->year;
            $startMonth = $filterStart->month;
        }
        
        if (!empty($filters['date_to'])) {
            $filterEnd = Carbon::parse($filters['date_to']);
            // If date_to is in a different year than date_from, use date_to's year for end
            if (!empty($filters['date_from'])) {
                $filterStart = Carbon::parse($filters['date_from']);
                if ($filterEnd->year == $filterStart->year) {
                    $endMonth = $filterEnd->month;
                } else {
                    // Cross-year range - for now just show to December of start year
                    $endMonth = 12;
                }
            } else {
                $year = $filterEnd->year;
                $endMonth = $filterEnd->month;
            }
        }
        
        $monthlyData = [];
        
        for ($month = $startMonth; $month <= $endMonth; $month++) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
            
            // Adjust boundaries based on filters
            if (!empty($filters['date_from'])) {
                $filterStart = Carbon::parse($filters['date_from'])->startOfDay();
                if ($startDate < $filterStart && $month == $startMonth) {
                    $startDate = $filterStart;
                }
            }
            
            if (!empty($filters['date_to'])) {
                $filterEnd = Carbon::parse($filters['date_to'])->endOfDay();
                if ($endDate > $filterEnd && $month == $endMonth) {
                    $endDate = $filterEnd;
                }
            }
            
            // Income for this month
            $incomeQuery = AccountingEntry::income()
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            // Expenses for this month  
            $expenseQuery = Expense::whereBetween('created_at', [$startDate, $endDate]);
            
            // Withdraws for this month (accepted only)
            $withdrawQuery = \Modules\Withdraw\app\Models\Withdraw::where('status', 'accepted')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            $monthlyData[$month] = [
                'income' => $incomeQuery->sum('amount'),
                'order_amount' => $incomeQuery->sum('amount'),
                'expenses' => $expenseQuery->sum('amount'),
                'commissions' => (clone $incomeQuery)->sum('commission_amount'),
                'vendor_share' => (clone $incomeQuery)->sum('vendor_amount'),
                'withdraws' => $withdrawQuery->sum('sent_amount')
            ];
        }
        
        return $monthlyData;
    }
    
    private function getExpenseCategories(array $filters = []): array
    {
        // Determine the year and month range based on filters
        $year = date('Y');
        $startMonth = 1;
        $endMonth = 12;
        
        if (!empty($filters['date_from'])) {
            $filterStart = Carbon::parse($filters['date_from']);
            $year = $filterStart->year;
            $startMonth = $filterStart->month;
        }
        
        if (!empty($filters['date_to'])) {
            $filterEnd = Carbon::parse($filters['date_to']);
            if (!empty($filters['date_from'])) {
                $filterStart = Carbon::parse($filters['date_from']);
                if ($filterEnd->year == $filterStart->year) {
                    $endMonth = $filterEnd->month;
                }
            } else {
                $year = $filterEnd->year;
                $endMonth = $filterEnd->month;
            }
        }
        
        // Get expense categories with monthly breakdown
        $categories = [];
        
        // Get all expense items
        $expenseItems = \Modules\Accounting\app\Models\ExpenseItem::all();
        
        foreach ($expenseItems as $item) {
            $monthlyExpenses = [];
            
            for ($month = $startMonth; $month <= $endMonth; $month++) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                
                // Adjust boundaries based on filters
                if (!empty($filters['date_from'])) {
                    $filterStart = Carbon::parse($filters['date_from'])->startOfDay();
                    if ($startDate < $filterStart && $month == $startMonth) {
                        $startDate = $filterStart;
                    }
                }
                
                if (!empty($filters['date_to'])) {
                    $filterEnd = Carbon::parse($filters['date_to'])->endOfDay();
                    if ($endDate > $filterEnd && $month == $endMonth) {
                        $endDate = $filterEnd;
                    }
                }
                
                $expenseQuery = Expense::where('expense_item_id', $item->id)
                    ->whereBetween('created_at', [$startDate, $endDate]);
                
                $monthlyExpenses[$month] = $expenseQuery->sum('amount');
            }
            
            $categories[] = [
                'name' => $item->name,
                'monthly' => $monthlyExpenses
            ];
        }
        
        // Add expenses without category (expense_item_id is null)
        $uncategorizedMonthlyExpenses = [];
        
        for ($month = $startMonth; $month <= $endMonth; $month++) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
            
            // Adjust boundaries based on filters
            if (!empty($filters['date_from'])) {
                $filterStart = Carbon::parse($filters['date_from'])->startOfDay();
                if ($startDate < $filterStart && $month == $startMonth) {
                    $startDate = $filterStart;
                }
            }
            
            if (!empty($filters['date_to'])) {
                $filterEnd = Carbon::parse($filters['date_to'])->endOfDay();
                if ($endDate > $filterEnd && $month == $endMonth) {
                    $endDate = $filterEnd;
                }
            }
            
            $expenseQuery = Expense::whereNull('expense_item_id')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            $uncategorizedMonthlyExpenses[$month] = $expenseQuery->sum('amount');
        }
        
        // Only add uncategorized if there are expenses without category
        if (array_sum($uncategorizedMonthlyExpenses) > 0) {
            $categories[] = [
                'name' => __('accounting.uncategorized_expenses'),
                'monthly' => $uncategorizedMonthlyExpenses
            ];
        }
        
        return $categories;
    }
}



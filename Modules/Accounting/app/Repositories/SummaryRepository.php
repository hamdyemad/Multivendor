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
        
        // Use scope filters for AccountingEntry
        $query->filter($filters);
        
        // Use scope filters for Expense with date mapping
        $expenseFilters = $filters;
        if (!empty($filters['date_from'])) {
            $expenseFilters['created_date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $expenseFilters['created_date_to'] = $filters['date_to'];
        }
        $expenseQuery->filter($expenseFilters);
        
        // Get monthly data
        $monthlyData = $this->getMonthlyData($filters);
        $expenseCategories = $this->getExpenseCategories($filters);
        
        // Get total withdraws (accepted)
        $totalWithdraws = $this->getTotalWithdraws($filters);
        
        // Calculate totals
        // amount = full order amount (vendor total + shipping) - THIS IS WHAT WE SHOW AS TOTAL INCOME
        // commission_amount = platform's share
        // vendor_amount = vendor's share
        $totalOrderAmount = (clone $query)->income()->sum('amount');
        $totalCommissions = (clone $query)->income()->sum('commission_amount');
        $totalVendorShare = (clone $query)->income()->sum('vendor_amount');
        $totalExpenses = $expenseQuery->sum('amount');
        
        return [
            'total_income' => $totalOrderAmount, // Show full order amount as Total Income
            'total_order_amount' => $totalOrderAmount, // Full order amounts
            'total_expenses' => $totalExpenses,
            'total_commissions' => $totalCommissions, // Platform's commission
            'total_vendor_share' => $totalVendorShare, // Amount that goes to vendors
            'total_refunds' => abs((clone $query)->refund()->sum('amount')),
            'total_withdraws' => $totalWithdraws,
            // Net profit = Platform Commission - Expenses
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
        $currentYear = date('Y');
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endDate = Carbon::create($currentYear, $month, 1)->endOfMonth();
            
            // Income for this month
            $incomeQuery = AccountingEntry::income()
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            // Expenses for this month  
            $expenseQuery = Expense::whereBetween('created_at', [$startDate, $endDate]);
            
            // Withdraws for this month (accepted only)
            $withdrawQuery = \Modules\Withdraw\app\Models\Withdraw::where('status', 'accepted')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            // Apply additional filters if provided
            if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
                $filterStart = Carbon::parse($filters['date_from']);
                $filterEnd = Carbon::parse($filters['date_to']);
                
                // Only include months within the filter range
                if ($endDate < $filterStart || $startDate > $filterEnd) {
                    $monthlyData[$month] = [
                        'income' => 0,
                        'expenses' => 0,
                        'commissions' => 0,
                        'withdraws' => 0
                    ];
                    continue;
                }
                
                // Adjust date range to filter boundaries
                $startDate = $startDate->max($filterStart);
                $endDate = $endDate->min($filterEnd);
                
                $incomeQuery->whereBetween('created_at', [$startDate, $endDate]);
                $expenseQuery->whereBetween('created_at', [$startDate, $endDate]);
                $withdrawQuery->whereBetween('created_at', [$startDate, $endDate]);
            }
            
            $monthlyData[$month] = [
                'income' => $incomeQuery->sum('amount'), // Full order amount as income
                'order_amount' => $incomeQuery->sum('amount'), // Full order amount
                'expenses' => $expenseQuery->sum('amount'),
                'commissions' => (clone $incomeQuery)->sum('commission_amount'), // Platform commission
                'vendor_share' => (clone $incomeQuery)->sum('vendor_amount'),
                'withdraws' => $withdrawQuery->sum('sent_amount')
            ];
        }
        
        return $monthlyData;
    }
    
    private function getExpenseCategories(array $filters = []): array
    {
        // Get expense categories with monthly breakdown
        $categories = [];
        
        // Get all expense items
        $expenseItems = \Modules\Accounting\app\Models\ExpenseItem::all();
        
        foreach ($expenseItems as $item) {
            $monthlyExpenses = [];
            
            for ($month = 1; $month <= 12; $month++) {
                $startDate = Carbon::create(date('Y'), $month, 1)->startOfMonth();
                $endDate = Carbon::create(date('Y'), $month, 1)->endOfMonth();
                
                $expenseQuery = Expense::where('expense_item_id', $item->id)
                    ->whereBetween('created_at', [$startDate, $endDate]);
                
                // Apply filters if provided
                if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
                    $filterStart = Carbon::parse($filters['date_from']);
                    $filterEnd = Carbon::parse($filters['date_to']);
                    
                    if ($endDate < $filterStart || $startDate > $filterEnd) {
                        $monthlyExpenses[$month] = 0;
                        continue;
                    }
                    
                    $startDate = $startDate->max($filterStart);
                    $endDate = $endDate->min($filterEnd);
                    
                    $expenseQuery->whereBetween('created_at', [$startDate, $endDate]);
                }
                
                $monthlyExpenses[$month] = $expenseQuery->sum('amount');
            }
            
            $categories[] = [
                'name' => $item->name,
                'monthly' => $monthlyExpenses
            ];
        }
        
        // Add expenses without category (expense_item_id is null)
        $uncategorizedMonthlyExpenses = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create(date('Y'), $month, 1)->startOfMonth();
            $endDate = Carbon::create(date('Y'), $month, 1)->endOfMonth();
            
            $expenseQuery = Expense::whereNull('expense_item_id')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            // Apply filters if provided
            if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
                $filterStart = Carbon::parse($filters['date_from']);
                $filterEnd = Carbon::parse($filters['date_to']);
                
                if ($endDate < $filterStart || $startDate > $filterEnd) {
                    $uncategorizedMonthlyExpenses[$month] = 0;
                    continue;
                }
                
                $startDate = $startDate->max($filterStart);
                $endDate = $endDate->min($filterEnd);
                
                $expenseQuery->whereBetween('created_at', [$startDate, $endDate]);
            }
            
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



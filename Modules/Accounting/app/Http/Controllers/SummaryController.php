<?php

namespace Modules\Accounting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Accounting\app\Http\Requests\AccountingSummaryRequest;
use Modules\Accounting\app\Services\SummaryService;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function __construct(private SummaryService $summaryService) {}

    public function index(AccountingSummaryRequest $request)
    {
        $filters = $request->validated();
        $summary = $this->summaryService->getSummary($filters);
        
        // Generate month headers based on filters or current year
        $monthHeaders = $this->generateMonthHeaders($filters);
        
        return view('accounting::summary', compact('summary', 'monthHeaders'));
    }
    
    private function generateMonthHeaders($filters)
    {
        $currentYear = date('Y');
        
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $startDate = \Carbon\Carbon::parse($filters['date_from']);
            $endDate = \Carbon\Carbon::parse($filters['date_to']);
        } else {
            // Default to full year (January to December)
            $startDate = \Carbon\Carbon::create($currentYear, 1, 1);
            $endDate = \Carbon\Carbon::create($currentYear, 12, 31);
        }
        
        $months = [];
        $current = $startDate->copy()->startOfMonth();
        
        while ($current <= $endDate) {
            $months[] = [
                'key' => $current->month,
                'name' => $current->format('M Y')
            ];
            $current->addMonth();
        }
        
        return $months;
    }
}



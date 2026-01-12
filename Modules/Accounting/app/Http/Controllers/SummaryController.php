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
        // Determine the year and month range based on filters
        $year = date('Y');
        $startMonth = 1;
        $endMonth = 12;
        
        if (!empty($filters['date_from'])) {
            $filterStart = \Carbon\Carbon::parse($filters['date_from']);
            $year = $filterStart->year;
            $startMonth = $filterStart->month;
        }
        
        if (!empty($filters['date_to'])) {
            $filterEnd = \Carbon\Carbon::parse($filters['date_to']);
            if (!empty($filters['date_from'])) {
                $filterStart = \Carbon\Carbon::parse($filters['date_from']);
                if ($filterEnd->year == $filterStart->year) {
                    $endMonth = $filterEnd->month;
                }
            } else {
                $year = $filterEnd->year;
                $endMonth = $filterEnd->month;
            }
        }
        
        $months = [];
        for ($month = $startMonth; $month <= $endMonth; $month++) {
            $date = \Carbon\Carbon::create($year, $month, 1);
            $months[] = [
                'key' => $month,
                'name' => $date->format('M Y')
            ];
        }
        
        return $months;
    }
}



<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $countryCode = session('country_code', 'eg');
        $data = $this->dashboardService->getDashboardData($countryCode);
        $data['title'] = __('menu.dashboard.title');

        return view('pages.dashboard.dashboard', $data);
    }
}

<?php

namespace App\Http\Controllers;

use Modules\Withdraw\app\Models\Withdraw;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title' => __('menu.dashboard.title'),
        ];
        return view('pages.dashboard.dashboard', $data);
    }
}

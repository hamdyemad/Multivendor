<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title' => __('menu.dashboard.title'),
        ];
        return $data;
        return view('pages.dashboard.dashboard', $data);
    }

    // Automatically get all models inside app/Models


}

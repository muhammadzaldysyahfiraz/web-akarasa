<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(DashboardReportService $report): View
    {
        return view('admin.dashboard', ['report' => $report->summary()]);
    }
}
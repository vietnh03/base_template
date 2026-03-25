<?php

namespace App\AppMain\CMS\Dashboard;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Show dashboard page with statistics
     */
    public function index()
    {
        $statistics = $this->dashboardService->getStatistics();

        return view('admin.dashboard.index', $statistics);
    }
}

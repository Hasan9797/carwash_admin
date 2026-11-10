<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
    ) {}

    public function index(): View
    {
        $stats = $this->dashboardService->getStatistics();
        $chartData = $this->dashboardService->getWeeklyChartData();

        return view('dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
        ]);
    }
}

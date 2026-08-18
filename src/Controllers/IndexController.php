<?php

namespace App\Controllers;

use App\Core\Redirect;
use App\Core\View;
use App\Services\DashboardService;

class IndexController
{
    private DashboardService $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
    }

    public function redirectToAdmin(): void
    {
        Redirect::to('/admin');
    }

    public function index(): void
    {
        $date = $_GET['date'] ?? null;
        $stats = $this->dashboardService->getAdminStats($date);

        View::render('admin/index', $stats);
    }
}

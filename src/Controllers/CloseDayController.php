<?php

namespace App\Controllers;

use App\Core\Logger;
use App\Core\Redirect;
use App\Core\Session;
use App\Core\View;
use App\Models\Action;
use App\Repositories\BusinessDayRepository;
use App\Repositories\OrderRepository;
use App\Services\BusinessDayService;

class CloseDayController
{
    private BusinessDayService $businessDayService;

    public function __construct()
    {
        $this->businessDayService = new BusinessDayService(
            new BusinessDayRepository(),
            new OrderRepository(),
        );
    }

    public function form(): void
    {
        $day = $this->businessDayService->currentOpenDay();
        $summary = $this->businessDayService->summaryForDate($day['date']);

        View::render('admin/close_day', [
            'day' => $day,
            'summary' => $summary,
        ]);
    }

    public function run(): void
    {
        $userId = (int)Session::get('user_id');

        $this->businessDayService->close($userId);

        Logger::add($userId, Action::fromRequest('Business day closed'));
        Redirect::to('/admin');
    }
}
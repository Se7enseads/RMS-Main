<?php

namespace App\Controllers;

use App\Core\View;
use App\Repositories\OrderRepository;

class ReportsController
{
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
    }

    public function index(): void
    {
        $from = $_GET['from'] ?? date('Y-m-d');
        $to = $_GET['to'] ?? date('Y-m-d');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $from)) {
            $from = date('Y-m-d');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $to)) {
            $to = date('Y-m-d');
        }

        View::render('admin/reports', [
            'salesByDay' => $this->orderRepository->salesByDay($from, $to),
            'itemSales' => $this->orderRepository->itemSales($from, $to),
            'paymentMethods' => $this->orderRepository->paymentMethodSummary($from, $to),
            'categorySales' => $this->orderRepository->categorySales($from, $to),
            'hourlySales' => $this->orderRepository->hourlySales($from, $to),
            'statusSummary' => $this->orderRepository->statusSummary($from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }
}
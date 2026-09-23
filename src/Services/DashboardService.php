<?php

namespace App\Services;

use App\Repositories\MenuRepository;
use App\Repositories\OrderRepository;
use App\Repositories\TableRepository;
use App\Repositories\UserRepository;

class DashboardService
{
    private OrderRepository $orderRepository;
    private MenuRepository $menuRepository;
    private UserRepository $userRepository;
    private TableRepository $tableRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->menuRepository = new MenuRepository();
        $this->userRepository = new UserRepository();
        $this->tableRepository = new TableRepository();
    }

    /**
     * @return array<string,mixed>
     */
    public function getAdminStats(?string $date = null): array
    {
        $date = $date ?? date('Y-m-d');
        $from = date('Y-m-d', strtotime($date . ' -6 days'));

        $salesByDay = $this->orderRepository->salesByDay($from, $date);
        $byDay = [];
        foreach ($salesByDay as $row) {
            $byDay[$row['day']] = $row;
        }

        $trendLabels = [];
        $trendRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime($date . " -$i days"));
            $trendLabels[] = $day;
            $trendRevenue[] = $byDay[$day]['revenue'] ?? 0.0;
        }

        $itemRows = $this->orderRepository->itemSales($date, $date);
        usort($itemRows, static fn (array $a, array $b): int => $b['revenue'] <=> $a['revenue']);
        $topItems = array_slice($itemRows, 0, 10);

        $categoryTotals = [];
        foreach ($itemRows as $row) {
            $categoryTotals[$row['category']] = ($categoryTotals[$row['category']] ?? 0.0) + $row['revenue'];
        }
        arsort($categoryTotals);

        return [
            'date' => $date,
            'businessDay' => (new BusinessDayService(
                new \App\Repositories\BusinessDayRepository(),
                $this->orderRepository,
            ))->currentOpenDay(),
            'revenueToday' => $this->orderRepository->sumRevenueForDate($date),
            'ordersToday' => $this->orderRepository->countOrdersForDate($date),
            'openOrders' => $this->orderRepository->countOpenOrders(),
            'menuItems' => $this->menuRepository->countActiveItems(),
            'activeUsers' => $this->userRepository->countActive(),
            'tablesTotal' => $this->tableRepository->countAll(),
            'tablesAvailable' => $this->tableRepository->countByStatus('AVAILABLE'),
            'tablesOccupied' => $this->tableRepository->countByStatus('OCCUPIED'),
            'tablesReserved' => $this->tableRepository->countByStatus('RESERVED'),
            'trendLabels' => $trendLabels,
            'trendRevenue' => $trendRevenue,
            'topItems' => $topItems,
            'categoryLabels' => array_keys($categoryTotals),
            'categoryRevenue' => array_values($categoryTotals),
        ];
    }
}

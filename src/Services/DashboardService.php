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

        return [
            'date' => $date,
            'revenueToday' => $this->orderRepository->sumRevenueForDate($date),
            'ordersToday' => $this->orderRepository->countOrdersForDate($date),
            'openOrders' => $this->orderRepository->countOpenOrders(),
            'menuItems' => $this->menuRepository->countActiveItems(),
            'activeUsers' => $this->userRepository->countActive(),
            'tablesTotal' => $this->tableRepository->countAll(),
            'tablesAvailable' => $this->tableRepository->countByStatus('AVAILABLE'),
            'tablesOccupied' => $this->tableRepository->countByStatus('OCCUPIED'),
            'tablesReserved' => $this->tableRepository->countByStatus('RESERVED'),
        ];
    }
}

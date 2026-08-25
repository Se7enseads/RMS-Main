<?php

namespace App\Controllers;

use App\Core\Logger;
use App\Core\Redirect;
use App\Core\Session;
use App\Core\View;
use App\Models\Action;
use App\Repositories\RestaurantRepository;
use App\Repositories\TableRepository;
use App\Services\MenuService;
use App\Services\OrderService;

class KioskController
{
    private MenuService $menuService;
    private OrderService $orderService;
    private TableRepository $tableRepository;

    public function __construct()
    {
        $this->menuService = new MenuService();
        $this->orderService = new OrderService();
        $this->tableRepository = new TableRepository();
    }

    public function dashboard(): void
    {
        $date = $_GET['date'] ?? null;
        $data = $this->orderService->getDashboardData($date);

        $openOrders = $data['openOrders'];
        $orders = $data['orders'];

        $todayPaid = [];
        $todayUnpaid = [];

        foreach ($orders as $order) {
            if ($order->isPaid) {
                $todayPaid[] = $order;
            } else {
                $todayUnpaid[] = $order;
            }
        }

        View::render('kiosk/dashboard', [
            'openOrders' => $openOrders,
            'todayPaid' => $todayPaid,
            'todayUnpaid' => $todayUnpaid,
            'itemsByOrderId' => $data['itemsByOrderId'],
            'date' => $data['date'],
            'dateFilter' => $date,
        ]);
    }

    public function order(): void
    {
        $menu = $this->menuService->getMenuGroupedByCategory();
        $tables = $this->tableRepository->findAvailable();

        View::render('kiosk/order', [
            'menu' => $menu,
            'tables' => $tables,
        ]);
    }

    public function payments(): void
    {
        $date = $_GET['date'] ?? null;
        $data = $this->orderService->getPaymentsData($date);

        View::render('kiosk/payments', [
            'orders' => $data['orders'],
            'paidTotal' => $data['paidTotal'],
            'unpaidTotal' => $data['unpaidTotal'],
            'date' => $data['date'],
            'dateFilter' => $date,
        ]);
    }

    public function placeOrder(): void
    {
        $userId = (int)Session::get('user_id');
        $type = $_POST['order_type'] ?? 'DINE_IN';
        $tableId = (int)($_POST['table_id'] ?? 0);
        $itemsJson = $_POST['items'] ?? '[]';

        $items = json_decode($itemsJson, true);

        if (!is_array($items) || !in_array($type, ['DINE_IN', 'TAKEAWAY', 'DELIVERY'], true)) {
            View::render('kiosk/order', [
                'menu' => $this->menuService->getMenuGroupedByCategory(),
                'tables' => $this->tableRepository->findAvailable(),
                'error' => 'Invalid order submission.',
            ]);
            return;
        }

        $result = $this->orderService->placeOrder($userId, $type, $tableId, $items);

        if ($result['success']) {
            Logger::add($userId, Action::fromRequest('Order placed: ' . $result['order']->orderNumber));
            Redirect::to('/kiosk');
            return;
        }

        View::render('kiosk/order', [
            'menu' => $this->menuService->getMenuGroupedByCategory(),
            'tables' => $this->tableRepository->findAvailable(),
            'error' => $result['error'],
        ]);
    }

    public function bill(int $id): void
    {
        $data = $this->orderService->getOrderBill($id);

        if (!$data) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        Logger::add((int)Session::get('user_id'), Action::fromRequest('Bill printed: ' . $data['order']->orderNumber));

        View::render('kiosk/bill', [
            'order' => $data['order'],
            'items' => $data['items'],
            'restaurant' => (new RestaurantRepository())->getDetails() ?? [],
        ]);
    }
}

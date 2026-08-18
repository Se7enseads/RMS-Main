<?php

namespace App\Services;

use App\Repositories\MenuRepository;
use App\Repositories\OrderRepository;
use Throwable;

class OrderService
{
    private OrderRepository $orderRepository;
    private MenuRepository $menuRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->menuRepository = new MenuRepository();
    }

    /**
     * @return array<string, mixed>
     */
    public function getDashboardData(?string $date = null): array
    {
        $date = $date ?? date('Y-m-d');

        return [
            'openOrders' => $this->orderRepository->findOpenOrders(),
            'orders' => $this->orderRepository->findOrdersForDate($date),
            'date' => $date,
        ];
    }

    /**
     * @return array<int, Order>
     */
    public function getOrdersByUserId(int $userId): array
    {
        return $this->orderRepository->findOrdersByUserId($userId);
    }

    /**
     * @param array<int, array{menu_item_id: int, quantity: int}> $items
     * @return array<string, mixed>
     * @throws Throwable
     */
    public function placeOrder(int $userId, string $type, int $tableId, array $items): array
    {
        if (empty($items)) {
            return ['success' => false, 'error' => 'Order has no items.'];
        }

        $orderItems = [];
        $total = 0.0;

        foreach ($items as $item) {
            $menuItem = $this->menuRepository->findItemById((int)$item['menu_item_id']);
            $quantity = max(1, (int)$item['quantity']);

            if (!$menuItem) {
                return ['success' => false, 'error' => 'Invalid menu item selected.'];
            }

            $orderItems[] = [
                'menu_item_id' => $menuItem->id,
                'price_at_time' => $menuItem->price,
                'quantity' => $quantity,
            ];
            $total += $menuItem->price * $quantity;
        }

        $order = $this->orderRepository->insertWithItems([
            'order_number' => $this->generateOrderNumber(),
            'status' => 'PLACED',
            'type' => $type,
            'user_id' => $userId,
            'table_id' => $type === 'DINE_IN' ? $tableId : null,
            'total_amount' => round($total, 2),
        ], $orderItems);

        return ['success' => true, 'order' => $order];
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
    }
}

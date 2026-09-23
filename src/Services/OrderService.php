<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
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
    public function getDashboardData(?string $date = null, ?int $userId = null): array
    {
        $date = $date ?? date('Y-m-d');

        $openOrders = $userId !== null
            ? $this->orderRepository->findOpenOrdersByUserId($userId)
            : $this->orderRepository->findOpenOrders();
        $orders = $this->orderRepository->findOrdersForDate($date);

        $orderIds = array_values(array_unique(array_map(
            fn(Order $order) => $order->id,
            array_merge($openOrders, $orders),
        )));

        return [
            'openOrders' => $this->enrich($openOrders),
            'orders' => $this->enrich($orders),
            'itemsByOrderId' => $this->orderRepository->findItemsByOrderIds($orderIds),
            'date' => $date,
        ];
    }

    /**
     * @return array<int, Order>
     */
    public function getOrdersByUserId(int $userId): array
    {
        return $this->enrich($this->orderRepository->findOrdersByUserId($userId));
    }

    /**
     * Attach item counts, payment status and method to a batch of orders
     * with two grouped queries instead of per-row subqueries.
     *
     * @param array<int, Order> $orders
     * @return array<int, Order>
     */
    private function enrich(array $orders): array
    {
        if (!$orders) {
            return [];
        }

        $ids = array_map(fn(Order $order) => $order->id, $orders);
        $counts = $this->orderRepository->countItemsByOrderIds($ids);
        $payments = $this->orderRepository->paymentMethodsByOrderIds($ids);

        return array_map(function (Order $order) use ($counts, $payments) {
            $orderId = $order->id;
            return $order->withSummary(
                itemCount: $counts[$orderId] ?? 0,
                isPaid: isset($payments[$orderId]),
                paymentMethod: $payments[$orderId] ?? null,
            );
        }, $orders);
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

    /**
     * Orders for the payments page: unpaid first, then newest first,
     * excluding cancelled orders.
     *
     * @return array{orders: array<int, Order>, paidTotal: float, unpaidTotal: float, date: string}
     */
    public function getPaymentsData(?string $date = null): array
    {
        $date = $date ?? date('Y-m-d');

        $orders = array_values(array_filter(
            $this->enrich($this->orderRepository->findOrdersForDate($date)),
            fn(Order $order) => !$order->isCancelled(),
        ));

        usort($orders, static function (Order $a, Order $b): int {
            $paidCmp = ($a->isPaid ? 1 : 0) <=> ($b->isPaid ? 1 : 0);
            if ($paidCmp !== 0) {
                return $paidCmp;
            }
            return strtotime($b->createdAt ?? '0') <=> strtotime($a->createdAt ?? '0');
        });

        $paidTotal = 0.0;
        $unpaidTotal = 0.0;
        foreach ($orders as $order) {
            if ($order->isPaid) {
                $paidTotal += $order->totalAmount;
            } else {
                $unpaidTotal += $order->totalAmount;
            }
        }

        return [
            'orders' => $orders,
            'paidTotal' => $paidTotal,
            'unpaidTotal' => $unpaidTotal,
            'date' => $date,
        ];
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
    }

    /**
     * @return array{order: Order, items: array<int, OrderItem>}|null
     */
    public function getOrderBill(int $orderId): ?array
    {
        $order = $this->orderRepository->findById($orderId);
        if (!$order) {
            return null;
        }

        return [
            'order' => $this->enrich([$order])[0],
            'items' => $this->orderRepository->findItemsByOrderId($orderId),
        ];
    }
}

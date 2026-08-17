<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepository;

class KitchenService
{
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
    }

    /**
     * @return array{waiting: array<int, Order>, served: array<int, Order>, servedCount: int, items: array<int, array<int, OrderItem>>}
     */
    public function getKitchenData(): array
    {
        $waiting = $this->orderRepository->findWaitingForServiceToday();
        $served = $this->orderRepository->findServedToday();
        $items = [];

        foreach (array_merge($waiting, $served) as $order) {
            $items[$order->id] = $this->orderRepository->findItemsByOrderId($order->id);
        }

        return [
            'waiting' => $waiting,
            'served' => $served,
            'servedCount' => count($served),
            'items' => $items,
        ];
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function markServed(int $orderId): array
    {
        $order = $this->orderRepository->findById($orderId);

        if (!$order) {
            return ['success' => false, 'error' => 'Order not found.'];
        }

        if ($order->status !== 'PLACED') {
            return ['success' => false, 'error' => 'Only placed orders can be served.'];
        }

        $this->orderRepository->markServed($orderId);
        return ['success' => true];
    }
}
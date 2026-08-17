<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepository;

class KitchenService
{
    private OrderRepository $orderRepository;
    private string $station;

    public function __construct(string $station = 'KITCHEN')
    {
        $this->orderRepository = new OrderRepository();
        $this->station = $station;
    }

    /**
     * @return array{waiting: array<int, Order>, served: array<int, Order>, servedCount: int, items: array<int, array<int, OrderItem>>}
     */
    public function getKitchenData(): array
    {
        $waiting = $this->orderRepository->findWaitingForStationToday($this->station);
        $served = $this->orderRepository->findServedToday();
        $items = [];

        foreach ($waiting as $order) {
            $items[$order->id] = $this->orderRepository->findUnservedItemsByOrderIdAndStation($order->id, $this->station);
        }

        foreach ($served as $order) {
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

        $this->orderRepository->markStationServed($orderId, $this->station);
        return ['success' => true];
    }
}
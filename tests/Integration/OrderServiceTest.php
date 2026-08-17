<?php

namespace Tests\Integration;

use App\Core\Database;
use App\Services\OrderService;

class OrderServiceTest extends DatabaseTestCase
{
    private function countOrderItems(int $orderId): int
    {
        $stmt = Database::getConnection()->prepare('SELECT COUNT(*) FROM order_items WHERE order_id = ?');
        $stmt->execute([$orderId]);
        return (int) $stmt->fetchColumn();
    }

    private function findLastOrder(): array
    {
        return Database::getConnection()
            ->query('SELECT * FROM orders ORDER BY id DESC LIMIT 1')
            ->fetch();
    }

    public function testPlaceOrderComputesTotalAndPassesPlacedStatus(): void
    {
        $service = new OrderService();
        $result = $service->placeOrder(2, 'TAKEAWAY', 0, [
            ['menu_item_id' => 1, 'quantity' => 2],
            ['menu_item_id' => 2, 'quantity' => 1],
        ]);

        $this->assertTrue($result['success']);

        $order = $this->findLastOrder();
        $this->assertSame('PLACED', $order['status']);
        $this->assertSame('TAKEAWAY', $order['type']);
        $this->assertNull($order['table_id']);
        $this->assertSame(600.0, (float) $order['total_amount']);
        $this->assertSame(2, $this->countOrderItems((int) $order['id']));
    }

    public function testPlaceOrderWithDineInKeepsTableId(): void
    {
        $service = new OrderService();
        $result = $service->placeOrder(2, 'DINE_IN', 1, [
            ['menu_item_id' => 1, 'quantity' => 1],
        ]);

        $this->assertTrue($result['success']);

        $order = $this->findLastOrder();
        $this->assertSame(1, (int) $order['table_id']);
        $this->assertSame(250.0, (float) $order['total_amount']);
    }

    public function testPlaceOrderRejectsEmptyItems(): void
    {
        $service = new OrderService();
        $result = $service->placeOrder(2, 'TAKEAWAY', 0, []);

        $this->assertFalse($result['success']);
        $this->assertSame('Order has no items.', $result['error']);
    }

    public function testPlaceOrderRejectsInvalidMenuItem(): void
    {
        $service = new OrderService();
        $result = $service->placeOrder(2, 'TAKEAWAY', 0, [
            ['menu_item_id' => 999, 'quantity' => 1],
        ]);

        $this->assertFalse($result['success']);
        $this->assertSame('Invalid menu item selected.', $result['error']);
    }

    public function testPlaceOrderClampsQuantityToMinimumOne(): void
    {
        $service = new OrderService();
        $result = $service->placeOrder(2, 'TAKEAWAY', 0, [
            ['menu_item_id' => 2, 'quantity' => -5],
        ]);

        $this->assertTrue($result['success']);

        $order = $this->findLastOrder();
        $this->assertSame(100.0, (float) $order['total_amount']);

        $stmt = Database::getConnection()->prepare(
            'SELECT quantity FROM order_items WHERE order_id = ? AND menu_item_id = 2'
        );
        $stmt->execute([(int) $order['id']]);
        $this->assertSame(1, (int) $stmt->fetchColumn());
    }
}
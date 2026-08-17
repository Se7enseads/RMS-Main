<?php

namespace Tests\Integration;

use App\Core\Database;
use App\Services\KitchenService;

class KitchenServiceTest extends DatabaseTestCase
{
    private function insertOrder(int $id, string $status): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO orders (id, order_number, status, type, user_id, table_id, total_amount, created_at)
             VALUES (?, ?, ?, 'DINE_IN', 1, 1, 100.00, NOW())"
        );
        $stmt->execute([$id, "ORD-$id", $status]);
        return $id;
    }

    public function testMarkServedTransitionsPlacedOrder(): void
    {
        $this->insertOrder(101, 'PLACED');

        $service = new KitchenService();
        $result = $service->markServed(101);

        $this->assertTrue($result['success']);

        $status = Database::getConnection()
            ->query('SELECT status FROM orders WHERE id = 101')
            ->fetchColumn();
        $this->assertSame('SERVED', $status);
    }

    public function testMarkServedRejectsAlreadyServedOrder(): void
    {
        $this->insertOrder(102, 'SERVED');

        $service = new KitchenService();
        $result = $service->markServed(102);

        $this->assertFalse($result['success']);
        $this->assertSame('Only placed orders can be served.', $result['error']);
    }

    public function testMarkServedRejectsCancelledOrder(): void
    {
        $this->insertOrder(103, 'CANCELLED');

        $service = new KitchenService();
        $result = $service->markServed(103);

        $this->assertFalse($result['success']);
    }

    public function testMarkServedRejectsPaidOrder(): void
    {
        $this->insertOrder(104, 'PAYED');

        $service = new KitchenService();
        $result = $service->markServed(104);

        $this->assertFalse($result['success']);
    }

    public function testMarkServedRejectsMissingOrder(): void
    {
        $service = new KitchenService();
        $result = $service->markServed(999);

        $this->assertFalse($result['success']);
        $this->assertSame('Order not found.', $result['error']);
    }
}
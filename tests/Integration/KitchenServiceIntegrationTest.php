<?php

namespace Tests\Integration;

use App\Core\Database;
use App\Services\KitchenService;
use PDO;

class KitchenServiceIntegrationTest extends DatabaseTestCase
{
    private KitchenService $service;
    private PDO $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KitchenService();
        $this->db = Database::getConnection();
    }

    private function insertOrder(string $status): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (order_number, status, type, user_id, table_id, total_amount)
             VALUES (?, ?, ?, 1, 1, 100.00)'
        );
        $stmt->execute([uniqid('ORD-'), $status, 'DINE_IN']);
        return (int) $this->db->lastInsertId();
    }

    public function testMarkServedAgainstRealDatabase(): void
    {
        $orderId = $this->insertOrder('PLACED');

        $result = $this->service->markServed($orderId);

        $this->assertTrue($result['success']);
        $status = $this->db->query("SELECT status FROM orders WHERE id = $orderId")->fetchColumn();
        $this->assertSame('SERVED', $status);
    }

    public function testMarkServedRejectsAlreadyServedAgainstRealDatabase(): void
    {
        $orderId = $this->insertOrder('SERVED');

        $result = $this->service->markServed($orderId);

        $this->assertFalse($result['success']);
    }

    public function testGetKitchenDataSplitsWaitingAndServed(): void
    {
        $waitingId = $this->insertOrder('PLACED');
        $servedId = $this->insertOrder('SERVED');

        $this->db->exec(
            "INSERT INTO order_items (order_id, menu_item_id, price_at_time, quantity)
             VALUES ($waitingId, 1, 250.00, 1)"
        );

        $data = $this->service->getKitchenData();

        $this->assertCount(1, $data['waiting']);
        $this->assertSame($waitingId, $data['waiting'][0]->id);
        $this->assertCount(1, $data['served']);
        $this->assertSame($servedId, $data['served'][0]->id);
        $this->assertSame(1, $data['servedCount']);
        $this->assertArrayHasKey($waitingId, $data['items']);
        $this->assertArrayHasKey($servedId, $data['items']);
    }
}
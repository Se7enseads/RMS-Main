<?php

namespace Tests\Integration;

use App\Core\Database;
use App\Services\BarService;
use App\Services\KitchenService;
use PDO;

class StationSplitTest extends DatabaseTestCase
{
    private PDO $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Database::getConnection();
    }

    private function insertMixedOrder(): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (order_number, status, type, user_id, table_id, total_amount)
             VALUES (?, ?, ?, 1, 1, 550.00)'
        );
        $stmt->execute([uniqid('ORD-'), 'PLACED', 'DINE_IN']);
        $orderId = (int)$this->db->lastInsertId();

        $itemStmt = $this->db->prepare(
            'INSERT INTO order_items (order_id, menu_item_id, price_at_time, quantity) VALUES (?, ?, ?, 1)'
        );
        $itemStmt->execute([$orderId, 1, 250.00]); // Chicken Soup (KITCHEN)
        $itemStmt->execute([$orderId, 2, 100.00]); // Soda (BAR)
        $itemStmt->execute([$orderId, 3, 200.00]); // Drip Coffee (KITCHEN)

        return $orderId;
    }

    public function testKitchenAndBarShowOnlyTheirOwnItems(): void
    {
        $orderId = $this->insertMixedOrder();

        $kitchen = new KitchenService()->getKitchenData();
        $bar = new BarService()->getKitchenData();

        $this->assertCount(1, $kitchen['waiting']);
        $this->assertSame($orderId, $kitchen['waiting'][0]->id);

        $kitchenItems = $kitchen['items'][$orderId];
        $this->assertCount(2, $kitchenItems);
        $this->assertSame('Chicken Soup', $kitchenItems[0]->menuItemName);
        $this->assertSame('Drip Coffee', $kitchenItems[1]->menuItemName);

        $this->assertCount(1, $bar['waiting']);
        $this->assertSame($orderId, $bar['waiting'][0]->id);

        $barItems = $bar['items'][$orderId];
        $this->assertCount(1, $barItems);
        $this->assertSame('Soda', $barItems[0]->menuItemName);
    }

    public function testOrderOnlyServedAfterBothStationsMarkServed(): void
    {
        $orderId = $this->insertMixedOrder();

        $kitchenResult = new KitchenService()->markServed($orderId);
        $this->assertTrue($kitchenResult['success']);

        // kitchen items served, order still PLACED because soda is pending at the bar
        $served = $this->db->query(
            "SELECT COUNT(*) FROM order_items WHERE order_id = $orderId AND served = 1"
        )->fetchColumn();
        $this->assertSame(2, (int)$served);
        $this->assertSame('PLACED', $this->db->query("SELECT status FROM orders WHERE id = $orderId")->fetchColumn());

        // order no longer in the kitchen display but still on the bar display
        $kitchen = new KitchenService()->getKitchenData();
        $bar = new BarService()->getKitchenData();
        $this->assertSame([], $kitchen['waiting']);
        $this->assertCount(1, $bar['waiting']);

        $barResult = new BarService()->markServed($orderId);
        $this->assertTrue($barResult['success']);
        $this->assertSame('SERVED', $this->db->query("SELECT status FROM orders WHERE id = $orderId")->fetchColumn());
    }

    public function testServedHistoryShowsFullReceipt(): void
    {
        $orderId = $this->insertMixedOrder();

        new KitchenService()->markServed($orderId);
        new BarService()->markServed($orderId);

        $data = new KitchenService()->getKitchenData();

        $this->assertCount(1, $data['served']);
        $this->assertSame($orderId, $data['served'][0]->id);
        $this->assertCount(3, $data['items'][$orderId]);
    }

    public function testBarRejectsServedOrder(): void
    {
        $orderId = $this->insertMixedOrder();
        $this->db->exec("UPDATE orders SET status = 'SERVED' WHERE id = $orderId");

        $result = new BarService()->markServed($orderId);

        $this->assertFalse($result['success']);
    }
}
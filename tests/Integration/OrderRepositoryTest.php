<?php

namespace Tests\Integration;

use App\Core\Database;
use App\Repositories\OrderRepository;
use PDO;

class OrderRepositoryTest extends DatabaseTestCase
{
    private OrderRepository $repo;
    private PDO $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new OrderRepository();
        $this->db = Database::getConnection();
    }

    private function insertOrder(string $status, ?string $createdAtExpr = null, ?int $tableId = 1): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (order_number, status, type, user_id, table_id, total_amount, created_at)
             VALUES (?, ?, ?, 1, ?, ?, ' . ($createdAtExpr ?? 'NOW()') . ')'
        );
        $stmt->execute([uniqid('ORD-'), $status, 'DINE_IN', $tableId, 100.00]);
        return (int) $this->db->lastInsertId();
    }

    private function insertItem(int $orderId, int $menuItemId, float $price, int $quantity): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO order_items (order_id, menu_item_id, price_at_time, quantity) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$orderId, $menuItemId, $price, $quantity]);
    }

    public function testInsertWithItemsCreatesOrderAndItems(): void
    {
        $order = $this->repo->insertWithItems([
            'order_number' => 'ORD-20260815-TEST01',
            'status' => 'PLACED',
            'type' => 'TAKEAWAY',
            'user_id' => 2,
            'table_id' => null,
            'total_amount' => 600.00,
        ], [
            ['menu_item_id' => 1, 'price_at_time' => 250.00, 'quantity' => 2],
            ['menu_item_id' => 2, 'price_at_time' => 100.00, 'quantity' => 1],
        ]);

        $this->assertSame('ORD-20260815-TEST01', $order->orderNumber);
        $this->assertSame('PLACED', $order->status);
        $this->assertNull($order->tableId);

        $count = (int) $this->db->query(
            'SELECT COUNT(*) FROM order_items WHERE order_id = ' . $order->id
        )->fetchColumn();
        $this->assertSame(2, $count);
    }

    public function testInsertWithItemsRollsBackOnFailure(): void
    {
        $this->expectException(\Throwable::class);

        try {
            $this->repo->insertWithItems([
                'order_number' => 'ORD-ROLLBACK',
                'status' => 'PLACED',
                'type' => 'TAKEAWAY',
                'user_id' => 2,
                'table_id' => null,
                'total_amount' => 100.00,
            ], [
                ['menu_item_id' => 99999, 'price_at_time' => 100.00, 'quantity' => 1],
            ]);
        } finally {
            $count = (int) $this->db->query(
                "SELECT COUNT(*) FROM orders WHERE order_number = 'ORD-ROLLBACK'"
            )->fetchColumn();
            $this->assertSame(0, $count, 'Order must not exist after rollback');
        }
    }

    public function testFindWaitingForStationTodayReturnsOnlyPlacedToday(): void
    {
        $placedToday = $this->insertOrder('PLACED');                    // today, kitchen item -> included
        $this->insertItem($placedToday, 1, 250.00, 1);
        $this->insertOrder('PLACED', 'NOW() - INTERVAL 1 DAY');      // not today
        $this->insertOrder('SERVED');                                   // not placed
        $this->insertOrder('PAYED');                                    // not placed
        $this->insertOrder('CANCELLED');                                // not placed

        $waiting = $this->repo->findWaitingForStationToday('KITCHEN');

        $this->assertCount(1, $waiting);
        $this->assertSame('PLACED', $waiting[0]->status);
    }

    public function testFindWaitingForStationTodayIgnoresOtherStationItems(): void
    {
        // a PLACED order whose only item is a drink must NOT appear on the kitchen display
        $drinksOnly = $this->insertOrder('PLACED');
        $this->insertItem($drinksOnly, 2, 100.00, 1); // Soda (BAR)

        $kitchen = $this->repo->findWaitingForStationToday('KITCHEN');
        $this->assertSame([], $kitchen);

        $bar = $this->repo->findWaitingForStationToday('BAR');
        $this->assertCount(1, $bar);
        $this->assertSame($drinksOnly, $bar[0]->id);
    }

    public function testFindWaitingForStationTodayOrdersByOldestFirst(): void
    {
        $older = $this->insertOrder('PLACED', 'NOW() - INTERVAL 10 MINUTE');
        $newer = $this->insertOrder('PLACED');
        $this->insertItem($older, 1, 250.00, 1);
        $this->insertItem($newer, 1, 250.00, 1);

        $waiting = $this->repo->findWaitingForStationToday('KITCHEN');

        $this->assertCount(2, $waiting);
        $this->assertLessThan($waiting[1]->id, $waiting[0]->id);
    }

    public function testFindServedTodayOnlyServed(): void
    {
        $this->insertOrder('SERVED');
        $this->insertOrder('SERVED', 'NOW() - INTERVAL 1 DAY');
        $this->insertOrder('PLACED');

        $served = $this->repo->findServedToday();

        $this->assertCount(1, $served);
        $this->assertSame('SERVED', $served[0]->status);
    }

    public function testMarkStationServedTransitionsPlacedOrder(): void
    {
        $orderId = $this->insertOrder('PLACED');

        $this->repo->markStationServed($orderId, 'KITCHEN');
        $this->assertSame('SERVED', $this->repo->findById($orderId)->status);
    }

    public function testMarkStationServedKeepsOrderPlacedWhileOtherStationPending(): void
    {
        $orderId = $this->insertOrder('PLACED');
        $this->insertItem($orderId, 1, 250.00, 1); // KITCHEN
        $this->insertItem($orderId, 2, 100.00, 1); // BAR

        $this->repo->markStationServed($orderId, 'KITCHEN');
        $this->assertSame('PLACED', $this->repo->findById($orderId)->status);

        $served = (int) $this->db->query(
            "SELECT COUNT(*) FROM order_items WHERE order_id = $orderId AND served = 1"
        )->fetchColumn();
        $this->assertSame(1, $served);

        $this->repo->markStationServed($orderId, 'BAR');
        $this->assertSame('SERVED', $this->repo->findById($orderId)->status);
    }

    public function testMarkStationServedRejectsNonPlacedOrders(): void
    {
        foreach (['SERVED', 'PAYED', 'CANCELLED'] as $status) {
            $orderId = $this->insertOrder($status);
            $this->insertItem($orderId, 1, 250.00, 1);

            $this->repo->markStationServed($orderId, 'KITCHEN');

            $served = (int) $this->db->query(
                "SELECT COUNT(*) FROM order_items WHERE order_id = $orderId AND served = 1"
            )->fetchColumn();
            $this->assertSame(0, $served, "Should not serve $status order");
            $this->assertSame($status, $this->repo->findById($orderId)->status);
        }
    }

    public function testFindItemsByOrderIdJoinsNames(): void
    {
        $orderId = $this->insertOrder('PLACED');
        $this->insertItem($orderId, 1, 250.00, 2);
        $this->insertItem($orderId, 2, 100.00, 1);

        $items = $this->repo->findItemsByOrderId($orderId);

        $this->assertCount(2, $items);
        $this->assertSame('Chicken Soup', $items[0]->menuItemName);
        $this->assertSame(2, $items[0]->quantity);
        $this->assertSame(250.00, $items[0]->priceAtTime);
        $this->assertSame('Soda', $items[1]->menuItemName);
    }

    public function testFindByIdReturnsNullForMissingOrder(): void
    {
        $this->assertNull($this->repo->findById(999999));
    }
}
<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Order;
use App\Models\OrderItem;
use PDO;
use Throwable;

class OrderRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findOpenOrders(): array
    {
        $stmt = $this->db->query($this->orderSelect() . " WHERE o.status = 'PLACED' ORDER BY o.created_at DESC");

        return array_map([Order::class, 'fromRow'], $stmt->fetchAll());
    }

    public function findOrdersForDate(string $date): array
    {
        $stmt = $this->db->prepare($this->orderSelect() . " WHERE DATE(o.created_at) = :date ORDER BY o.created_at DESC");
        $stmt->execute(['date' => $date]);

        return array_map([Order::class, 'fromRow'], $stmt->fetchAll());
    }

    /**
     * @return array<int, Order>
     */
    public function findOrdersByUserId(int $userId): array
    {
        $stmt = $this->db->prepare($this->orderSelect() . " WHERE o.user_id = :user_id ORDER BY o.created_at DESC");
        $stmt->execute(['user_id' => $userId]);

        return array_map([Order::class, 'fromRow'], $stmt->fetchAll());
    }

    public function countOpenOrders(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM orders WHERE status = 'PLACED'");
        return (int)$stmt->fetchColumn();
    }

    public function findWaitingForStationToday(string $station): array
    {
        $sql = $this->orderSelect() . "
            WHERE o.status = 'PLACED'
              AND DATE(o.created_at) = CURDATE()
              AND EXISTS (
                  SELECT 1
                  FROM order_items oi
                  INNER JOIN menu_items mi ON mi.id = oi.menu_item_id
                  INNER JOIN menu_categories mc ON mc.id = mi.category_id
                  WHERE oi.order_id = o.id
                    AND oi.served = 0
                    AND mc.station = :station
              )
            ORDER BY o.created_at ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['station' => $station]);

        return array_map([Order::class, 'fromRow'], $stmt->fetchAll());
    }

    public function findServedToday(): array
    {
        $stmt = $this->db->query($this->orderSelect() . "
            WHERE o.status = 'SERVED'
              AND DATE(o.created_at) = CURDATE()
            ORDER BY o.created_at DESC
        ");

        return array_map([Order::class, 'fromRow'], $stmt->fetchAll());
    }

    /**
     * @return array<int, OrderItem>
     */
    public function findItemsByOrderId(int $orderId): array
    {
        $sql = "
            SELECT oi.*, mi.name AS menu_item_name
            FROM order_items oi
            INNER JOIN menu_items mi ON mi.id = oi.menu_item_id
            WHERE oi.order_id = :order_id
            ORDER BY oi.id ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);

        return array_map([OrderItem::class, 'fromRow'], $stmt->fetchAll());
    }

    /**
     * Marks the station's items as served and transitions the order to
     * SERVED once every item on the order has been served.
     */
    public function markStationServed(int $orderId, string $station): void
    {
        $stmt = $this->db->prepare("
            UPDATE order_items oi
            INNER JOIN menu_items mi ON mi.id = oi.menu_item_id
            INNER JOIN menu_categories mc ON mc.id = mi.category_id
            SET oi.served = 1
            WHERE oi.order_id = :order_id
              AND oi.served = 0
              AND mc.station = :station
              AND EXISTS (
                  SELECT 1 FROM orders o
                  WHERE o.id = oi.order_id AND o.status = 'PLACED'
              )
        ");
        $stmt->execute(['order_id' => $orderId, 'station' => $station]);

        $anyUnserved = $this->db->prepare("
            SELECT COUNT(*)
            FROM order_items oi
            WHERE oi.order_id = :order_id AND oi.served = 0
        ");
        $anyUnserved->execute(['order_id' => $orderId]);

        if ((int)$anyUnserved->fetchColumn() > 0) {
            return;
        }

        $orderStmt = $this->db->prepare("
            UPDATE orders
            SET status = 'SERVED'
            WHERE id = :id AND status = 'PLACED'
        ");
        $orderStmt->execute(['id' => $orderId]);
    }

    /**
     * @return array<int, OrderItem>
     */
    public function findUnservedItemsByOrderIdAndStation(int $orderId, string $station): array
    {
        $sql = "
            SELECT oi.*, mi.name AS menu_item_name
            FROM order_items oi
            INNER JOIN menu_items mi ON mi.id = oi.menu_item_id
            INNER JOIN menu_categories mc ON mc.id = mi.category_id
            WHERE oi.order_id = :order_id
              AND oi.served = 0
              AND mc.station = :station
            ORDER BY oi.id ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $orderId, 'station' => $station]);

        return array_map([OrderItem::class, 'fromRow'], $stmt->fetchAll());
    }

    public function countOrdersForDate(string $date): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = :date");
        $stmt->execute(['date' => $date]);
        return (int)$stmt->fetchColumn();
    }

    public function sumRevenueForDate(string $date): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0)
             FROM payments
             WHERE DATE(created_at) = :date AND method <> 'REFUND'"
        );
        $stmt->execute(['date' => $date]);
        return (float)$stmt->fetchColumn();
    }

    public function findById(int $id): ?Order
    {
        $stmt = $this->db->prepare($this->orderSelect() . " WHERE o.id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Order::fromRow($row) : null;
    }

    private function orderSelect(): string
    {
        return "
            SELECT o.*, t.number AS table_number, u.first_name AS user_name
            FROM orders o
            LEFT JOIN tables t ON o.table_id = t.id
            LEFT JOIN users u ON o.user_id = u.id
        ";
    }

    /**
     * @param array<int, int> $orderIds
     * @return array<int, int> order_id => item count
     */
    public function countItemsByOrderIds(array $orderIds): array
    {
        if (!$orderIds) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $stmt = $this->db->prepare("
            SELECT order_id, COUNT(*) AS item_count
            FROM order_items
            WHERE order_id IN ($placeholders)
            GROUP BY order_id
        ");
        $stmt->execute(array_values($orderIds));

        $counts = [];
        foreach ($stmt->fetchAll() as $row) {
            $counts[(int) $row['order_id']] = (int) $row['item_count'];
        }

        return $counts;
    }

    /**
     * @param array<int, int> $orderIds
     * @return array<int, string> order_id => payment method
     */
    public function paymentMethodsByOrderIds(array $orderIds): array
    {
        if (!$orderIds) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $stmt = $this->db->prepare("
            SELECT order_id, method
            FROM payments
            WHERE order_id IN ($placeholders)
            GROUP BY order_id
        ");
        $stmt->execute(array_values($orderIds));

        $methods = [];
        foreach ($stmt->fetchAll() as $row) {
            $methods[(int) $row['order_id']] = $row['method'];
        }

        return $methods;
    }

    /**
     * Insert an order and its items in a transaction.
     *
     * @param array{order_number: string, status: string, type: string, user_id: int, table_id: int|null, total_amount: float} $order
     * @param array<int, array{menu_item_id: int, price_at_time: float, quantity: int}> $items
     * @throws Throwable
     */
    public function insertWithItems(array $order, array $items): Order
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("
                INSERT INTO orders (order_number, status, type, user_id, table_id, total_amount)
                VALUES (:order_number, :status, :type, :user_id, :table_id, :total_amount)
            ");
            $stmt->execute($order);

            $orderId = (int)$this->db->lastInsertId();

            $itemStmt = $this->db->prepare("
                INSERT INTO order_items (order_id, menu_item_id, price_at_time, quantity)
                VALUES (:order_id, :menu_item_id, :price_at_time, :quantity)
            ");
            foreach ($items as $item) {
                $itemStmt->execute([
                    'order_id' => $orderId,
                    'menu_item_id' => $item['menu_item_id'],
                    'price_at_time' => $item['price_at_time'],
                    'quantity' => $item['quantity'],
                ]);
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $this->findById($orderId);
    }
}

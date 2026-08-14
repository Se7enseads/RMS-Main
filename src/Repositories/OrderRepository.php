<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Order;
use PDO;

class OrderRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findOpenOrders(): array
    {
        $sql = "
            SELECT o.*, t.number AS table_number, u.first_name AS user_name,
                   EXISTS(SELECT 1 FROM payments p WHERE p.order_id = o.id) AS is_paid,
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
            FROM orders o
            LEFT JOIN tables t ON o.table_id = t.id
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.status IN ('OPEN', 'PLACED')
            ORDER BY o.created_at DESC
        ";
        $stmt = $this->db->query($sql);

        return array_map([Order::class, 'fromRow'], $stmt->fetchAll());
    }

    public function findOrdersForDate(string $date): array
    {
        $sql = "
            SELECT o.*, t.number AS table_number, u.first_name AS user_name,
                   EXISTS(SELECT 1 FROM payments p WHERE p.order_id = o.id) AS is_paid,
                   (SELECT p.method FROM payments p WHERE p.order_id = o.id LIMIT 1) AS payment_method,
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
            FROM orders o
            LEFT JOIN tables t ON o.table_id = t.id
            LEFT JOIN users u ON o.user_id = u.id
            WHERE DATE(o.created_at) = :date
            ORDER BY o.created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['date' => $date]);

        return array_map([Order::class, 'fromRow'], $stmt->fetchAll());
    }

    public function countOpenOrders(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM orders WHERE status IN ('OPEN', 'PLACED')");
        return (int) $stmt->fetchColumn();
    }

    public function countOrdersForDate(string $date): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = :date");
        $stmt->execute(['date' => $date]);
        return (int) $stmt->fetchColumn();
    }

    public function sumRevenueForDate(string $date): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0)
             FROM payments
             WHERE DATE(created_at) = :date AND method <> 'REFUND'"
        );
        $stmt->execute(['date' => $date]);
        return (float) $stmt->fetchColumn();
    }

    public function findById(int $id): ?Order
    {
        $sql = "
            SELECT o.*, t.number AS table_number, u.first_name AS user_name
            FROM orders o
            LEFT JOIN tables t ON o.table_id = t.id
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Order::fromRow($row) : null;
    }

    /**
     * Insert an order and its items in a transaction.
     *
     * @param array{order_number: string, status: string, type: string, user_id: int, table_id: int, total_amount: float} $order
     * @param array<int, array{menu_item_id: int, price_at_time: float, quantity: int}> $items
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

            $orderId = (int) $this->db->lastInsertId();

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
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $this->findById($orderId);
    }
}

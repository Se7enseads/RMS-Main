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

    /**
     * Open (PLACED) orders placed by a single user, newest first.
     *
     * @return array<int, Order>
     */
    public function findOpenOrdersByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            $this->orderSelect() . " WHERE o.status = 'PLACED' AND o.user_id = :user_id ORDER BY o.created_at DESC"
        );
        $stmt->execute(['user_id' => $userId]);

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

    /**
     * Non-cancelled, unpaid orders for a date, oldest first (cashier queue).
     *
     * @return array<int, Order>
     */
    public function findUnpaidByDate(string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT o.*, t.number AS table_number, u.first_name AS user_name,
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count,
                   0 AS is_paid,
                   NULL AS payment_method
            FROM orders o
            LEFT JOIN tables t ON o.table_id = t.id
            LEFT JOIN staff u ON o.user_id = u.id
            WHERE o.status <> 'CANCELLED'
              AND NOT EXISTS (
                  SELECT 1 FROM payments p WHERE p.order_id = o.id AND p.method <> 'REFUND'
              )
              AND DATE(o.created_at) = :date
            ORDER BY o.created_at ASC
        ");
        $stmt->execute(['date' => $date]);

        return array_map([Order::class, 'fromRow'], $stmt->fetchAll());
    }

    /**
     * Paid (settled) orders for a date, newest first.
     *
     * @return array<int, Order>
     */
    public function findPaidByDate(string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT o.*, t.number AS table_number, u.first_name AS user_name,
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count,
                   1 AS is_paid,
                   (SELECT p.method FROM payments p WHERE p.order_id = o.id ORDER BY p.id DESC LIMIT 1) AS payment_method
            FROM orders o
            LEFT JOIN tables t ON o.table_id = t.id
            LEFT JOIN staff u ON o.user_id = u.id
            WHERE o.status = 'PAYED'
              AND DATE(o.created_at) = :date
            ORDER BY o.created_at DESC
        ");
        $stmt->execute(['date' => $date]);

        return array_map([Order::class, 'fromRow'], $stmt->fetchAll());
    }

    /**
     * Insert a payment for an order, mark it PAID and stamp closed_at
     * (all in one transaction).
     */
    public function recordPayment(int $orderId, string $method, float $amount, int $cashierId, ?string $transactionCode = null): void
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare('
                INSERT INTO payments (transaction_code, method, amount, order_id, cashier_id)
                VALUES (:transaction_code, :method, :amount, :order_id, :cashier_id)
            ');
            $stmt->execute([
                'transaction_code' => $transactionCode,
                'method' => $method,
                'amount' => $amount,
                'order_id' => $orderId,
                'cashier_id' => $cashierId,
            ]);

            $update = $this->db->prepare("
                UPDATE orders SET status = 'PAYED', closed_at = NOW() WHERE id = :id
            ");
            $update->execute(['id' => $orderId]);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
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
     * @param array<int, int> $orderIds
     * @return array<int, array<int, OrderItem>> order_id => items
     */
    public function findItemsByOrderIds(array $orderIds): array
    {
        if (!$orderIds) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $stmt = $this->db->prepare("
            SELECT oi.*, mi.name AS menu_item_name
            FROM order_items oi
            INNER JOIN menu_items mi ON mi.id = oi.menu_item_id
            WHERE oi.order_id IN ($placeholders)
            ORDER BY oi.id ASC
        ");
        $stmt->execute(array_values($orderIds));

        $itemsByOrder = [];
        foreach ($stmt->fetchAll() as $row) {
            $itemsByOrder[(int) $row['order_id']][] = OrderItem::fromRow($row);
        }

        return $itemsByOrder;
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

    /**
     * Sales grouped by day for a date range (view v_sales_by_day).
     *
     * @return array<int, array{day: string, orders: int, items: int, revenue: float, paid: float, unpaid: float}>
     */
    public function salesByDay(string $from, string $to): array
    {
        $stmt = $this->db->prepare("
            SELECT day, orders, items, revenue, paid, unpaid
            FROM v_sales_by_day
            WHERE day BETWEEN :from AND :to
            ORDER BY day ASC
        ");
        $stmt->execute(['from' => $from, 'to' => $to]);

        return array_map(static function (array $row): array {
            return [
                'day' => (string) $row['day'],
                'orders' => (int) $row['orders'],
                'items' => (int) $row['items'],
                'revenue' => (float) $row['revenue'],
                'paid' => (float) $row['paid'],
                'unpaid' => (float) $row['unpaid'],
            ];
        }, $stmt->fetchAll());
    }

    /**
     * Sales grouped by menu item for a date range (view v_item_sales_by_day).
     *
     * @return array<int, array{item: string, category: string, quantity: int, revenue: float}>
     */
    public function itemSales(string $from, string $to): array
    {
        $stmt = $this->db->prepare("
            SELECT item, category,
                   SUM(quantity) AS quantity,
                   SUM(revenue) AS revenue
            FROM v_item_sales_by_day
            WHERE day BETWEEN :from AND :to
            GROUP BY item, category
            ORDER BY revenue DESC, quantity DESC
        ");
        $stmt->execute(['from' => $from, 'to' => $to]);

        return array_map(static function (array $row): array {
            return [
                'item' => (string) $row['item'],
                'category' => (string) $row['category'],
                'quantity' => (int) $row['quantity'],
                'revenue' => (float) $row['revenue'],
            ];
        }, $stmt->fetchAll());
    }

    /**
     * Payments grouped by method for a date range (view v_payments_by_day).
     *
     * @return array<int, array{method: string, count: int, total: float}>
     */
    public function paymentMethodSummary(string $from, string $to): array
    {
        $stmt = $this->db->prepare("
            SELECT method,
                   SUM(count) AS count,
                   SUM(total) AS total
            FROM v_payments_by_day
            WHERE day BETWEEN :from AND :to
            GROUP BY method
            ORDER BY total DESC
        ");
        $stmt->execute(['from' => $from, 'to' => $to]);

        return array_map(static function (array $row): array {
            return [
                'method' => (string) $row['method'],
                'count' => (int) $row['count'],
                'total' => (float) $row['total'],
            ];
        }, $stmt->fetchAll());
    }

    /**
     * Sales grouped by menu category for a date range (view v_item_sales_by_day).
     *
     * @return array<int, array{category: string, quantity: int, revenue: float}>
     */
    public function categorySales(string $from, string $to): array
    {
        $stmt = $this->db->prepare("
            SELECT category,
                   SUM(quantity) AS quantity,
                   SUM(revenue) AS revenue
            FROM v_item_sales_by_day
            WHERE day BETWEEN :from AND :to
            GROUP BY category
            ORDER BY revenue DESC
        ");
        $stmt->execute(['from' => $from, 'to' => $to]);

        return array_map(static function (array $row): array {
            return [
                'category' => (string) $row['category'],
                'quantity' => (int) $row['quantity'],
                'revenue' => (float) $row['revenue'],
            ];
        }, $stmt->fetchAll());
    }

    /**
     * Order counts and revenue per hour of day for a date range (excluding cancelled orders).
     *
     * @return array<int, array{hour: int, orders: int, revenue: float}>
     */
    public function hourlySales(string $from, string $to): array
    {
        $stmt = $this->db->prepare("
            SELECT HOUR(created_at)                  AS hour,
                   COUNT(*)                          AS orders,
                   COALESCE(SUM(total_amount), 0)    AS revenue
            FROM orders
            WHERE status <> 'CANCELLED'
              AND DATE(created_at) BETWEEN :from AND :to
            GROUP BY HOUR(created_at)
            ORDER BY hour ASC
        ");
        $stmt->execute(['from' => $from, 'to' => $to]);

        return array_map(static function (array $row): array {
            return [
                'hour' => (int) $row['hour'],
                'orders' => (int) $row['orders'],
                'revenue' => (float) $row['revenue'],
            ];
        }, $stmt->fetchAll());
    }

    /**
     * Order counts grouped by status for a date range.
     *
     * @return array<int, array{status: string, count: int}>
     */
    public function statusSummary(string $from, string $to): array
    {
        $stmt = $this->db->prepare("
            SELECT status,
                   COUNT(*) AS count
            FROM orders
            WHERE DATE(created_at) BETWEEN :from AND :to
            GROUP BY status
            ORDER BY count DESC, status ASC
        ");
        $stmt->execute(['from' => $from, 'to' => $to]);

        return array_map(static function (array $row): array {
            return [
                'status' => (string) $row['status'],
                'count' => (int) $row['count'],
            ];
        }, $stmt->fetchAll());
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
            LEFT JOIN staff u ON o.user_id = u.id
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

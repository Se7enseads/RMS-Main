<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;
use Throwable;

class StockTakeRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * @param array<string,mixed> $item system_qty, counted_qty, variance_qty,
     *                                    unit_cost, variance_value
     * @throws Throwable
     */
    public function createTake(
        string $scope,
        string $takeDate,
        int $performedBy,
        array $items
    ): int {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("
                INSERT INTO stock_takes (scope, take_date, performed_by)
                VALUES (:scope, :take_date, :performed_by)
            ");
            $stmt->execute([
                'scope' => $scope,
                'take_date' => $takeDate,
                'performed_by' => $performedBy,
            ]);
            $takeId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare("
                INSERT INTO stock_take_items
                    (stock_take_id, inventory_id, system_qty, counted_qty,
                     variance_qty, unit_cost, variance_value)
                VALUES
                    (:take_id, :inventory_id, :system_qty, :counted_qty,
                     :variance_qty, :unit_cost, :variance_value)
            ");
            $movementStmt = $this->db->prepare("
                INSERT INTO inventory_movements
                    (inventory_id, movement_type, quantity, reference_type,
                     reference_id, performed_by, unit, unit_cost)
                VALUES
                    (:inventory_id, 'ADJUSTMENT', :quantity, 'STOCK_TAKE',
                     :reference_id, :performed_by, :unit, :unit_cost)
            ");
            $stockStmt = $this->db->prepare("UPDATE inventory SET stock = :stock WHERE id = :id");

            foreach ($items as $item) {
                $itemStmt->execute([
                    'take_id' => $takeId,
                    'inventory_id' => $item['inventory_id'],
                    'system_qty' => $item['system_qty'],
                    'counted_qty' => $item['counted_qty'],
                    'variance_qty' => $item['variance_qty'],
                    'unit_cost' => $item['unit_cost'],
                    'variance_value' => $item['variance_value'],
                ]);

                $movementStmt->execute([
                    'inventory_id' => $item['inventory_id'],
                    'quantity' => $item['variance_qty'],
                    'reference_id' => $takeId,
                    'performed_by' => $performedBy,
                    'unit' => $item['base_unit'],
                    'unit_cost' => $item['unit_cost'],
                ]);

                $stockStmt->execute([
                    'stock' => $item['counted_qty'],
                    'id' => $item['inventory_id'],
                ]);
            }

            $this->db->commit();
            return $takeId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @return array<int, array<string,mixed>>
     */
    public function findTakes(?string $scope = null): array
    {
        $sql = "
            SELECT st.id, st.scope, st.take_date, st.performed_by, st.notes, st.created_at,
                   CONCAT(s.first_name, ' ', s.last_name) AS staff_name,
                   COUNT(sti.id) AS item_count,
                   COALESCE(SUM(sti.variance_value), 0) AS total_variance
            FROM stock_takes st
            JOIN staff s ON s.id = st.performed_by
            LEFT JOIN stock_take_items sti ON sti.stock_take_id = st.id
        ";
        if ($scope !== null) {
            $sql .= " WHERE st.scope = :scope";
        }
        $sql .= " GROUP BY st.id ORDER BY st.created_at DESC, st.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($scope !== null ? ['scope' => $scope] : []);

        return $stmt->fetchAll();
    }

    /**
     * @return array<int, array<string,mixed>>
     */
    public function findTakeItems(int $takeId): array
    {
        $stmt = $this->db->prepare("
            SELECT sti.*, i.name, i.base_unit, i.receive_unit, i.units_per_container
            FROM stock_take_items sti
            JOIN inventory i ON i.id = sti.inventory_id
            WHERE sti.stock_take_id = :take_id
            ORDER BY i.name
        ");
        $stmt->execute(['take_id' => $takeId]);

        return $stmt->fetchAll();
    }
}

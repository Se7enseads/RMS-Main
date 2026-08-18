<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Ingredient;
use PDO;
use Throwable;

class IngredientRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * @return array<int, Ingredient>
     */
    public function findAllActive(): array
    {
        $stmt = $this->db->query("SELECT * FROM inventory WHERE active = 1 ORDER BY name");

        return array_map([Ingredient::class, 'fromRow'], $stmt->fetchAll());
    }

    /**
     * @return array<int, Ingredient>
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM inventory ORDER BY name");

        return array_map([Ingredient::class, 'fromRow'], $stmt->fetchAll());
    }

    public function findById(int $id): ?Ingredient
    {
        $stmt = $this->db->prepare("SELECT * FROM inventory WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Ingredient::fromRow($row) : null;
    }

    public function findByName(string $name): ?Ingredient
    {
        $stmt = $this->db->prepare("SELECT * FROM inventory WHERE name = :name");
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch();

        return $row ? Ingredient::fromRow($row) : null;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function insert(array $data): Ingredient
    {
        $stmt = $this->db->prepare("
            INSERT INTO inventory (name, base_unit, receive_unit, units_per_container, reorder_level, active)
            VALUES (:name, :base_unit, :receive_unit, :units_per_container, :reorder_level, 1)
        ");
        $stmt->execute([
            'name' => $data['name'],
            'base_unit' => $data['base_unit'],
            'receive_unit' => $data['receive_unit'],
            'units_per_container' => !empty($data['units_per_container']) ? (float) $data['units_per_container'] : null,
            'reorder_level' => !empty($data['reorder_level']) ? (float) $data['reorder_level'] : 0,
        ]);

        return $this->findById((int) $this->db->lastInsertId());
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare("
            UPDATE inventory
            SET name = :name, base_unit = :base_unit, receive_unit = :receive_unit,
                units_per_container = :units_per_container, reorder_level = :reorder_level
            WHERE id = :id
        ");
        $stmt->execute([
            'name' => $data['name'],
            'base_unit' => $data['base_unit'],
            'receive_unit' => $data['receive_unit'],
            'units_per_container' => !empty($data['units_per_container']) ? (float) $data['units_per_container'] : null,
            'reorder_level' => !empty($data['reorder_level']) ? (float) $data['reorder_level'] : 0,
            'id' => $id,
        ]);
    }

    public function setActive(int $id, bool $active): void
    {
        $stmt = $this->db->prepare("UPDATE inventory SET active = :active WHERE id = :id");
        $stmt->execute(['active' => $active ? 1 : 0, 'id' => $id]);
    }

    /**
     * Active ingredients for a stock take. BAR scope: ingredients used by
     * active menu items in BAR-station categories.
     *
     * @return array<int, Ingredient>
     */
    public function getStockTakeList(?string $scope = 'ALL'): array
    {
        $sql = "SELECT * FROM inventory WHERE active = 1";
        if ($scope === 'BAR') {
            $sql .= " AND id IN (
                SELECT DISTINCT mii.inventory_id
                FROM menu_item_ingredients mii
                JOIN menu_items mi ON mi.id = mii.menu_item_id
                JOIN menu_categories mc ON mc.id = mi.category_id
                WHERE mc.station = 'BAR' AND mi.active = 1 AND mc.active = 1
            )";
        }
        $sql .= " ORDER BY name";

        $stmt = $this->db->query($sql);

        return array_map([Ingredient::class, 'fromRow'], $stmt->fetchAll());
    }

    /**
     * @return array<int, float> inventory_id => last recorded variance (base units)
     */
    public function getLastVarianceMap(): array
    {
        $stmt = $this->db->query("
            SELECT si.inventory_id, si.variance_qty
            FROM stock_take_items si
            JOIN (
                SELECT inventory_id, MAX(id) AS max_id
                FROM stock_take_items
                GROUP BY inventory_id
            ) last ON last.max_id = si.id
        ");

        $map = [];
        foreach ($stmt->fetchAll() as $row) {
            $map[(int) $row['inventory_id']] = (float) $row['variance_qty'];
        }

        return $map;
    }

    /**
     * Add stock in receive units, recording a movement and updating the
     * weighted-average cost per base unit.
     *
     * @throws Throwable
     */
    public function addStock(int $ingredientId, float $quantity, string $unit, float $unitCost, int $performedBy): void
    {
        $this->db->beginTransaction();

        try {
            $ingredient = $this->findById($ingredientId);
            if (!$ingredient) {
                throw new \RuntimeException('Ingredient not found.');
            }

            $unitsPerContainer = $ingredient->unitsPerContainer ?? 1.0;
            $toBase = $unit === $ingredient->baseUnit ? 1.0 : $unitsPerContainer;
            $baseAdded = $quantity * $toBase;
            $costPerBase = $unitCost / $toBase;

            $newStock = $ingredient->stock + $baseAdded;
            $newCostPerUnit = $newStock > 0
                ? (($ingredient->stock * $ingredient->costPerUnit) + ($baseAdded * $costPerBase)) / $newStock
                : 0.0;

            $stmt = $this->db->prepare("
                UPDATE inventory
                SET stock = :new_stock, cost_per_unit = :new_cost
                WHERE id = :id
            ");
            $stmt->execute([
                'new_stock' => $newStock,
                'new_cost' => $newCostPerUnit,
                'id' => $ingredientId,
            ]);

            $movement = $this->db->prepare("
                INSERT INTO inventory_movements (inventory_id, movement_type, quantity, reference_type, performed_by, unit, unit_cost)
                VALUES (:inventory_id, 'IN', :quantity, 'MANUAL', :performed_by, :unit, :unit_cost)
            ");
            $movement->execute([
                'inventory_id' => $ingredientId,
                'quantity' => $quantity,
                'performed_by' => $performedBy,
                'unit' => $unit,
                'unit_cost' => $unitCost,
            ]);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
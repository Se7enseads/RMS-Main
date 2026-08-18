<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Ingredient;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use PDO;
use Throwable;

class MenuRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAllActiveCategories(?string $station = null): array
    {
        $sql = "SELECT * FROM menu_categories WHERE active = 1";
        if ($station !== null) {
            $sql .= " AND station = :station";
        }
        $sql .= " ORDER BY name";

        $stmt = $this->db->prepare($sql);
        if ($station !== null) {
            $stmt->bindValue('station', $station);
        }
        $stmt->execute();

        return array_map([MenuCategory::class, 'fromRow'], $stmt->fetchAll());
    }

    public function findAllCategories(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM menu_categories ORDER BY name"
        );

        return array_map([MenuCategory::class, 'fromRow'], $stmt->fetchAll());
    }

    public function findCategoryById(int $id): ?MenuCategory
    {
        $stmt = $this->db->prepare("SELECT * FROM menu_categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? MenuCategory::fromRow($row) : null;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function insertCategory(array $data): MenuCategory
    {
        $stmt = $this->db->prepare(
            "INSERT INTO menu_categories (name, station, active) VALUES (:name, :station, 1)"
        );
        $stmt->execute([
            'name' => $data['name'],
            'station' => $data['station'],
        ]);

        return $this->findCategoryById((int) $this->db->lastInsertId());
    }

    /**
     * @param array<string,mixed> $data
     */
    public function updateCategory(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE menu_categories SET name = :name, station = :station WHERE id = :id"
        );
        $stmt->execute([
            'name' => $data['name'],
            'station' => $data['station'],
            'id' => $id,
        ]);
    }

    public function setCategoryActive(int $id, bool $active): void
    {
        $stmt = $this->db->prepare("UPDATE menu_categories SET active = :active WHERE id = :id");
        $stmt->execute(['active' => $active ? 1 : 0, 'id' => $id]);
    }

    /**
     * Replace the recipe for a menu item.
     *
     * @param array<int, array{ingredient_id: int, quantity: float}> $rows
     */
    public function setRecipe(int $menuItemId, array $rows): void
    {
        $this->db->beginTransaction();

        try {
            $delete = $this->db->prepare("DELETE FROM menu_item_ingredients WHERE menu_item_id = :menu_item_id");
            $delete->execute(['menu_item_id' => $menuItemId]);

            $insert = $this->db->prepare(
                "INSERT INTO menu_item_ingredients (menu_item_id, inventory_id, quantity, unit)
                 VALUES (:menu_item_id, :ingredient_id, :quantity, :unit)"
            );
            foreach ($rows as $row) {
                $ingredient = $this->findIngredientById((int) $row['ingredient_id']);
                if (!$ingredient) {
                    continue;
                }
                $insert->execute([
                    'menu_item_id' => $menuItemId,
                    'ingredient_id' => $row['ingredient_id'],
                    'quantity' => $row['quantity'],
                    'unit' => $ingredient->baseUnit,
                ]);
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @return array<int, array{ingredient_id: int, name: string, base_unit: string, quantity: float, unit: string, cost_per_unit: float}>
     */
    public function findRecipeByItemId(int $menuItemId): array
    {
        $stmt = $this->db->prepare("
            SELECT mii.inventory_id AS ingredient_id, i.name, i.base_unit, i.cost_per_unit,
                   mii.quantity, mii.unit
            FROM menu_item_ingredients mii
            INNER JOIN inventory i ON i.id = mii.inventory_id
            WHERE mii.menu_item_id = :menu_item_id
            ORDER BY i.name
        ");
        $stmt->execute(['menu_item_id' => $menuItemId]);

        return array_map(
            fn(array $row) => [
                'ingredient_id' => (int) $row['ingredient_id'],
                'name' => $row['name'],
                'base_unit' => $row['base_unit'],
                'quantity' => (float) $row['quantity'],
                'unit' => $row['unit'],
                'cost_per_unit' => (float) $row['cost_per_unit'],
            ],
            $stmt->fetchAll()
        );
    }

    /**
     * Ingredient cost per menu item, keyed by item id.
     *
     * @return array<int, float>
     */
    public function findRecipeCosts(): array
    {
        $stmt = $this->db->query("
            SELECT mii.menu_item_id, SUM(mii.quantity * i.cost_per_unit) AS cost
            FROM menu_item_ingredients mii
            INNER JOIN inventory i ON i.id = mii.inventory_id
            GROUP BY mii.menu_item_id
        ");

        $costs = [];
        foreach ($stmt->fetchAll() as $row) {
            $costs[(int) $row['menu_item_id']] = (float) $row['cost'];
        }

        return $costs;
    }

    public function findIngredientById(int $id): ?Ingredient
    {
        $stmt = $this->db->prepare("SELECT * FROM inventory WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Ingredient::fromRow($row) : null;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function insertItem(array $data): MenuItem
    {
        $sql = "INSERT INTO menu_items (name, description, price, category_id, is_combo, active)
                VALUES (:name, :description, :price, :category_id, :is_combo, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'category_id' => $data['category_id'] ? (int) $data['category_id'] : null,
            'is_combo' => !empty($data['is_combo']) ? 1 : 0,
        ]);

        return $this->findItemById((int) $this->db->lastInsertId());
    }

    public function findAllActiveItems(?string $station = null): array
    {
        $sql = "
            SELECT mi.*, mc.name AS category_name
            FROM menu_items mi
            LEFT JOIN menu_categories mc ON mi.category_id = mc.id
            WHERE mi.active = 1
        ";
        if ($station !== null) {
            $sql .= " AND mc.station = :station";
        }
        $sql .= " ORDER BY mi.name";

        $stmt = $this->db->prepare($sql);
        if ($station !== null) {
            $stmt->bindValue('station', $station);
        }
        $stmt->execute();

        return array_map([MenuItem::class, 'fromRow'], $stmt->fetchAll());
    }

    public function countActiveItems(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM menu_items WHERE active = 1");
        return (int) $stmt->fetchColumn();
    }

    public function findItemById(int $id): ?MenuItem
    {
        $sql = "
            SELECT mi.*, mc.name AS category_name
            FROM menu_items mi
            LEFT JOIN menu_categories mc ON mi.category_id = mc.id
            WHERE mi.id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? MenuItem::fromRow($row) : null;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function updateItem(int $id, array $data): ?MenuItem
    {
        $stmt = $this->db->prepare("
            UPDATE menu_items
            SET name = :name, description = :description, price = :price,
                category_id = :category_id, is_combo = :is_combo
            WHERE id = :id
        ");
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'category_id' => $data['category_id'] ? (int) $data['category_id'] : null,
            'is_combo' => !empty($data['is_combo']) ? 1 : 0,
            'id' => $id,
        ]);

        return $this->findItemById($id);
    }

    public function setItemActive(int $id, bool $active): void
    {
        $stmt = $this->db->prepare("UPDATE menu_items SET active = :active WHERE id = :id");
        $stmt->execute(['active' => $active ? 1 : 0, 'id' => $id]);
    }
}

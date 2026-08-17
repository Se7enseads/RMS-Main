<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use PDO;

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
}

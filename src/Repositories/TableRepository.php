<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\RestaurantTable;
use PDO;

class TableRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAvailable(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM tables WHERE status = 'AVAILABLE' ORDER BY number"
        );

        return array_map([RestaurantTable::class, 'fromRow'], $stmt->fetchAll());
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tables WHERE status = :status");
        $stmt->execute(['status' => $status]);
        return (int) $stmt->fetchColumn();
    }

    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM tables");
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?RestaurantTable
    {
        $stmt = $this->db->prepare("SELECT * FROM tables WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? RestaurantTable::fromRow($row) : null;
    }
}

<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Role;
use PDO;

class RoleRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM roles");

        return array_map([Role::class, 'fromRow'], $stmt->fetchAll());
    }

    public function findById(int $id): ?Role
    {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Role::fromRow($row) : null;
    }

    public function findByName(string $name): ?Role
    {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE name = :name");
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch();

        return $row ? Role::fromRow($row) : null;
    }

    public function insert(string $name): Role
    {
        $stmt = $this->db->prepare("INSERT INTO roles (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);

        $id = (int)$this->db->lastInsertId();
        return new Role(id: $id, name: $name);
    }
}

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

    public function update(int $id, string $name): ?Role
    {
        $stmt = $this->db->prepare("UPDATE roles SET name = :name WHERE id = :id");
        $stmt->execute(['name' => $name, 'id' => $id]);

        return $this->findById($id);
    }

    public function setActive(int $id, bool $active): void
    {
        $stmt = $this->db->prepare("UPDATE roles SET active = :active WHERE id = :id");
        $stmt->execute(['active' => $active ? 1 : 0, 'id' => $id]);
    }
}

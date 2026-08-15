<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class PermissionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * @return array<int, string> Permission names for a role
     */
    public function findNamesByRoleId(int $roleId): array
    {
        $sql = "
            SELECT p.name
            FROM permissions p
            INNER JOIN role_permissions rp ON rp.permission_id = p.id
            WHERE rp.role_id = :role_id
            ORDER BY p.name
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['role_id' => $roleId]);

        return array_column($stmt->fetchAll(), 'name');
    }

    public function roleHasPermission(int $roleId, string $permission): bool
    {
        $sql = "
            SELECT COUNT(*)
            FROM role_permissions rp
            INNER JOIN permissions p ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id AND p.name = :name
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['role_id' => $roleId, 'name' => $permission]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function roleHasPermissionByName(string $roleName, string $permission): bool
    {
        $sql = "
            SELECT COUNT(*)
            FROM role_permissions rp
            INNER JOIN permissions p ON p.id = rp.permission_id
            INNER JOIN roles r ON r.id = rp.role_id
            WHERE r.name = :role_name AND p.name = :name
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['role_name' => $roleName, 'name' => $permission]);

        return (int) $stmt->fetchColumn() > 0;
    }
}

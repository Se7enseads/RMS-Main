<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;
use Throwable;

class PermissionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT id, name FROM permissions ORDER BY name");

        return array_map(
            fn(array $row) => ['id' => (int) $row['id'], 'name' => $row['name']],
            $stmt->fetchAll()
        );
    }

    /**
     * @return array<int, int> Permission ids for a role
     */
    public function findIdsByRoleId(int $roleId): array
    {
        $sql = "
            SELECT p.id
            FROM permissions p
            INNER JOIN role_permissions rp ON rp.permission_id = p.id
            WHERE rp.role_id = :role_id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['role_id' => $roleId]);

        return array_map('intval', array_column($stmt->fetchAll(), 'id'));
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

    /**
     * Replace the role's permission set. Nonexistent ids are ignored.
     *
     * @param array<int, int> $permissionIds
     * @throws Throwable
     */
    public function syncRolePermissions(int $roleId, array $permissionIds): void
    {
        $this->db->beginTransaction();

        try {
            $delete = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = :role_id");
            $delete->execute(['role_id' => $roleId]);

            $insert = $this->db->prepare(
                "INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)"
            );
            foreach ($permissionIds as $permissionId) {
                $insert->execute(['role_id' => $roleId, 'permission_id' => (int) $permissionId]);
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}

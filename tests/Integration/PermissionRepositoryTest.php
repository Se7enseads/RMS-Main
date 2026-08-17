<?php

namespace Tests\Integration;

use App\Core\Database;
use App\Repositories\PermissionRepository;
use PDO;

class PermissionRepositoryTest extends DatabaseTestCase
{
    private PermissionRepository $repo;
    private PDO $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new PermissionRepository();
        $this->db = Database::getConnection();
    }

    private function roleId(string $name): int
    {
        $stmt = $this->db->prepare('SELECT id FROM roles WHERE name = ?');
        $stmt->execute([$name]);
        return (int) $stmt->fetchColumn();
    }

    public function testRoleHasPermissionByNameForManager(): void
    {
        $this->assertTrue($this->repo->roleHasPermissionByName('MANAGER', 'kitchen.view'));
        $this->assertTrue($this->repo->roleHasPermissionByName('MANAGER', 'users.create'));
    }

    public function testRoleHasPermissionByNameForHeadChef(): void
    {
        $this->assertTrue($this->repo->roleHasPermissionByName('HEAD CHEF', 'kitchen.view'));
        $this->assertFalse($this->repo->roleHasPermissionByName('HEAD CHEF', 'users.view'));
    }

    public function testRoleHasPermissionByNameForWaiter(): void
    {
        $this->assertFalse($this->repo->roleHasPermissionByName('WAITER', 'kitchen.view'));
        $this->assertFalse($this->repo->roleHasPermissionByName('WAITER', 'dashboard.view'));
    }

    public function testRoleHasPermissionByNameForUnknownRole(): void
    {
        $this->assertFalse($this->repo->roleHasPermissionByName('NOPE', 'kitchen.view'));
    }

    public function testRoleHasPermissionById(): void
    {
        $this->assertTrue($this->repo->roleHasPermission($this->roleId('MANAGER'), 'dashboard.view'));
        $this->assertFalse($this->repo->roleHasPermission($this->roleId('WAITER'), 'dashboard.view'));
    }
}
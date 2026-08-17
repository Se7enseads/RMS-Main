<?php

namespace Tests\Integration;

use App\Core\Database;
use App\Services\UserService;

class UserServiceTest extends DatabaseTestCase
{
    private function waiterRoleId(): int
    {
        return (int) Database::getConnection()
            ->query("SELECT id FROM roles WHERE name = 'WAITER'")
            ->fetchColumn();
    }

    private function createPayload(array $overrides = []): array
    {
        return array_merge([
            'employee_num' => 'NEW001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'national_id' => '999999',
            'pin' => '4321',
            'password' => 'password123',
            'role_id' => $this->waiterRoleId(),
        ], $overrides);
    }

    public function testCreateUserSuccess(): void
    {
        $service = new UserService();
        $result = $service->createUser($this->createPayload());

        $this->assertTrue($result['success']);
        $this->assertSame('NEW001', $result['user']->employeeNum);
    }

    public function testCreateUserRejectsDuplicateEmployeeNum(): void
    {
        $service = new UserService();
        $result = $service->createUser($this->createPayload(['employee_num' => 'WTR001']));

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('employee_num', $result['errors']);
    }

    public function testCreateUserRejectsDuplicateNationalId(): void
    {
        $service = new UserService();
        $result = $service->createUser($this->createPayload(['national_id' => '222222']));

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('national_id', $result['errors']);
    }

    public function testCreateUserRejectsShortPassword(): void
    {
        $service = new UserService();
        $result = $service->createUser($this->createPayload(['password' => 'short']));

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('password', $result['errors']);
    }

    public function testCreateUserRejectsUnknownRole(): void
    {
        $service = new UserService();
        $result = $service->createUser($this->createPayload(['role_id' => 99]));

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('role_id', $result['errors']);
    }

    public function testUpdateUserAllowsSameEmployeeNumForSelf(): void
    {
        $wtrId = (int) Database::getConnection()
            ->query("SELECT id FROM users WHERE employee_num = 'WTR001'")
            ->fetchColumn();

        $service = new UserService();
        $result = $service->updateUser($wtrId, $this->createPayload(['employee_num' => 'WTR001', 'pin' => '1234']));

        $this->assertTrue($result['success']);
    }

    public function testUpdateUserRejectsEmployeeNumOwnedByAnother(): void
    {
        $wtrId = (int) Database::getConnection()
            ->query("SELECT id FROM users WHERE employee_num = 'WTR001'")
            ->fetchColumn();

        $service = new UserService();
        $result = $service->updateUser($wtrId, $this->createPayload(['employee_num' => 'MGR001', 'pin' => '1234']));

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('employee_num', $result['errors']);
    }

    public function testDeactivateUser(): void
    {
        $wtrId = (int) Database::getConnection()
            ->query("SELECT id FROM users WHERE employee_num = 'WTR001'")
            ->fetchColumn();

        $service = new UserService();
        $result = $service->deactivateUser($wtrId);

        $this->assertTrue($result['success']);

        $active = Database::getConnection()
            ->prepare('SELECT active FROM users WHERE id = ?');
        $active->execute([$wtrId]);
        $this->assertSame(0, (int) $active->fetchColumn());
    }

    public function testDeactivateMissingUser(): void
    {
        $service = new UserService();
        $result = $service->deactivateUser(999);

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('user', $result['errors']);
    }
}
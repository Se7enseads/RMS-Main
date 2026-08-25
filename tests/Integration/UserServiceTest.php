<?php

namespace Tests\Integration;

use App\Core\Database;
use App\Services\AuthService;
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
            'password' => 'S3cure!Passw0rdX',
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

    public function testCreateUserRejectsPasswordWithoutSpecialCharacter(): void
    {
        $service = new UserService();
        $result = $service->createUser($this->createPayload(['password' => 'abcdefghijklmnop']));

        $this->assertFalse($result['success']);
        $this->assertSame('Password must contain at least one special character.', $result['errors']['password']);
    }

    public function testCreateUserRejectsPasswordOver64Characters(): void
    {
        $service = new UserService();
        $result = $service->createUser($this->createPayload(['password' => 'Ab1!' . str_repeat('a', 62)]));

        $this->assertFalse($result['success']);
        $this->assertSame('Password must be at most 64 characters.', $result['errors']['password']);
    }

    public function testCreateUserRejectsDictionaryPassword(): void
    {
        $service = new UserService();
        $result = $service->createUser($this->createPayload(['password' => 'password123!']));

        $this->assertFalse($result['success']);
        $this->assertSame('Password is too common. Choose a stronger one.', $result['errors']['password']);
    }

    public function testCreateUserRejectsPasswordContainingName(): void
    {
        $service = new UserService();
        $result = $service->createUser($this->createPayload(['password' => 'JohnDoe!Passw0rd1']));

        $this->assertFalse($result['success']);
        $this->assertSame('Password must not contain the user\'s name or role.', $result['errors']['password']);
    }

    public function testCreateUserRejectsPasswordContainingRole(): void
    {
        $service = new UserService();
        $result = $service->createUser($this->createPayload(['password' => 'Waiter!Passw0rd12']));

        $this->assertFalse($result['success']);
        $this->assertSame('Password must not contain the user\'s name or role.', $result['errors']['password']);
    }

    public function testCreateUserRejectsUnknownRole(): void
    {
        $service = new UserService();
        $result = $service->createUser($this->createPayload(['role_id' => 99]));

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('role_id', $result['errors']);
    }

    public function testUpdateUserKeepsEmployeeNumAndNationalId(): void
    {
        $wtrId = (int) Database::getConnection()
            ->query("SELECT id FROM staff WHERE employee_num = 'WTR001'")
            ->fetchColumn();

        $service = new UserService();
        $result = $service->updateUser($wtrId, [
            'first_name' => 'Brian',
            'last_name' => 'Otieno',
            'pin' => '1234',
            'password' => 'S3cure!Passw0rdX',
            'role_id' => $this->waiterRoleId(),
        ]);

        $this->assertTrue($result['success']);

        $row = Database::getConnection()
            ->query("SELECT employee_num, national_id FROM staff WHERE id = $wtrId")
            ->fetch();
        $this->assertSame('WTR001', $row['employee_num']);
        $this->assertSame('222222', $row['national_id']);
    }

    public function testUpdateUserChangesPassword(): void
    {
        $wtrId = (int) Database::getConnection()
            ->query("SELECT id FROM staff WHERE employee_num = 'WTR001'")
            ->fetchColumn();

        $service = new UserService();
        $result = $service->updateUser($wtrId, [
            'first_name' => 'Brian',
            'last_name' => 'Otieno',
            'pin' => '1234',
            'password' => 'N3w!SecurePassw0rd',
            'role_id' => $this->waiterRoleId(),
        ]);

        $this->assertTrue($result['success']);

        $auth = new AuthService();
        $login = $auth->loginWithPassword('WTR001', 'N3w!SecurePassw0rd');
        $this->assertTrue($login['success']);
        $this->assertSame('WTR001', $login['user']->employeeNum);
    }

    public function testUpdateUserRejectsPasswordContainingCurrentName(): void
    {
        $wtrId = (int) Database::getConnection()
            ->query("SELECT id FROM staff WHERE employee_num = 'WTR001'")
            ->fetchColumn();

        $service = new UserService();
        $result = $service->updateUser($wtrId, [
            'first_name' => 'Brian',
            'last_name' => 'Otieno',
            'pin' => '1234',
            'password' => 'Brian!Passw0rd123',
            'role_id' => $this->waiterRoleId(),
        ]);

        $this->assertFalse($result['success']);
        $this->assertSame('Password must not contain the user\'s name or role.', $result['errors']['password']);
    }

    public function testDeactivateUser(): void
    {
        $wtrId = (int) Database::getConnection()
            ->query("SELECT id FROM staff WHERE employee_num = 'WTR001'")
            ->fetchColumn();

        $service = new UserService();
        $result = $service->deactivateUser($wtrId);

        $this->assertTrue($result['success']);

        $active = Database::getConnection()
            ->prepare('SELECT active FROM staff WHERE id = ?');
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
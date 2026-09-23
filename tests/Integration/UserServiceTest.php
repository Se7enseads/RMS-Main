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
        $this->assertSame('WT002', $result['user']->employeeNum);
    }

    public function testCreateUserAutoGeneratesSequentialNumbersPerRole(): void
    {
        $service = new UserService();

        $first = $service->createUser($this->createPayload());
        $second = $service->createUser($this->createPayload(['national_id' => '999998', 'pin' => '4322']));

        $this->assertTrue($first['success']);
        $this->assertTrue($second['success']);
        $this->assertSame('WT002', $first['user']->employeeNum);
        $this->assertSame('WT003', $second['user']->employeeNum);
    }

    public function testCreateUserGeneratesRoleSpecificPrefixes(): void
    {
        $service = new UserService();

        $roles = [
            'MANAGER' => 'MR',
            'WAITER' => 'WT',
            'HEAD CHEF' => 'HC',
            'BARTENDER' => 'BR',
            'CASHIER' => 'CS',
        ];

        foreach ($roles as $roleName => $prefix) {
            $roleId = (int) Database::getConnection()
                ->query("SELECT id FROM roles WHERE name = '$roleName'")
                ->fetchColumn();
            $nationalId = '911' . str_pad((string) $roleId, 3, '0', STR_PAD_LEFT);
            $pin = str_pad((string) $roleId, 4, '0', STR_PAD_LEFT);

            $result = $service->createUser($this->createPayload([
                'role_id' => $roleId,
                'national_id' => $nationalId,
                'pin' => $pin,
            ]));

            $this->assertTrue($result['success']);
            $this->assertMatchesRegularExpression('/^' . $prefix . '00[2-9]$/', $result['user']->employeeNum);
        }
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
            ->query("SELECT id FROM staff WHERE employee_num = 'WT001'")
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
        $this->assertSame('WT001', $row['employee_num']);
        $this->assertSame('222222', $row['national_id']);
    }

    public function testUpdateUserChangesPassword(): void
    {
        $wtrId = (int) Database::getConnection()
            ->query("SELECT id FROM staff WHERE employee_num = 'WT001'")
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
        $login = $auth->loginWithPassword('WT001', 'N3w!SecurePassw0rd');
        $this->assertTrue($login['success']);
        $this->assertSame('WT001', $login['user']->employeeNum);
    }

    public function testUpdateUserRejectsPasswordContainingCurrentName(): void
    {
        $wtrId = (int) Database::getConnection()
            ->query("SELECT id FROM staff WHERE employee_num = 'WT001'")
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
            ->query("SELECT id FROM staff WHERE employee_num = 'WT001'")
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
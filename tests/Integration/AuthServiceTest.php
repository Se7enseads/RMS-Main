<?php

namespace Tests\Integration;

use App\Core\Database;
use App\Core\Session;
use App\Services\AuthService;

class AuthServiceTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Session::destroy();
    }

    protected function tearDown(): void
    {
        Session::destroy();
    }

    public function testLoginWithPasswordSuccess(): void
    {
        $service = new AuthService();
        $result = $service->loginWithPassword('MR001', 'manager123');

        $this->assertTrue($result['success']);
        $this->assertSame('MANAGER', Session::get('role_name'));
    }

    public function testLoginWithPasswordRejectsWrongPassword(): void
    {
        $service = new AuthService();
        $result = $service->loginWithPassword('MR001', 'wrongpass');

        $this->assertFalse($result['success']);
        $this->assertNull(Session::get('user_id'));
    }

    public function testLoginWithPasswordRejectsInactiveUser(): void
    {
        $pdo = Database::getConnection();
        $pdo->exec("UPDATE staff SET active = 0 WHERE employee_num = 'MR001'");

        $service = new AuthService();
        $result = $service->loginWithPassword('MR001', 'manager123');

        $this->assertFalse($result['success']);
    }

    public function testLoginWithPasswordRejectsUnknownUser(): void
    {
        $service = new AuthService();
        $result = $service->loginWithPassword('NOPE', 'secret123');

        $this->assertFalse($result['success']);
        $this->assertSame('Invalid credentials.', $result['error']);
    }

    public function testLoginWithPinSuccess(): void
    {
        $service = new AuthService();
        $result = $service->loginWithPin('1234');

        $this->assertTrue($result['success']);
        $this->assertSame('WAITER', Session::get('role_name'));
    }

    public function testLoginWithPinRejectsInvalidPin(): void
    {
        $service = new AuthService();
        $result = $service->loginWithPin('9999');

        $this->assertFalse($result['success']);
        $this->assertSame('Invalid PIN.', $result['error']);
    }

    public function testLogoutClearsSession(): void
    {
        $service = new AuthService();
        $service->loginWithPin('1234');
        $this->assertTrue(Session::has('user_id'));

        $service->logout();
        $this->assertFalse(Session::has('user_id'));
    }
}
<?php

namespace Tests\Integration;

use App\Services\EmployeeNumberGenerator;

class EmployeeNumberGeneratorTest extends DatabaseTestCase
{
    public function testNextForRoleFollowsLastAddedUser(): void
    {
        $generator = new EmployeeNumberGenerator();

        $this->assertSame('MR002', $generator->nextForRole('MANAGER'));
        $this->assertSame('WT002', $generator->nextForRole('WAITER'));
        $this->assertSame('HC002', $generator->nextForRole('HEAD CHEF'));
        $this->assertSame('BR002', $generator->nextForRole('BARTENDER'));
        $this->assertSame('CS002', $generator->nextForRole('CASHIER'));
    }

    public function testNextForRoleGrowsPastThreeDigits(): void
    {
        $db = \App\Core\Database::getConnection();
        $db->exec("INSERT INTO staff (employee_num, first_name, last_name, national_id, pin, role_id, active)
                   VALUES ('WT999', 'Test', 'User', 'NTEST1', '1357', 2, 1)");

        $this->assertSame('WT1000', (new EmployeeNumberGenerator())->nextForRole('WAITER'));
    }

    public function testCustomRoleFallsBackToFirstTwoLetters(): void
    {
        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare('INSERT INTO roles (name) VALUES (?)');
        $stmt->execute(['MOBILE VENDOR']);
        $roleId = (int) $db->lastInsertId();

        $this->assertSame('MO001', (new EmployeeNumberGenerator())->nextForRole('MOBILE VENDOR'));
    }
}
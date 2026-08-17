<?php

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testFromRowMapsAllFields(): void
    {
        $user = User::fromRow([
            'id' => '4',
            'employee_num' => 'WTR001',
            'first_name' => 'Brian',
            'middle_name' => 'O.',
            'last_name' => 'Otieno',
            'national_id' => '222222',
            'pin' => '1234',
            'pin_hash' => null,
            'password_hash' => 'hash',
            'phone_number' => '0700000000',
            'role_id' => '2',
            'active' => 1,
            'created_at' => '2026-01-01 00:00:00',
            'updated_at' => '2026-01-01 00:00:00',
            'role_name' => 'WAITER',
        ]);

        $this->assertSame(4, $user->id);
        $this->assertSame('WTR001', $user->employeeNum);
        $this->assertSame('WAITER', $user->roleName);
        $this->assertTrue($user->active);
        $this->assertSame('O.', $user->middleName);
    }

    public function testGetFullNameWithMiddleName(): void
    {
        $user = User::fromRow([
            'id' => 1, 'employee_num' => 'X', 'first_name' => 'Brian',
            'middle_name' => 'O.', 'last_name' => 'Otieno', 'national_id' => '1',
            'pin' => '1', 'role_id' => 1, 'active' => 1,
        ]);

        $this->assertSame('Brian O. Otieno', $user->getFullName());
    }

    public function testGetFullNameWithoutMiddleName(): void
    {
        $user = User::fromRow([
            'id' => 1, 'employee_num' => 'X', 'first_name' => 'Brian',
            'middle_name' => null, 'last_name' => 'Otieno', 'national_id' => '1',
            'pin' => '1', 'role_id' => 1, 'active' => 1,
        ]);

        $this->assertSame('Brian Otieno', $user->getFullName());
    }
}
<?php

namespace Tests\Integration;

use App\Repositories\UserRepository;

class UserRepositoryTest extends DatabaseTestCase
{
    private UserRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new UserRepository();
    }

    public function testFindByEmployeeNum(): void
    {
        $user = $this->repo->findByEmployeeNum('MR001');

        $this->assertNotNull($user);
        $this->assertSame('MANAGER', $user->roleName);
    }

    public function testFindByNationalId(): void
    {
        $user = $this->repo->findByNationalId('222222');

        $this->assertNotNull($user);
        $this->assertSame('WT001', $user->employeeNum);
    }

    public function testFindByNationalIdReturnsNullWhenMissing(): void
    {
        $this->assertNull($this->repo->findByNationalId('NOPE99'));
    }

    public function testFindByPin(): void
    {
        $user = $this->repo->findByPin('5678');

        $this->assertNotNull($user);
        $this->assertSame('HEAD CHEF', $user->roleName);
    }

    public function testFindByPinReturnsNullWhenMissing(): void
    {
        $this->assertNull($this->repo->findByPin('9999'));
    }

    public function testDeactivateSetsInactive(): void
    {
        $this->repo->deactivate(2);

        $user = $this->repo->findById(2);
        $this->assertNotNull($user);
        $this->assertFalse($user->active);
    }
}
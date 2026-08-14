<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\RoleRepository;

class RoleService
{
    private RoleRepository $roleRepository;

    public function __construct()
    {
        $this->roleRepository = new RoleRepository();
    }

    public function getAllRoles(): array
    {
        return $this->roleRepository->findAll();
    }

    public function getRoleById(int $id): ?Role
    {
        return $this->roleRepository->findById($id);
    }

    /**
     * @return array<string,mixed>
     */
    public function createRole(string $name): array
    {
        $name = trim($name);

        if ($name === '') {
            return ['success' => false, 'errors' => ['name' => 'Role name is required.']];
        }

        if ($this->roleRepository->findByName($name)) {
            return ['success' => false, 'errors' => ['name' => 'Role already exists.']];
        }

        $role = $this->roleRepository->insert($name);
        return ['success' => true, 'role' => $role];
    }
}

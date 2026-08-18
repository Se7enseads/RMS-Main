<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;

class RoleService
{
    private RoleRepository $roleRepository;
    private PermissionRepository $permissionRepository;

    public function __construct()
    {
        $this->roleRepository = new RoleRepository();
        $this->permissionRepository = new PermissionRepository();
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
     * @return array<int, array{id: int, name: string}>
     */
    public function getAllPermissions(): array
    {
        return $this->permissionRepository->findAll();
    }

    /**
     * @return array<int, int>
     */
    public function getPermissionIdsByRoleId(int $roleId): array
    {
        return $this->permissionRepository->findIdsByRoleId($roleId);
    }

    /**
     * @param array<int, int> $permissionIds
     * @return array<string,mixed>
     */
    public function createRole(string $name, array $permissionIds = []): array
    {
        $name = trim($name);

        if ($name === '') {
            return ['success' => false, 'errors' => ['name' => 'Role name is required.']];
        }

        if ($this->roleRepository->findByName($name)) {
            return ['success' => false, 'errors' => ['name' => 'Role already exists.']];
        }

        $role = $this->roleRepository->insert($name);
        $this->permissionRepository->syncRolePermissions($role->id, $permissionIds);

        return ['success' => true, 'role' => $role];
    }

    /**
     * @param array<int, int> $permissionIds
     * @return array<string,mixed>
     */
    public function updateRole(int $id, string $name, array $permissionIds = []): array
    {
        $name = trim($name);

        if ($name === '') {
            return ['success' => false, 'errors' => ['name' => 'Role name is required.']];
        }

        $existing = $this->roleRepository->findByName($name);
        if ($existing && $existing->id !== $id) {
            return ['success' => false, 'errors' => ['name' => 'Role already exists.']];
        }

        $role = $this->roleRepository->update($id, $name);
        $this->permissionRepository->syncRolePermissions($id, $permissionIds);

        return ['success' => true, 'role' => $role];
    }

    public function setRoleActive(int $id, bool $active): void
    {
        $this->roleRepository->setActive($id, $active);
    }
}
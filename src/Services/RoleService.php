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
}
